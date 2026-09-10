<?php
// /src/app/controllers/procesarReporte.php
// Procesa el reporte de pago enviado desde el formulario

use App\Sistema\models\PagoOnlineModel;
use App\Sistema\models\PedidoModel;
use App\Sistema\models\BancoReceptorModel;
use App\Sistema\models\TasaCambioModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar buffers
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // =============================================
    // 0. VALIDAR CARRITO
    // =============================================
    if (empty($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No hay productos en el carrito. Agrega productos antes de reportar un pago.',
            'redirect' => '?pagina=web_Catalogo'
        ]);
        exit;
    }

    // =============================================
    // 1. RECIBIR DATOS
    // =============================================
    $id_pedido = intval($_POST['id_pedido'] ?? 0);
    $referencia = trim($_POST['referencia'] ?? '');
    $banco_emisor = trim($_POST['banco_emisor'] ?? '');
    $telefono_raw = trim($_POST['telefono'] ?? '');
    $monto_raw = trim($_POST['monto'] ?? '0');
    $nombre_pagador = trim($_POST['nombre_pagador'] ?? ($_SESSION['cliente_nombre'] ?? ''));
    $cedula_pagador = trim($_POST['cedula_pagador'] ?? '');
    $metodo = trim($_POST['metodo'] ?? 'transferencia');

    // =============================================
    // BANCO RECEPTOR: se resuelve en servidor desde constantes (BancoReceptorModel).
    // NO se confía en el campo oculto del formulario (evita manipulación).
    // =============================================
    $receptorModel = new BancoReceptorModel();
    $banco_receptor = $receptorModel->obtenerNombre($metodo)
        ?? trim($_POST['banco_receptor'] ?? '')
        ?? 'Banesco';

    // =============================================
    // 2. LIMPIAR TELÉFONO (SOLO NÚMEROS)
    // =============================================
    $telefono_limpio = preg_replace('/[^0-9]/', '', $telefono_raw);
    
    if (empty($telefono_limpio)) {
        echo json_encode(['success' => false, 'message' => 'El teléfono es requerido']);
        exit;
    }
    
    if (strlen($telefono_limpio) < 10) {
        echo json_encode(['success' => false, 'message' => 'El teléfono debe tener al menos 10 dígitos']);
        exit;
    }
    
    if (strlen($telefono_limpio) > 11) {
        echo json_encode(['success' => false, 'message' => 'El teléfono no puede tener más de 11 dígitos']);
        exit;
    }

    // =============================================
    // 3. LIMPIAR MONTO (SOPORTA INGLÉS Y ESPAÑOL)
    // =============================================
    // Regla: el ÚLTIMO separador es el decimal.
    //   "1,340.23" (inglés, como muestra number_format) → 1340.23
    //   "1.340,23" (español)                            → 1340.23
    //   "1340.23" / "1340,23" / "1340"                  → OK
    $posPuntoUltimo = strrpos($monto_raw, '.');
    $posComaUltima  = strrpos($monto_raw, ',');
    $posPuntoUltimo = ($posPuntoUltimo === false) ? -1 : $posPuntoUltimo;
    $posComaUltima  = ($posComaUltima === false) ? -1 : $posComaUltima;

    if ($posComaUltima > $posPuntoUltimo) {
        // La coma es el decimal ("1.340,23"): quitar puntos (miles), coma → punto
        $monto_str = str_replace('.', '', $monto_raw);
        $monto = floatval(str_replace(',', '.', $monto_str));
    } else {
        // El punto es el decimal ("1,340.23") o no hay separador: las comas son miles
        $monto = floatval(str_replace(',', '', $monto_raw));
    }

    // =============================================
    // 4. VALIDACIONES
    // =============================================
    
    if ($id_pedido <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
        exit;
    }
    
    if (empty($referencia) || strlen($referencia) < 4) {
        echo json_encode(['success' => false, 'message' => 'La referencia debe tener al menos 4 caracteres']);
        exit;
    }
    
    if (empty($banco_emisor) || strlen($banco_emisor) < 3) {
        echo json_encode(['success' => false, 'message' => 'El banco emisor es requerido']);
        exit;
    }
    
    if ($monto <= 0) {
        echo json_encode(['success' => false, 'message' => 'El monto debe ser mayor a 0']);
        exit;
    }
    
    $cedula_persona = $_SESSION['cliente_cedula'] ?? '';
    if (empty($cedula_persona)) {
        error_log("❌ procesarReporte - Sesión sin cedula_persona");
        echo json_encode(['success' => false, 'message' => 'Sesión no válida. Inicia sesión nuevamente']);
        exit;
    }

    // =============================================
    // 5. VERIFICAR PEDIDO
    // =============================================
    $pedidoModel = new PedidoModel();
    $pedido = $pedidoModel->obtenerPorIdYCliente($id_pedido, $cedula_persona);
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado o no te pertenece']);
        exit;
    }

    // Validar que el monto reportado (en Bs.) cubra el total del pedido (en USD)
    $tasaModel = new TasaCambioModel();
    $tasaActual = $tasaModel->obtener();
    $pedidoTotal = floatval($pedido['total']);

    if ($tasaActual <= 0) {
        echo json_encode(['success' => false, 'message' => 'La tasa de cambio no está disponible. Contacte a soporte.']);
        exit;
    }

    // El monto lo escribe el cliente en Bolívares → convertir a USD
    $montoUsd = $monto / $tasaActual;

    if ($montoUsd < ($pedidoTotal - 0.01)) {
        echo json_encode([
            'success' => false,
            'message' => 'El monto transferido (Bs. ' . number_format($monto, 2) . ' ≈ $' . number_format($montoUsd, 2) . ' a la tasa ' . number_format($tasaActual, 2) . ') no cubre el total del pedido ($' . number_format($pedidoTotal, 2) . '). Asegúrate de transferir el monto completo.'
        ]);
        exit;
    }

    // =============================================
    // 6. GUARDAR PAGO (CON TRANSA + BLOQUEO)
    // =============================================
    $pagoModel = new PagoOnlineModel();
    $conn = $pagoModel->getConexion();

    try {
        $conn->beginTransaction();

        // Bloquear el pedido para evitar condiciones de carrera
        $stmtLock = $conn->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ? FOR UPDATE");
        $stmtLock->execute([$id_pedido]);

        // Verificar que no exista otro pago (DENTRO del bloqueo)
        $existe = $pagoModel->ejecutar('existe_por_pedido', ['id_pedido' => $id_pedido]);
        if ($existe === true) {
            $conn->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => 'Ya existe un reporte de pago para este pedido',
                'redirect' => '?pagina=misPedidos'
            ]);
            exit;
        }

        $resultado = $pagoModel->ejecutar('crear', [
            'id_pedido' => $id_pedido,
            'cedula_persona' => $cedula_persona,
            'referencia' => $referencia,
            'banco_emisor' => $banco_emisor,
            'banco_receptor' => $banco_receptor,
            'telefono' => $telefono_limpio,
            'monto' => $monto,
            'fecha_transferencia' => date('Y-m-d'),
            'nombre_pagador' => $nombre_pagador,
            'cedula_pagador' => $cedula_pagador,
            'comprobante' => null
        ]);

        if (isset($resultado['error'])) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $resultado['error']]);
        } else {
            $conn->commit();
            echo json_encode([
                'success' => true, 
                'message' => '¡Pago reportado exitosamente! Tu pedido está en revisión.',
                'redirect' => '?pagina=misPedidos'
            ]);
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        error_log("Error en transacción procesarReporte: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
    }

} catch (Exception $e) {
    error_log("Error en procesarReporte: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
}