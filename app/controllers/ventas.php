<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Sistema\models\Usuarios;
use App\Sistema\models\ventas;
use App\Sistema\models\scrape_dolar;
use App\Sistema\models\TasaModel;

$action = $_GET['action'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    $cedula = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;
    
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

        $tasa = scrape_dolar::obtenerPrecioDolarBCV();

        if ($tasa !== null && $tasa > 0) {
            $_SESSION['tasa_bcv'] = $tasa;
            $_SESSION['tasa_bcv_time'] = time();
            return (float) $tasa;
        }

        if (
            isset($_SESSION['tasa_bcv'], $_SESSION['tasa_bcv_time']) &&
            (time() - $_SESSION['tasa_bcv_time']) < $cacheTtl
        ) {
            return (float) $_SESSION['tasa_bcv'];
        }

        return 0.0;
    }
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Ventas";
$obj_ventas = new ventas();
$obj_tasa = new TasaModel();

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

            $cedulaCliente = trim($datosCliente['cedula'] ?? '');
            $nombre        = trim($datosCliente['nombre'] ?? '');
            $apellido      = trim($datosCliente['apellido'] ?? '');
            $telefono      = trim($datosCliente['telefono'] ?? '');

            if ($cedulaCliente === '' || $nombre === '' || $apellido === '') {
                echo json_encode([
                    "success" => false, 
                    "mensaje" => "Los campos Cédula, Nombre y Apellido son obligatorios."
                ]);
                exit();
            }

            try {
                $obj_ventas->setCedula($cedulaCliente);
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

        case 'verificarAdmin':
            $clave = $_POST['clave'] ?? '';
            if (empty($clave)) {
                echo json_encode(["success" => false, "mensaje" => "Debe ingresar la contraseña."]);
                exit();
            }

            $esAdmin = $obj_tasa->validarClaveAdministrador($clave);
            if ($esAdmin) {
                $_SESSION['admin_tasa_autorizado'] = true;
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "mensaje" => "Contraseña incorrecta o permisos insuficientes."]);
            }
            break;

        case 'guardarTasaManual':
            if (empty($_SESSION['admin_tasa_autorizado'])) {
                echo json_encode(["success" => false, "mensaje" => "No posee autorización de administrador."]);
                exit();
            }

            $nuevaTasa = $_POST['tasa'] ?? null;
            if (!$nuevaTasa || !is_numeric($nuevaTasa) || $nuevaTasa <= 0) {
                echo json_encode(["success" => false, "mensaje" => "Monto de tasa inválido."]);
                exit();
            }

            $resultado = $obj_tasa->cambiarTasaManual($nuevaTasa, $cedula, '');
            
            if ($resultado === true || (is_array($resultado) && !isset($resultado['error']))) {
                unset($_SESSION['admin_tasa_autorizado']);
                $_SESSION['tasa_bcv'] = (float) $nuevaTasa;
                $_SESSION['tasa_bcv_time'] = time();
                echo json_encode(["success" => true, "mensaje" => "Tasa actualizada correctamente."]);
            } else {
                $errorMsg = is_array($resultado) && isset($resultado['error']) ? $resultado['error'] : "Error al guardar la tasa.";
                echo json_encode(["success" => false, "mensaje" => $errorMsg]);
            }
            break;

        case 'obtenerTasaCambio':
            $tasa = obtenerTasaOptimizada();
            $fecha = date('d/m/Y');

            if ($tasa > 0) {
                $idTasaDB = null;
            } else {
                $tasaDB = $obj_tasa->obtenerTasaActual();
                if ($tasaDB && !empty($tasaDB['tasa'])) {
                    $tasa = (float) $tasaDB['tasa'];
                    $fecha = date('d/m/Y', strtotime($tasaDB['fecha_actualizacion']));
                }
            }

            if ($tasa <= 0) {
                $obj_tasa->registrarNotificacionTasaNoDisponible();
            }

            echo json_encode([
                "success" => $tasa > 0,
                "tasa" => $tasa,
                "fecha" => $fecha,
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

            $tasaMonto = obtenerTasaOptimizada();
            $idTasa = null;

            if ($tasaMonto > 0) {
                $datosVenta['id_tasa'] = null;
                $datosVenta['tasa_monto'] = $tasaMonto;
            } else {
                $tasaDB = $obj_tasa->obtenerTasaActual();
                if ($tasaDB && !empty($tasaDB['tasa'])) {
                    $datosVenta['id_tasa'] = $tasaDB['id'] ?? null;
                    $datosVenta['tasa_monto'] = (float)$tasaDB['tasa'];
                } else {
                    $datosVenta['id_tasa'] = null;
                    $datosVenta['tasa_monto'] = 0.0;
                }
            }

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
if ($tasaCambioActual <= 0) {
    $tasaDB = $obj_tasa->obtenerTasaActual();
    $tasaCambioActual = ($tasaDB && !empty($tasaDB['tasa'])) ? (float)$tasaDB['tasa'] : 0.0;
}

if ($tasaCambioActual <= 0) {
    $obj_tasa->registrarNotificacionTasaNoDisponible();
}

$ruta_vista = "app/views/ventas.php"; 

if (file_exists($ruta_vista)) {
    require_once $ruta_vista;
} else {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>Error 404: La vista ventas no existe.</h3>";
    echo "<p>Ruta buscada: <code>{$ruta_vista}</code></p>";
    exit();
}