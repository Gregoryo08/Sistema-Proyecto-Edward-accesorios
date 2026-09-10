<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\entradas_productos;
use App\Sistema\models\Productos;
use App\Sistema\models\proveedores;
use App\Sistema\models\notificacion;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!($cedula && $rol)) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Entradas";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar")
    ]);
    exit();
}

$objeto_entrada = new entradas_productos();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    if (ob_get_length()) ob_clean(); 
    header('Content-Type: application/json');
    
    $opcion = $_GET['x'] ?? '';

    switch ($opcion) {
        case "listado":
            $listado = $objeto_entrada->procesarSolicitud('consultarEntradas');
            $vencimientos = $objeto_entrada->procesarSolicitud('verificarGarantiasProximas');
            
            if (is_array($vencimientos) && !empty($vencimientos)) {
                foreach ($vencimientos as $g) {
                    $n = new notificacion();
                    $diasRestantes = (int)$g['dias_restantes'];
                    
                    if ($diasRestantes == 0) {
                        $mensaje = "Garantía vence hoy: {$g['nombre_producto']}";
                    } elseif ($diasRestantes == 1) {
                        $mensaje = "Garantía vence mañana: {$g['nombre_producto']}";
                    } else {
                        $mensaje = "Garantía por vencer: {$g['nombre_producto']} - Quedan {$diasRestantes} días.";
                    }

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
                        $n->setTipo("aviso");
                        $n->registrar();
                    }
                }
            }

            $objProd = new Productos();
            $todosProductos = $objProd->listar();
            foreach ($todosProductos as $p) {
                $stockActual = (int)$p['stock_actual'];
                $stockMinimo = (int)$p['stock_minimo'];
                
                if ($stockActual == 0 || ($stockActual > 0 && $stockActual <= $stockMinimo)) {
                    $n = new notificacion();
                    $mensajeStock = "Reposición necesaria: {$p['nombre_producto']} - Quedan {$stockActual} unidades.";
                    $n->setCedula_usuario($cedula);
                    
                    $notificacionesExistentes = $n->listar(false);
                    $yaExiste = false;
                    foreach($notificacionesExistentes as $notifExistente) {
                        if($notifExistente['mensaje'] === $mensajeStock) {
                            $yaExiste = true;
                            break;
                        }
                    }
                    if(!$yaExiste) {
                        $n->setMensaje($mensajeStock);
                        $n->setTipo("alerta");
                        $n->registrar();
                    }
                }
            }

            echo json_encode($listado);
            break;

        case "productos":
            echo json_encode($objeto_entrada->procesarSolicitud('consultarProductosSinUnidad'));
            break;

        case "proveedores":
            $obj_proveedor = new proveedores();
            if (method_exists($obj_proveedor, 'listar')) {
                echo json_encode($obj_proveedor->listar());
            } else {
                
            }
            break;
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    $accion = $_POST['accion'];

    $accionesEscritura = ['registrar_entrada', 'modificar_cantidad', 'eliminar_entrada'];
    if (in_array($accion, $accionesEscritura) && !$obj_usuario->tienePermiso($modulo_actual, "modificar")) {
        echo json_encode(["error" => "No tienes permisos para realizar modificaciones"]);
        exit();
    }

    switch ($accion) {
        case 'ver_detalles':
            $objeto_entrada->setId_entrada((int)$_POST['id']);
            echo json_encode($objeto_entrada->procesarSolicitud('consultarDetalles'));
            break;

        case 'registrar_entrada':
            if (empty($_POST['rif_proveedor'])) {
                echo json_encode(["error" => "Debes seleccionar un proveedor."]);
                exit();
            }
            if (empty($_POST['productos'])) {
                echo json_encode(["error" => "Debes agregar al menos un producto."]);
                exit();
            }

            $objeto_entrada->setRif_proveedor(trim($_POST['rif_proveedor']));
            $respuesta = $objeto_entrada->procesarSolicitud('registrarEntrada');

            if (isset($respuesta["success"]) && $respuesta["success"]) {
                $id_nueva_entrada = $respuesta["id_entrada"];
                $errorDetalle = false;
                $productos = $_POST['productos'] ?? [];

                foreach ($productos as $prod) {
                    $objeto_entrada->setId_entrada((int)$id_nueva_entrada);
                    $objeto_entrada->setId_producto((int)$prod['id_producto']);
                    $objeto_entrada->setCantidad((float)$prod['cantidad']);
                    
                    $garantia = isset($prod['dias_garantia']) ? trim($prod['dias_garantia']) : '';
                    $objeto_entrada->setDiasGarantia(!empty($garantia) ? (int)$garantia : 0);

                    $resDetalle = $objeto_entrada->procesarSolicitud('insertarDetalle');
                    
                    if (isset($resDetalle['error'])) {
                        $errorDetalle = $resDetalle['error'];
                        break;
                    }
                }

                if ($errorDetalle) {
                    echo json_encode(["error" => "Error al registrar el detalle: " . $errorDetalle]);
                } else {
                    $n = new notificacion();
                    $n->setMensaje("Se ha registrado una nueva entrada de productos al inventario.");
                    $n->setTipo("info");
                    $n->setCedula_usuario($cedula);
                    $n->registrar();

                    echo json_encode(["success" => true]);
                }
            } else {
                echo json_encode($respuesta);
            }
            break;

        case 'modificar_cantidad':
            $objeto_entrada->setId_entrada((int)$_POST['id_entrada']);
            $objeto_entrada->setId_producto((int)$_POST['id_producto']);
            $objeto_entrada->setCantidad((float)$_POST['cantidadNEW']);
            $objeto_entrada->setCantidadOLD((float)$_POST['cantidadOLD']);
            
            $garantiaNEW = trim($_POST['garantiaNEW'] ?? '');
            $objeto_entrada->setDiasGarantia(!empty($garantiaNEW) ? (int)$garantiaNEW : 0);

            echo json_encode($objeto_entrada->procesarSolicitud('modificarDetalle'));
            break;

        case 'eliminar_entrada':
            $objeto_entrada->setId_entrada((int)$_POST['id']);
            echo json_encode($objeto_entrada->procesarSolicitud('eliminarEntrada'));
            break;
    }
    exit();
}

$vista = 'App/views/entradas_productos.php';
if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'App/views/error_404.php';
}

