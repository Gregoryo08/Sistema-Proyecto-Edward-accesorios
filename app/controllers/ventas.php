<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\Usuarios;
use App\Sistema\models\ventas;
use App\Sistema\models\TasaCambioModel;

$action = $_GET['action'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    $$cedula = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;
    
    if (!$cedula) {
        echo json_encode(["error" => "Sesion no encontrada o expirada"]);
        exit();
    }

  
}

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;
$_SESSION["rol"] = $rol;

if ($cedula) {
    $_SESSION['cliente_cedula'] = $cedula;
}

if (!$cedula) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$obj_tasa = new TasaCambioModel();
$modulo_actual = "Administrar Ventas";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    if (($_GET['ajax'] ?? '') === 'true' || $_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false,
            "mensaje" => "No tienes permiso para ingresar al modulo de ventas."
        ]);
        exit();
    }

    header("Location: ?pagina=principal");
    exit();
}

$obj_ventas = new ventas();

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

if ($accion !== null) {
    header('Content-Type: application/json');
    
    switch ($accion) {
        case 'listarProductos':
        case 'listarMetodosPago':
        case 'listarClientes':
            $resultado = $obj_ventas->procesarSolicitud($accion);
            echo json_encode([
                "success" => true,
                "data" => $resultado
            ]);
            break;

        case 'registrarCliente':
            $datosCliente = $_POST['data'] ?? $_POST ?? null;

            if (empty($datosCliente) || !is_array($datosCliente)) {
                echo json_encode([
                    "success" => false, 
                    "mensaje" => "No se recibieron los datos requeridos para registrar el cliente."
                ]);
                exit();
            }

            $cedula   = trim($datosCliente['cedula'] ?? '');
            $nombre   = trim($datosCliente['nombre'] ?? '');
            $apellido = trim($datosCliente['apellido'] ?? '');
            $telefono = trim($datosCliente['telefono'] ?? '');

            if ($cedula === '' || $nombre === '' || $apellido === '') {
                echo json_encode([
                    "success" => false, 
                    "mensaje" => "Los campos Cédula, Nombre y Apellido son obligatorios."
                ]);
                exit();
            }

            try {
                $obj_ventas->setCedula($cedula);
                $obj_ventas->setNombre($nombre);
                $obj_ventas->setApellido($apellido);
                $obj_ventas->setCel($telefono !== '' ? $telefono : 'No registrado');
                $obj_ventas->setCorreo($datosCliente['correo'] ?? 'No registrado');
                $obj_ventas->setSexo($datosCliente['sexo'] ?? 'No registrado');
                $obj_ventas->setEdad($datosCliente['fecha'] ?? '0000-00-00');
                $obj_ventas->setDireccion($datosCliente['direccion'] ?? 'No registrado');

                $resultado = $obj_ventas->procesarSolicitud('registrarCliente');

                if (is_array($resultado) && isset($resultado['error'])) {
                    echo json_encode([
                        "success" => false,
                        "mensaje" => "Error de BD: " . $resultado['error']
                    ]);
                } else if ($resultado === true) {
                    echo json_encode([
                        "success" => true,
                        "mensaje" => "Cliente registrado exitosamente."
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "mensaje" => "No se pudo completar el registro del cliente."
                    ]);
                }

            } catch (\Throwable $e) {
                echo json_encode([
                    "success" => false, 
                    "mensaje" => "Error en el servidor: " . $e->getMessage()
                ]);
            }
            break;

        case 'obtenerTasaCambio':
            $tasa = $obj_tasa->obtenerTasaConCache();
            echo json_encode([
                "success" => $tasa > 0,
                "tasa" => $tasa,
                "fecha" => date('d/m/Y'),
                "mensaje" => $tasa > 0 ? "Tasa obtenida con éxito" : "No se pudo obtener la tasa de cambio"
            ]);
            break;

        case 'procesarVenta':
            $rawData = $_POST['data'] ?? null;
            
            if (!$rawData) {
                echo json_encode(["success" => false, "mensaje" => "No se recibieron datos para procesar la venta."]);
                exit();
            }

            $datosVenta = json_decode($rawData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(["success" => false, "mensaje" => "Error de formato en la payload enviada."]);
                exit();
            }

            if (empty($datosVenta['items'])) {
                echo json_encode(["success" => false, "mensaje" => "El carrito de compras está vacío."]);
                exit();
            }

            if (empty($datosVenta['pagos'])) {
                echo json_encode(["success" => false, "mensaje" => "Debe registrar al menos un método de abono/pago."]);
                exit();
            }
            
            $datosVenta['cedula_usuario'] = $cedula;

            if (empty($datosVenta['cedula_usuario'])) {
                echo json_encode(["success" => false, "mensaje" => "Error de sesión: No se localizó la cédula del operador de caja."]);
                exit();
            }
            $datosVenta['tasa_bcv'] = $datosVenta['tasa_bcv'] ?? $obj_tasa->obtenerTasaConCache();

            $respuesta = $obj_ventas->procesarSolicitud('registrarVenta', $datosVenta);
            echo json_encode($respuesta);
            break;

        default:
            echo json_encode(["success" => false, "mensaje" => "Acción no reconocida."]);
            break;
    }
    exit();
}

$tasaCambioActual = $obj_tasa->obtenerTasaConCache();

$ruta_vista = "app/views/ventas.php"; 

if (file_exists($ruta_vista)) {
    require_once $ruta_vista;
} else {
    header("HTTP/1.0 404 Not Found");
    require_once 'app/views/404.php';
    exit();
}