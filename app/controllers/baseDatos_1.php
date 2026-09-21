<?php
ob_start();

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
    if (isset($_GET['archivo']) && !empty($_GET['archivo'])) {
        $archivo = basename($_GET['archivo']);
        $ruta = __DIR__ . '/../../databases/Respaldos/' . $archivo;

        if (file_exists($ruta)) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $archivo . '"');
            header('Content-Length: ' . filesize($ruta));
            header('Pragma: public');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            readfile($ruta);
            exit();
        } else {
            http_response_code(404);
            echo "El archivo no existe.";
            exit();
        }
    } else {
        http_response_code(400);
        echo "Archivo no especificado.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'realizar_backup') {
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        
        $obj_respaldo = new basedatos();
        $resultado = $obj_respaldo->realizarBackup();
        
        echo json_encode($resultado);
        exit();
    }
}

require_once 'app/views/baseDatos_1.php';