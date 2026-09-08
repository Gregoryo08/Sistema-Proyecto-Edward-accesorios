<?php
use App\Sistema\models\Usuarios;
use App\Sistema\models\Empleados; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? null;
$usuario_sesion = $_SESSION['username'] ?? $_SESSION['cliente_cedula'] ?? null;

if ($action === 'DatosDashboardCliente') {
    header('Content-Type: application/json; charset=utf-8');
    
    if (!$usuario_sesion) {
        echo json_encode(["error" => "Sesion no encontrada o expirada"]);
        exit();
    }
}

$cedula = $usuario_sesion;
$rol = $_SESSION["rol"] ?? '6';
$_SESSION["rol"] = $rol;

if (!$cedula) {
    header("Location: ?pagina=loginEcommerce");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    if ($_POST['accion'] === 'consulta') {
        $obj_perfil = new Empleados();
        $obj_perfil->setCedula($cedula);
        $datos = $obj_perfil->obtenerDatosUsuario();
        
        if (!$datos || empty($datos)) {
            $datos = [
                "nombre" => "Administrador",
                "apellido" => "",
                "cedula" => $cedula,
                "telefono" => "Sin registro",
                "correo" => "Sin registro",
                "direccion" => "Sin registro",
                "fecha_nacimiento" => "Sin registro"
            ];
        }
        
        echo json_encode($datos);
        exit();
    }

    if ($_POST['accion'] === 'modificar') {
        $obj_perfil = new Empleados();
        
        $obj_perfil->setNombre(htmlspecialchars(trim($_POST['nombre'])));
        $obj_perfil->setApellido(htmlspecialchars(trim($_POST['apellido'])));
        $obj_perfil->setCedula(trim($_POST['cedula']));
        $obj_perfil->setCel(trim($_POST['operadora']) . trim($_POST['telefono']));
        $obj_perfil->setDireccion(htmlspecialchars(trim($_POST['direccion'])));
        $obj_perfil->setCorreo(filter_var(trim($_POST['correo']), FILTER_SANITIZE_EMAIL));
        $obj_perfil->setCargo((int)$_POST['cargo']);
        $obj_perfil->setEdad(trim($_POST['fecha_nacimiento']));
        
        $respuesta = $obj_perfil->ModificarEmpleado(trim($_POST['cedula']));
        
        procesarRespuesta($respuesta, "Datos modificados exitosamente.");
    }
}

if (!function_exists('procesarRespuesta')) {
    function procesarRespuesta($respuesta, $mensajeExito) {
        header('Content-Type: application/json');
        if ($respuesta === true || (is_array($respuesta) && isset($respuesta["success"]))) {
            echo json_encode(["success" => $mensajeExito]);
        } else {
            echo json_encode(is_array($respuesta) ? $respuesta : ["error" => "Error interno."]);
        }
        exit();
    }
}

$rolCrudo = is_array($rol) ? ($rol['descripcion_rol'] ?? '') : $rol;
$rolLimpio = strtolower(trim((string)$rolCrudo));

if ($rolLimpio === 'cliente' || $rolLimpio === '6') {
    $vista = 'app/views/miperfil_cliente.php';
} else {
    $vista = 'app/views/miperfil.php';
}

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}