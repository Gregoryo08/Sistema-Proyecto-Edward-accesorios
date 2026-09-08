<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\financiamiento;
use App\Sistema\models\TasaCambioModel;
use App\Sistema\models\notificacion;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!($cedula && $rol)) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Financiamiento";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar"),
        "registrar_pago"  => $obj_usuario->tienePermiso($modulo_actual, "registrar_pago")
    ]);
    exit();
}

$objeto_finan = new financiamiento();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    
    $opcion = $_GET['x'] ?? '';

    switch ($opcion) {
        case "listado":
            $listado = $objeto_finan->procesarSolicitud('actualizarSaldosYMoras');
            $vencimientos = $objeto_finan->procesarSolicitud('verificarVencimientosProximos');
            if (isset($vencimientos['data'])) {
                foreach ($vencimientos['data'] as $v) {
                    $n = new notificacion();
                    $mensaje = "Financiamiento #{$v['id_financiamiento']} (Cliente: {$v['nombre']} {$v['apellido']}, Producto: {$v['nombre_producto']}) vence en 48 horas.";
                    $n->setCedula_usuario($cedula);
                    $notificacionesExistentes = $n->listar(false); 
                    $yaExiste = false;
                    foreach($notificacionesExistentes as $notifExistente) {
                        if($notifExistente['mensaje'] === $mensaje) {
                            $yaExiste = true;
                            break;
                        }
                    }
                    if(!$yaExiste) {
                        $n->setMensaje($mensaje);
                        $n->setTipo("recordatorio");
                        $n->registrar();
                    }
                }
            }
            echo json_encode($listado);
            break;
        case "clientes":
            echo json_encode($objeto_finan->listarClientes());
            break;
        case "telefonos_disponibles":
            echo json_encode($objeto_finan->listarTelefonosDisponibles());
            break;
        case "metodos":
            echo json_encode($objeto_finan->listarMetodos());
            break;
        case "bancos":
            echo json_encode($objeto_finan->listarBancos());
            break;
        case "tasa_bcv":
            $tasaModel = new TasaCambioModel();
            echo json_encode(["tasa" => $tasaModel->obtener()]);
            break;
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json');
    $accion = $_POST['accion'];

    $accionesEscritura = ['anularFinanciamiento', 'actualizarFinanciamiento', 'registrarPagoCuota', 'registrarFinanciamiento', 'cambiarEstadoEquipo', 'finalizarContrato', 'aprobarPago', 'negarPago'];
    if (in_array($accion, $accionesEscritura) && !$obj_usuario->tienePermiso($modulo_actual, "modificar")) {
        echo json_encode(["error" => "No tienes permisos"]);
        exit();
    }

    switch ($accion) {
        case 'anularFinanciamiento':
            $objeto_finan->setId_financiamiento((int)$_POST['id']);
            echo json_encode($objeto_finan->procesarSolicitud('anularFinanciamiento'));
            break;

        case 'consultarCuotas':
            $objeto_finan->setId_financiamiento((int)$_POST["id"]);
            echo json_encode($objeto_finan->consultarCuotas());
            break;

        case 'consultarUno':
            $objeto_finan->setId_financiamiento((int)$_POST['id']);
            echo json_encode($objeto_finan->procesarSolicitud('consultarUno'));
            break;

        case 'actualizarFinanciamiento':
            $objeto_finan->setId_financiamiento((int)$_POST['id_financiamiento']);
            $datos = [
                'monto_total'     => (float)$_POST['monto_total'],
                'pago_inicial'    => (float)$_POST['pago_inicial'],
                'cantidad_cuotas' => (int)$_POST['cantidad_cuotas'],
                'dia_pago'        => (int)$_POST['dia_pago'],
                'fecha_inicio'    => trim($_POST['fecha_inicio']),
                'id_productos'    => (int)$_POST['id_producto'],
                'id_unidad'       => (int)$_POST['id_unidad']
            ];
            echo json_encode($objeto_finan->procesarSolicitud('modificarCompleto', $datos));
            break;

        case 'registrarPagoCuota':
            $datosPago = [
                'id_cuota' => (int)$_POST['id_cuota'],
                'monto'    => (float)$_POST['monto_pagado'],
                'id_metodo'=> (int)$_POST['id_metodopago']
            ];
            $respuesta = $objeto_finan->procesarSolicitud('registrarPago', $datosPago);
            if (isset($respuesta["success"])) {
                $n = new notificacion();
                $n->setMensaje("Pago recibido en cuota #" . $datosPago['id_cuota']);
                $n->setTipo("pago");
                $n->setCedula_usuario($cedula);
                $n->registrar();
            }
            echo json_encode($respuesta);
            break;

        case 'registrarFinanciamiento':
            $objeto_finan->setCedula_persona(trim($_POST['cedula_persona'] ?? ''));
            $objeto_finan->setId_productos((int)($_POST['id_producto'] ?? 0));
            $objeto_finan->setId_unidad((int)($_POST['id_unidad'] ?? 0));
            $objeto_finan->setMonto_total((float)($_POST['monto_total'] ?? 0));
            $objeto_finan->setPago_inicial((float)($_POST['pago_inicial'] ?? 0));
            $objeto_finan->setCantidad_cuotas((int)($_POST['cantidad_cuotas'] ?? 0));
            $objeto_finan->setDia_pago((int)($_POST['dia_pago'] ?? 0));
            $objeto_finan->setFecha_inicio(trim($_POST['fecha_inicio'] ?? ''));
            echo json_encode($objeto_finan->procesarSolicitud('registrar'));
            break;

        case 'cambiarEstadoEquipo':
            $objeto_finan->setId_financiamiento((int)$_POST['id']);
            echo json_encode($objeto_finan->procesarSolicitud('cambiarEstadoEquipo', ['estado' => trim($_POST['estado'])]));
            break;

        case 'finalizarContrato':
            $objeto_finan->setId_financiamiento((int)$_POST['id']);
            echo json_encode($objeto_finan->procesarSolicitud('finalizarContratoManualmente'));
            break;
            
        case 'aprobarPago':
            $id_cuota = (int)$_POST['id_cuota'];
            $respuesta = $objeto_finan->procesarSolicitud('aprobarPago', ['id_cuota' => $id_cuota]);
            
            if (isset($respuesta["success"])) {
                $info = $objeto_finan->obtenerInfoParaNotificacion($id_cuota);
                
                if ($info) {
                    $n = new notificacion();
                    $n->setMensaje("Tu pago de la cuota #{$info['numero_cuota']} del producto {$info['nombre_producto']} ha sido APROBADO.");
                    $n->setTipo("pago");
                    $n->setCedula_usuario($info['cedula_persona']);
                    $n->registrar();
                }
            }
            echo json_encode($respuesta);
            break;

        case 'negarPago':
            $id_cuota = (int)$_POST['id_cuota'];
            $respuesta = $objeto_finan->procesarSolicitud('negarPago', ['id_cuota' => $id_cuota]);
            
            if (isset($respuesta["success"])) {
                $info = $objeto_finan->obtenerInfoParaNotificacion($id_cuota);
                
                if ($info) {
                    $n = new notificacion();
                    $n->setMensaje("Tu pago de la cuota #{$info['numero_cuota']} del producto {$info['nombre_producto']} ha sido RECHAZADO.");
                    $n->setTipo("pago");
                    $n->setCedula_usuario($info['cedula_persona']);
                    $n->registrar();
                }
            }
            echo json_encode($respuesta);
            break;
    }
    exit();
}

$vista = 'App/views/financiamiento.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'App/views/error_404.php';
}