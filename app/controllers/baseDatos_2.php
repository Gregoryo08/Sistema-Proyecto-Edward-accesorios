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

$obj_respaldo = new basedatos();

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
        $resultado = $obj_respaldo->realizarBackup();
        
        if ($resultado['resultado'] === 'exito') {
            $resultado['archivo'] = $obj_respaldo->getNombreArchivoGenerado(); 
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado);
        exit();
    }

    if ($_POST['accion'] === 'restaurar_bd') {
        header('Content-Type: application/json');

        if (isset($_FILES['backup']) && $_FILES['backup']['error'] === UPLOAD_ERR_OK) {
            $archivoTmp = $_FILES['backup']['tmp_name'];
            $nombreArchivo = $_FILES['backup']['name'];
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

            if (strtolower($extension) === 'sql') {
                $carpetaDestino = __DIR__ . '/../databases/Restauraciones/';
                if (!is_dir($carpetaDestino)) {
                    mkdir($carpetaDestino, 0755, true);
                }

                $rutaDestino = $carpetaDestino . time() . '_' . basename($nombreArchivo);

                if (move_uploaded_file($archivoTmp, $rutaDestino)) {
                    $resultado = $obj_respaldo->restaurarBaseDatos($rutaDestino);

                    if (isset($resultado['success'])) {
                        echo json_encode(['resultado' => 'exito', 'mensaje' => $resultado['success']]);
                    } else {
                        echo json_encode(['resultado' => 'error', 'mensaje' => $resultado['error'] ?? 'Error desconocido al restaurar la base de datos.']);
                    }
                } else {
                    echo json_encode(['resultado' => 'error', 'mensaje' => 'Error al mover el archivo subido.']);
                }
            } else {
                echo json_encode(['resultado' => 'error', 'mensaje' => 'Por favor, seleccione un archivo con extensión .sql válido.']);
            }
        } else {
            echo json_encode(['resultado' => 'error', 'mensaje' => 'No se ha seleccionado ningún archivo o ocurrió un error en la subida.']);
        }
        exit();
    }
}

require_once 'app/views/baseDatos_2.php';