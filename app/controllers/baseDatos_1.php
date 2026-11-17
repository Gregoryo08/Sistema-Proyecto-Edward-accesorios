<?php
use App\Sistema\Models\basedatos;
use App\Sistema\Models\Usuarios;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Base De Datos";

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'descargar_backup') {
    $archivo = basename($_GET['archivo']);
    $ruta = __DIR__ . '/../databases/Respaldos/' . $archivo;

    if (file_exists($ruta)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'realizar_backup') {
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        
        $obj_respaldo = new basedatos();
        $resultado = $obj_respaldo->realizarBackup();
        
        if ($resultado['resultado'] === 'exito') {
            $resultado['archivo'] = $obj_respaldo->getNombreArchivoGenerado(); 
        }
        
        echo json_encode($resultado);
        exit();
    }
}

require_once 'app/views/baseDatos_1.php';