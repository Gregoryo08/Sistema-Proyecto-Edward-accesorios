<?php

use App\Sistema\models\ClienteEcommerce;
use App\Sistema\models\EcommerceModel;
use App\Sistema\models\Vista;
use App\Sistema\models\TasaCambioModel;
use App\Sistema\Models\Usuarios;
use App\Sistema\models\bitacora;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$clienteEcommerce = new ClienteEcommerce();
$modelo = new EcommerceModel();
$tasaCambioModel = new TasaCambioModel();
$obj_usuario = new Usuarios();
$bitacora = new bitacora();
$vistas = new Vista(__DIR__ . '/../views');
$vistaParciales = new Vista(__DIR__ . '/../views/partials');

$action = $_GET['action'] ?? $_GET['pagina'] ?? 'index';

function responderJson($respuesta)
{
    echo json_encode($respuesta);
    exit;
}

function exigirSesionTienda()
{
    if (isset($_SESSION['es_ecommerce']) && $_SESSION['es_ecommerce'] === true) {
        return true;
    }

    header('Location: ?pagina=login_principal');
    exit;
}

function resolverTasaPedido($modelo, $pedido, $id_pedido, $tasaCambioModel)
{
    $tasa = $tasaCambioModel->obtener();
    $total_bs = $tasaCambioModel->convertir($pedido['total']);
    $vigencia = $tasaCambioModel->vigencia();
    $fecha_tasa = $tasaCambioModel->obtenerFechaActualizacion();

    if (!empty($pedido['total_bs']) && (float) $pedido['total_bs'] > 0 && !empty($pedido['tasa_bs']) && (float) $pedido['tasa_bs'] > 0) {
        $tasa = (float) $pedido['tasa_bs'];
        $total_bs = (float) $pedido['total_bs'];
        $vigencia = ['expirada' => false, 'minutos' => 0, 'horas' => 0, 'mensaje' => 'Tasa del pedido'];
        $fecha_tasa = 'Tasa del pedido';
    } else {
        $modelo->PedidoactualizarTotalBs($id_pedido, $total_bs, $tasa);
    }

    return [$tasa, $total_bs, $vigencia, $fecha_tasa];
}

function validarReportePago($id_pedido, $referencia, $banco_emisor, $telefono, $monto)
{
    if ($id_pedido <= 0) {
        return 'ID de pedido inválido';
    }

    if (strlen($referencia) < 4) {
        return 'La referencia debe tener al menos 4 caracteres';
    }

    if (strlen($banco_emisor) < 3) {
        return 'El banco emisor es requerido';
    }

    if ($telefono === '') {
        return 'El teléfono es requerido';
    }

    if (strlen($telefono) < 10) {
        return 'El teléfono debe tener al menos 10 dígitos';
    }

    if (strlen($telefono) > 11) {
        return 'El teléfono no puede tener más de 11 dígitos';
    }

    if ($monto <= 0) {
        return 'El monto debe ser mayor a 0';
    }

    return null;
}

function normalizarMonto($montoRaw)
{
    if (strpos($montoRaw, ',') !== false) {
        return floatval(str_replace(',', '.', str_replace('.', '', $montoRaw)));
    }

    return floatval($montoRaw);
}

function responderCarritoVacio($vistas)
{
    $vistas->mostrar('carritoVacio.php');
    exit;
}

switch ($action) {
    case 'index':
    case 'catalogo':
    case 'webcatalogo':
    case 'web_catalogo':
        $mostrarOpcionesCliente = false;
        $nombreCliente = '';

        if (isset($_SESSION['cliente_cedula']) && isset($_SESSION['cliente_nombre'])) {
            $mostrarOpcionesCliente = true;
            $nombreCliente = $_SESSION['cliente_nombre'];
        }

        $productos = $modelo->ejecutar('producto.obtenerActivos');

        if (empty($productos)) {
            error_log('catalogo: no se encontraron productos');
            $productos = [];
        }

        $categorias = $modelo->ejecutar('producto.obtenerCategorias');

        if (!is_array($categorias)) {
            $categorias = [];
        }

        $vistas->mostrar('catalogo.php', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
        break;

    case 'carrito':
        $vistas->mostrar('carrito.php');
        break;

    case 'verificarsesion':
        $logueado = $clienteEcommerce->ejecutar('estaLogueado');
        header('Content-Type: application/json');
        echo json_encode(['logueado' => $logueado]);
        exit;

    case 'guardardestino':
        $_SESSION['destino_after_login'] = $_GET['destino'] ?? 'checkout';
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;

    case 'checkout':
        try {
            if (!$clienteEcommerce->ejecutar('estaLogueado')) {
                $_SESSION['destino_after_login'] = 'checkout';
                header('Location: ?pagina=login_principal&destino=checkout');
                exit;
            }

            $cliente_cedula = $_SESSION['cliente_cedula'] ?? '';
            $cliente_nombre = $_SESSION['cliente_nombre'] ?? '';
            $cliente_correo = $_SESSION['cliente_correo'] ?? '';
            $cliente_telefono = $_SESSION['cliente_telefono'] ?? '';

            if (empty($cliente_nombre)) {
                $datos = $clienteEcommerce->ejecutar('obtenerPorCedula', ['cedula' => $cliente_cedula]);
                if ($datos) {
                    $cliente_nombre = trim(($datos['nombre'] ?? '') . ' ' . ($datos['apellido'] ?? ''));
                    $cliente_correo = $datos['correo'] ?? '';
                    $cliente_telefono = $datos['telefono'] ?? '';

                    $_SESSION['cliente_nombre'] = $cliente_nombre;
                    $_SESSION['cliente_correo'] = $cliente_correo;
                    $_SESSION['cliente_telefono'] = $cliente_telefono;
                }
            }

            $cedula_persona_checkout = $cliente_cedula;

            $vistas->mostrar('checkout.php', [
                'cliente_nombre' => $cliente_nombre,
                'cliente_correo' => $cliente_correo,
                'cliente_telefono' => $cliente_telefono,
                'cedula_persona_checkout' => $cedula_persona_checkout,
            ]);
        } catch (\Exception $e) {
            error_log("Error en checkout: " . $e->getMessage());
            header('Location: ?pagina=web_Catalogo');
            exit;
        }
        break;

    case 'guardarpedido':
        header('Content-Type: application/json');

        try {
            if (!$clienteEcommerce->ejecutar('estaLogueado')) {
                echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
                exit;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['carrito'])) {
                echo json_encode(['success' => false, 'error' => 'Carrito vacío']);
                exit;
            }

            $cedula = $_SESSION['cliente_cedula'];
            $nombre = $_SESSION['cliente_nombre'] ?? 'Cliente';
            $telefono = $_SESSION['cliente_telefono'] ?? '';
            $email = $_SESSION['cliente_correo'] ?? '';

            $carrito = $data['carrito'];
            $_SESSION['carrito'] = $carrito;
            $total = floatval($data['total'] ?? 0);
            $metodo_pago = $data['metodo_pago'] ?? 'transferencia';
            $banco_emisor = $data['banco_emisor'] ?? null;
            $tipo_entrega = $data['entrega'] ?? 'retiro';
            $direccion_entrega = $data['direccion'] ?? null;

            if ($total <= 0) {
                echo json_encode(['success' => false, 'error' => 'Total inválido']);
                exit;
            }
            if ($tipo_entrega === 'delivery' && empty($direccion_entrega)) {
                echo json_encode(['success' => false, 'error' => 'Dirección de entrega requerida']);
                exit;
            }

            $subtotal = 0;
            foreach ($carrito as $item) {
                $producto = $modelo->ejecutar('producto.obtenerPorId', ['id' => $item['id']]);
                if (!$producto) {
                    echo json_encode(['success' => false, 'error' => 'Producto no encontrado: ' . $item['id']]);
                    exit;
                }
                $precioEsperado = floatval($producto['precio_detal']);
                $precioRecibido = floatval($item['precio']);
                if (abs($precioEsperado - $precioRecibido) > 0.01) {
                    echo json_encode(['success' => false, 'error' => 'Precio incorrecto para: ' . $producto['nombre_producto']]);
                    exit;
                }
                $cantidad = intval($item['cantidad']);
                if ($cantidad < 1) {
                    echo json_encode(['success' => false, 'error' => 'Cantidad inválida para: ' . $producto['nombre_producto']]);
                    exit;
                }
                $subtotal += $precioEsperado * $cantidad;
            }

            if ($tipo_entrega === 'delivery') {
                $costoEnvio = $subtotal >= 100 ? 0 : 10;
            } else {
                $costoEnvio = 0;
            }
            $totalEsperado = $subtotal + $costoEnvio;
            if (abs($total - $totalEsperado) > 0.01) {
                echo json_encode(['success' => false, 'error' => 'El total no coincide con los productos' . ' (esperado: ' . number_format($totalEsperado, 2) . ')']);
                exit;
            }

            $modelo->PedidoiniciarTransaccion();

            $id_pedido = $modelo->Pedidocrear(
                $cedula,
                $nombre,
                $telefono,
                $email,
                $subtotal,
                $costoEnvio,
                $totalEsperado,
                $metodo_pago
            );

            if (!$id_pedido) {
                throw new \Exception('Error al crear el pedido');
            }

            $tasaOrden = $tasaCambioModel->obtener();
            if ($tasaOrden > 0) {
                $modelo->PedidoactualizarTotalBs($id_pedido, round($totalEsperado * $tasaOrden, 2), $tasaOrden);
            }

            if ($tipo_entrega === 'delivery' && !empty($direccion_entrega)) {
                $modelo->PedidoactualizarDireccionEntrega($id_pedido, $direccion_entrega);
            }

            foreach ($carrito as $item) {
                $modelo->PedidoagregarDetalle(
                    $id_pedido,
                    $item['id'],
                    $item['cantidad'],
                    $item['precio'],
                    $item['precio'] * $item['cantidad']
                );
            }

            $datosPago = [
                'metodo' => $metodo_pago,
                'banco_emisor' => $banco_emisor,
                'tipo_entrega' => $tipo_entrega,
                'fecha' => date('Y-m-d H:i:s')
            ];
            $modelo->PedidoactualizarDatosPago($id_pedido, json_encode($datosPago));

            $modelo->PedidoconfirmarTransaccion();

            echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);
        } catch (\Exception $e) {
            if (isset($modelo)) {
                $modelo->PedidorevertirTransaccion();
            }
            error_log("Error en guardarPedido: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;

    case 'listarbancos':
        $cedula = $_SESSION['cliente_cedula'] ?? $_SESSION['username'] ?? null;

        header('Content-Type: application/json');

        if (empty($cedula)) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            exit;
        }

        try {
            $forzar = (isset($_GET['forzar']) && $_GET['forzar'] === '1');

            if ($forzar) {
                $bancos = $modelo->BancoobtenerActivos(true);
                $fuente = 'bd_api';
            } else {
                $bancos = $modelo->BancoobtenerActivosSoloBd();
                $fuente = 'bd';
            }

            echo json_encode([
                'success' => true,
                'bancos' => $bancos,
                'fuente' => $fuente
            ]);
        } catch (\Exception $e) {
            error_log("Error en listarBancos: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }

        exit;

    case 'mispedidos':
        if (!exigirSesionTienda()) {
            break;
        }

        try {
            $pedidos = $modelo->PedidoobtenerPorCliente($_SESSION['cliente_cedula']);
        } catch (\Exception $e) {
            $pedidos = [];
            error_log("Error en misPedidos: " . $e->getMessage());
        }

        $vistas->mostrar('misPedidos.php', ['modelo' => $modelo, 'pedidos' => $pedidos]);
        break;

    case 'verpedido':
        if (!exigirSesionTienda()) {
            break;
        }

        $id_pedido = intval($_GET['id'] ?? 0);

        if ($id_pedido <= 0) {
            header('Location: ?pagina=misPedidos');
            exit;
        }

        try {
            $cedula = $_SESSION['cliente_cedula'];
            $pedido = $modelo->PedidoobtenerPorId($id_pedido);

            if (!$pedido || ($pedido['cedula_persona'] ?? '') !== $cedula) {
                header('Location: ?pagina=misPedidos');
                exit;
            }

            $detalle = $modelo->PedidoobtenerDetalle($id_pedido);
        } catch (\Exception $e) {
            $pedido = null;
            $detalle = [];
            error_log("Error en verPedido: " . $e->getMessage());
        }

        if (!$pedido) {
            header('Location: ?pagina=misPedidos');
            exit;
        }

        $vistas->mostrar('verPedido.php', [
            'modelo'  => $modelo,
            'pedido'  => $pedido,
            'detalle' => $detalle,
        ]);
        break;

    case 'tasaecommerce':
        header('Content-Type: application/json');

        $accionUrl = $_GET['action'] ?? '';
        $accionUrl = is_string($accionUrl) ? $accionUrl : '';

        if ($accionUrl === 'obtener') {
            $tasa = $tasaCambioModel->obtener();
            echo json_encode([
                'success' => true,
                'tasa' => $tasa,
                'tasa_formateada' => number_format($tasa, 2),
                'vigencia' => $tasaCambioModel->vigencia(),
                'fecha' => $tasaCambioModel->obtenerFechaActualizacion(),
                'fuente' => $tasaCambioModel->obtenerFuente()
            ]);
            exit;
        }

        if (!isset($_SESSION['es_ecommerce']) || $_SESSION['es_ecommerce'] !== true) {
            echo json_encode(['success' => false, 'message' => 'No autorizado: debe iniciar sesión como cliente']);
            exit;
        }

        $tasaNueva = $tasaCambioModel->actualizarDesdeFuentes();

        if ($tasaNueva === null || $tasaNueva <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo obtener la tasa automáticamente. Un administrador debe actualizarla.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'tasa' => $tasaNueva,
            'tasa_formateada' => number_format($tasaNueva, 2),
            'vigencia' => $tasaCambioModel->vigencia(),
            'fecha' => $tasaCambioModel->obtenerFechaActualizacion(),
            'fuente' => $tasaCambioModel->obtenerFuente()
        ]);
        exit;

    case 'reportarpago':
        if (!exigirSesionTienda()) {
            break;
        }

        if (empty($_SESSION['carrito'])) {
            responderCarritoVacio($vistas);
        }

        $id_pedido = $_GET['pedido'] ?? 0;
        $metodo = $_GET['metodo'] ?? 'transferencia';

        $pedido = $modelo->PedidoobtenerPorIdYCliente($id_pedido, $_SESSION['cliente_cedula'] ?? '');
        $total_usd = $pedido['total'] ?? 0;

        list($tasa, $total_bs, $vigencia, $fecha_tasa) = resolverTasaPedido($modelo, $pedido, $id_pedido, $tasaCambioModel);

        $banco_receptor = $modelo->ReceptorobtenerNombre($metodo) ?? 'Banesco';

        $vistas->mostrar('reportarPago.php', [
            'id_pedido'      => $id_pedido,
            'metodo'        => $metodo,
            'total_usd'     => $total_usd,
            'tasa'          => $tasa,
            'total_bs'      => $total_bs,
            'vigencia'      => $vigencia,
            'fecha_tasa'    => $fecha_tasa,
            'banco_receptor' => $banco_receptor,
        ]);
        break;

    case 'procesarreporte':
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');

        try {
            if (empty($_SESSION['carrito']) || count($_SESSION['carrito']) === 0) {
                responderJson([
                    'success' => false,
                    'message' => 'No hay productos en el carrito. Agrega productos antes de reportar un pago.',
                    'redirect' => '?pagina=web_Catalogo'
                ]);
            }

            $id_pedido = intval($_POST['id_pedido'] ?? 0);
            $referencia = trim($_POST['referencia'] ?? '');
            $banco_emisor = trim($_POST['banco_emisor'] ?? '');
            $telefono_limpio = preg_replace('/[^0-9]/', '', trim($_POST['telefono'] ?? ''));
            $monto = normalizarMonto(trim($_POST['monto'] ?? '0'));
            $nombre_pagador = trim($_POST['nombre_pagador'] ?? ($_SESSION['cliente_nombre'] ?? ''));
            $cedula_pagador = trim($_POST['cedula_pagador'] ?? '');
            $metodo = trim($_POST['metodo'] ?? 'transferencia');

            $error = validarReportePago($id_pedido, $referencia, $banco_emisor, $telefono_limpio, $monto);

            if ($error !== null) {
                responderJson(['success' => false, 'message' => $error]);
            }

            $cedula_persona = $_SESSION['cliente_cedula'] ?? '';

            if (empty($cedula_persona)) {
                error_log("procesarReporte - Sesión sin cedula_persona");
                responderJson(['success' => false, 'message' => 'Sesión no válida. Inicia sesión nuevamente']);
            }

            $pedido = $modelo->PedidoobtenerPorIdYCliente($id_pedido, $cedula_persona);

            if (!$pedido) {
                responderJson(['success' => false, 'message' => 'Pedido no encontrado o no te pertenece']);
            }

            $tasaActual = $tasaCambioModel->obtener();
            $pedidoTotal = floatval($pedido['total']);

            if ($tasaActual <= 0) {
                responderJson(['success' => false, 'message' => 'La tasa de cambio no está disponible. Contacte a soporte.']);
            }

            $montoUsd = $monto / $tasaActual;

            if ($montoUsd < ($pedidoTotal - 0.01)) {
                responderJson([
                    'success' => false,
                    'message' => 'El monto transferido (Bs. ' . number_format($monto, 2) . ' ≈ $' . number_format($montoUsd, 2) . ' a la tasa ' . number_format($tasaActual, 2) . ') no cubre el total del pedido ($' . number_format($pedidoTotal, 2) . '). Asegúrate de transferir el monto completo.'
                ]);
            }

            $banco_receptor = $modelo->ReceptorobtenerNombre($metodo)
                ?? trim($_POST['banco_receptor'] ?? '')
                ?? 'Banesco';

            $conn = $modelo->PagogetConexion();

            $conn->beginTransaction();

            $modelo->PagobloquearPedido($id_pedido);

            if ($modelo->ejecutar('pago.existe_por_pedido', ['id_pedido' => $id_pedido]) === true) {
                $conn->rollBack();
                responderJson([
                    'success' => false,
                    'message' => 'Ya existe un reporte de pago para este pedido',
                    'redirect' => '?pagina=misPedidos'
                ]);
            }

            $resultado = $modelo->ejecutar('pago.crear', [
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
                responderJson(['success' => false, 'message' => 'Error al guardar: ' . $resultado['error']]);
            }

            $conn->commit();

            responderJson([
                'success' => true,
                'message' => '¡Pago reportado exitosamente! Tu pedido está en revisión.',
                'redirect' => '?pagina=misPedidos'
            ]);
        } catch (\Exception $e) {
            error_log("Error en procesarReporte: " . $e->getMessage());
            responderJson(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }

    case 'datospago':
        try {
            $id_pedido = intval($_GET['pedido'] ?? 0);
            $metodo = $_GET['metodo'] ?? 'transferencia';

            if ($id_pedido <= 0) {
                echo "Pedido no encontrado";
                exit;
            }

            if (!in_array($metodo, ['transferencia', 'pago_movil'])) {
                echo "Método de pago inválido";
                exit;
            }

            $pedido = $modelo->PedidoobtenerPorIdYCliente($id_pedido, $_SESSION['cliente_cedula'] ?? '');

            if (!$pedido) {
                echo "Pedido no encontrado";
                exit;
            }

            $tasa = $tasaCambioModel->obtener();
            $total_bs = $tasaCambioModel->convertir($pedido['total']);
            $vigencia = $tasaCambioModel->vigencia();
            $fecha_tasa = $tasaCambioModel->obtenerFechaActualizacion();

            if (!empty($pedido['total_bs']) && (float) $pedido['total_bs'] > 0 && !empty($pedido['tasa_bs']) && (float) $pedido['tasa_bs'] > 0) {
                $tasa = (float) $pedido['tasa_bs'];
                $total_bs = (float) $pedido['total_bs'];
                $vigencia = ['expirada' => false, 'minutos' => 0, 'horas' => 0, 'mensaje' => 'Tasa del pedido'];
                $fecha_tasa = 'Tasa del pedido';
            } else {
                $modelo->PedidoactualizarTotalBs($id_pedido, $total_bs, $tasa);
            }

            $datos_pago = $modelo->ReceptorobtenerDatosPago($metodo);

            if (!$datos_pago) {
                error_log("datosPago: sin banco receptor configurado para método '$metodo', se usa el respaldo de Checkout");
                if ($metodo === 'transferencia') {
                    $datos_pago = $modelo->ejecutar('checkout.obtenerDatosBanco', ['banco_id' => null]);
                } else {
                    $datos_pago = $modelo->ejecutar('checkout.obtenerDatosPagoMovil');
                }
            }

            if ($metodo === 'transferencia') {
                $titulo = "Transferencia Bancaria";
            } else {
                $titulo = "Pago Móvil";
            }

            $vistas->mostrar('datosPago.php', [
                'datos_pago' => $datos_pago,
                'id_pedido' => $id_pedido,
                'pedido' => $pedido,
                'total_bs' => $total_bs,
                'tasa' => $tasa,
                'vigencia' => $vigencia,
                'metodo' => $metodo,
                'titulo' => $titulo,
            ]);
        } catch (\Exception $e) {
            error_log("Error en datosPago: " . $e->getMessage());
            echo "Error al cargar datos de pago";
            exit;
        }
        break;

    case 'cajerapanel':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cedula = $_SESSION['username'] ?? null;
        $rol = $_SESSION['rol'] ?? null;
        if (!isset($cedula) || !isset($rol)) {
            header('Location: ?pagina=iniciarSesion');
            exit;
        }
        if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'listar')) {
            header('Location: ?pagina=principal');
            exit;
        }
        try {
            $bitacora->registrar(
                'cajera_panel',
                'ACCESO',
                'Ventas Online',
                $cedula,
                0
            );
        } catch (Exception $e) {
            error_log("Error al registrar acceso en bitácora: " . $e->getMessage());
        }
        $pagos_pendientes = $modelo->ejecutar('pago.obtener_pendientes');
        if (isset($pagos_pendientes['error'])) {
            $pagos_pendientes = [];
        }
        $pagos_aprobados = $modelo->ejecutar('pago.obtener_aprobados_sin_despacho');
        if (isset($pagos_aprobados['error'])) {
            $pagos_aprobados = [];
        }
        $despachos_activos = $modelo->ejecutar('despacho.obtenerActivos');
        if (isset($despachos_activos['error'])) {
            $despachos_activos = [];
        }
        $stats = [
            'pendientes' => count($pagos_pendientes),
            'aprobados' => count($pagos_aprobados),
            'en_ruta' => count($despachos_activos)
        ];
        error_log("📊 CajeraPanel - Pendientes: " . $stats['pendientes']);
        error_log("📊 CajeraPanel - Aprobados: " . $stats['aprobados']);
        error_log("📊 CajeraPanel - En Ruta: " . $stats['en_ruta']);
        $vistas->mostrar('admin/panelCajera.php', [
            'stats' => $stats,
            'pagos_pendientes' => $pagos_pendientes,
            'pagos_aprobados' => $pagos_aprobados,
            'despachos_activos' => $despachos_activos,
        ]);
        break;

    case 'getdashboarddata':
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cedula = $_SESSION['username'] ?? null;
        session_write_close();
        if (!$cedula) {
            echo json_encode(['success' => false]);
            exit;
        }
        if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'listar')) {
            echo json_encode(['success' => false, 'message' => 'Sin permisos']);
            exit;
        }
        try {
            $tipo = $_GET['tipo'] ?? 'resumen';
            switch ($tipo) {
                case 'top_clientes':
                    echo json_encode(array_merge(['success' => true], $modelo->DashboardtopClientes()));
                    break;
                case 'top_productos':
                    echo json_encode(array_merge(['success' => true], $modelo->DashboardtopProductos()));
                    break;
                case 'ventas_diarias':
                    echo json_encode(array_merge(['success' => true], $modelo->DashboardventasDiarias()));
                    break;
                case 'ventas_mensuales':
                    echo json_encode(array_merge(['success' => true], $modelo->DashboardventasMensuales()));
                    break;
                case 'ventas_no_concretadas':
                    echo json_encode(['success' => true, 'datos' => $modelo->DashboardventasNoConcretadas()]);
                    break;
                case 'metodos_pago':
                    echo json_encode(['success' => true, 'datos' => $modelo->DashboardmetodosPago()]);
                    break;
                case 'despachos_mensuales':
                    echo json_encode(array_merge(['success' => true], $modelo->DashboarddespachosMensuales()));
                    break;
                default:
                    $pagosPendientes = $modelo->DashboardpagosPendientes();
                    $pagosAprobados = $modelo->DashboardpagosAprobados();
                    $despachosActivos = $modelo->DashboarddespachosActivos();
                    echo json_encode([
                        'success' => true,
                        'pendientes' => $modelo->DashboardcontarPagosPendientes(),
                        'aprobados' => $modelo->DashboardcontarPagosAprobados(),
                        'en_ruta' => $modelo->DashboardcontarDespachosActivos(),
                        'total_pedidos' => $modelo->DashboardcontarPedidos(),
                        'html_pendientes' => $vistaParciales->render('pagosPendientes.php', ['pagosPendientes' => $pagosPendientes]),
                        'html_aprobados' => $vistaParciales->render('pagosAprobados.php', ['pagosAprobados' => $pagosAprobados]),
                        'html_despachos' => $vistaParciales->render('despachosActivos.php', ['despachosActivos' => $despachosActivos])
                    ]);
                    break;
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'procesarverificacion':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cedula = $_SESSION['username'] ?? null;
        $rol = $_SESSION['rol'] ?? null;
        session_write_close();
        if (!isset($cedula) || !isset($rol)) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'aprobar_pago')) {
            echo json_encode(['success' => false, 'message' => 'Sin permisos para verificar pagos']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id_reporte']) || empty($data['accion'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        $id_reporte = intval($data['id_reporte']);
        $accion = $data['accion'];
        $id_pedido = intval($data['id_pedido'] ?? 0);
        if ($id_pedido <= 0) {
            $reporte = $modelo->ejecutar('pago.obtener_por_id', ['id' => $id_reporte]);
            if (!$reporte || empty($reporte['id_pedido'])) {
                echo json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado']);
                exit;
            }
            $id_pedido = intval($reporte['id_pedido']);
        }
        if ($accion !== 'aprobar' && $accion !== 'rechazar') {
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            exit;
        }
        $motivo = trim((string)($data['motivo'] ?? ''));
        if ($accion === 'rechazar' && $motivo === '') {
            $motivo = 'Pago no verificado por el administrador';
        }
        try {
            $resultado = $modelo->ejecutar('pago.procesar_verificacion', [
                'id_reporte' => $id_reporte,
                'accion' => $accion,
                'verificado_por' => $cedula,
                'motivo_rechazo' => $motivo
            ]);
            if ($resultado['success']) {
                if ($accion === 'aprobar') {
                    $pedidoActualizado = $modelo->PedidoactualizarEstado($id_pedido, 'aprobado');
                    if ($pedidoActualizado) {
                        $detalles = $modelo->PedidoobtenerDetalle($id_pedido);
                        if (!empty($detalles)) {
                            foreach ($detalles as $detalle) {
                                $descontado = $modelo->ejecutar('producto.descontarStock', [
                                    'id' => $detalle['id_producto'],
                                    'cantidad' => $detalle['cantidad']
                                ]);
                                if (!$descontado) {
                                    error_log("⚠️ No se pudo descontar stock del Producto #{$detalle['id_producto']}");
                                }
                            }
                        }
                    } else {
                        error_log("⚠️ No se pudo actualizar el pedido #{$id_pedido}");
                    }
                } elseif ($accion === 'rechazar') {
                    $modelo->PedidoactualizarEstado($id_pedido, 'rechazado', $motivo);
                    error_log("❌ Pedido #{$id_pedido} rechazado por cajera {$cedula}");
                }
                try {
                    $bitacora->registrar(
                        'pago_online',
                        $accion === 'aprobar' ? 'APROBAR_PAGO' : 'RECHAZAR_PAGO',
                        'Ventas Online',
                        $cedula,
                        $id_reporte
                    );
                } catch (Exception $e) {
                    error_log("Error al registrar en bitácora: " . $e->getMessage());
                }
                echo json_encode([
                    'success' => true,
                    'message' => $accion === 'aprobar' ? 'Pago aprobado correctamente' : 'Pago rechazado',
                    'pedido_actualizado' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $resultado['error'] ?? 'Error al procesar la verificación'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error en procesarVerificacion: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ]);
        }
        exit;

    case 'creardespacho':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cedula = $_SESSION['username'] ?? null;
        $rol = $_SESSION['rol'] ?? null;
        session_write_close();
        if (!isset($cedula) || !isset($rol)) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'crear_despacho')) {
            echo json_encode(['success' => false, 'message' => 'Sin permisos para crear despachos']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id_pedido'])) {
            echo json_encode(['success' => false, 'message' => 'ID de pedido requerido']);
            exit;
        }
        $id_pedido = intval($data['id_pedido']);
        $tipo = $data['tipo'] ?? 'delivery';
        $motorizado_nombre = trim($data['motorizado_nombre'] ?? '');
        $motorizado_telefono = trim($data['motorizado_telefono'] ?? '');
        $tiempo_estimado = intval($data['tiempo_estimado'] ?? 60);
        $pedido = $modelo->PedidoobtenerPorId($id_pedido);
        if (!$pedido) {
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            exit;
        }
        $fecha_estimada = date('Y-m-d', strtotime('+' . ceil($tiempo_estimado / 60) . ' days'));
        $resultado = $modelo->ejecutar('despacho.crear', [
            'id_pedido' => $id_pedido,
            'tipo_despacho' => $tipo,
            'fecha_despacho' => date('Y-m-d'),
            'despachador_nombre' => $motorizado_nombre,
            'despachador_telefono' => $motorizado_telefono,
            'instrucciones_entrega' => $pedido['direccion_entrega'] ?? '',
            'fecha_entrega_estimada' => $fecha_estimada
        ]);
        if ($resultado['success'] ?? false) {
            $modelo->PedidoactualizarEstado($id_pedido, 'enviado');
        }
        if ($resultado['success'] ?? false) {
            try {
                $bitacora->registrar(
                    'despachos',
                    'ASIGNAR_DESPACHO',
                    'Ventas Online',
                    $cedula,
                    $id_pedido
                );
            } catch (Exception $e) {
                error_log("Error al registrar en bitácora: " . $e->getMessage());
            }
        }
        echo json_encode([
            'success' => $resultado['success'] ?? false,
            'message' => ($resultado['success'] ?? false) ? 'Despacho creado correctamente' : ($resultado['error'] ?? 'Error al crear despacho'),
            'id_despacho' => $resultado['id_despacho'] ?? null,
            'id_pedido' => $id_pedido
        ]);
        exit;

    case 'gestionardespacho':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cedula = $_SESSION['username'] ?? null;
        $rol = $_SESSION['rol'] ?? null;
        session_write_close();
        if (!isset($cedula) || !isset($rol)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            exit;
        }
        if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'gestionar_despacho')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Sin permisos']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $id_pedido = intval($data['id_pedido'] ?? 0);
        $accion = $data['accion'] ?? '';
        if ($id_pedido <= 0 || empty($accion)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        $mapa_accion_estado = [
            'en_ruta'   => ['pedido' => 'enviado',   'despacho' => 'en_ruta'],
            'entregado' => ['pedido' => 'entregado', 'despacho' => 'entregado'],
            'cancelado' => ['pedido' => 'cancelado', 'despacho' => 'cancelado'],
        ];
        if (!isset($mapa_accion_estado[$accion])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            exit;
        }
        $nuevo_estado_pedido = $mapa_accion_estado[$accion]['pedido'];
        $nuevo_estado_despacho = $mapa_accion_estado[$accion]['despacho'];
        header('Content-Type: application/json');
        try {
            $despachos = $modelo->ejecutar('despacho.obtenerPorPedido', ['id_pedido' => $id_pedido]);
            if (empty($despachos)) {
                echo json_encode(['success' => false, 'message' => 'No hay despacho asignado para este pedido']);
                exit;
            }
            $despacho = $despachos[0];
            $id_despacho = $despacho['id_despacho'];
            $resultado = $modelo->ejecutar('despacho.actualizarEstado', ['id_despacho' => $id_despacho, 'estado' => $nuevo_estado_despacho]);
            if (!$resultado) {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el despacho']);
                exit;
            }
            $modelo->PedidoactualizarEstado($id_pedido, $nuevo_estado_pedido);
            try {
                $bitacora->registrar(
                    'despachos',
                    strtoupper($accion),
                    'Ventas Online',
                    $cedula,
                    $id_pedido
                );
            } catch (\Exception $e) {
                error_log("Error al registrar en bitácora: " . $e->getMessage());
            }
            echo json_encode([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'estado_pedido' => $nuevo_estado_pedido,
                'estado_despacho' => $nuevo_estado_despacho
            ]);
        } catch (\Exception $e) {
            error_log("Error en gestionarDespacho: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
        exit;

    default:
        header('Location: ?pagina=web_Catalogo');
        exit;
}

exit;
