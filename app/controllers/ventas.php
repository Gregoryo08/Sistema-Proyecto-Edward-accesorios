<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\Usuarios;
use App\Sistema\models\ventas;
use App\Sistema\models\scrape_dolar;

$action = $_GET['action'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    $$cedula = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;
    
    if (!$cedula) {
        echo json_encode(["error" => "Sesion no encontrada o expirada"]);
        exit();
    }

  
}

$cedula = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;
$rol = $_SESSION["rol"] ?? '6';
$_SESSION["rol"] = $rol;

if ($cedula) {
    $_SESSION['cliente_cedula'] = $cedula;
}

if (!$cedula) {
    header("Location: ?pagina=loginEcommerce");
    exit();
}

if (!function_exists('obtenerTasaOptimizada')) {
    function obtenerTasaOptimizada(): float
    {
        $cacheTtl = 3600;

        if (
            isset($_SESSION['tasa_bcv'], $_SESSION['tasa_bcv_time']) &&
            (time() - $_SESSION['tasa_bcv_time']) < $cacheTtl
        ) {
            return (float) $_SESSION['tasa_bcv'];
        }

        $tasa = scrape_dolar::obtenerPrecioDolarBCV();

        if ($tasa !== null && $tasa > 0) {
            $_SESSION['tasa_bcv'] = $tasa;
            $_SESSION['tasa_bcv_time'] = time();
            return (float) $tasa;
        }

        return (float) ($_SESSION['tasa_bcv'] ?? 0.0);
    }
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Ventas";
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
            $tasa = obtenerTasaOptimizada();
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
            $datosVenta['tasa_bcv'] = $datosVenta['tasa_bcv'] ?? obtenerTasaOptimizada();

            $respuesta = $obj_ventas->procesarSolicitud('registrarVenta', $datosVenta);
            echo json_encode($respuesta);
            break;

        default:
            echo json_encode(["success" => false, "mensaje" => "Acción no reconocida."]);
            break;
    }
    exit();
}

$tasaCambioActual = obtenerTasaOptimizada();

$ruta_vista = "app/views/ventas.php"; 

if (file_exists($ruta_vista)) {
    require_once $ruta_vista;
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>Error 404: La vista ventas no existe.</h3>";
    echo "<p>Ruta buscada: <code>{$ruta_vista}</code></p>";
    exit();
}