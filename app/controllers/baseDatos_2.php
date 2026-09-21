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

        if (isset($_FILES['backup'])) {
            $files = $_FILES['backup'];
            $fileList = [];

            if (is_array($files['name'])) {
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $fileList[] = [
                            'tmp_name' => $files['tmp_name'][$i],
                            'name' => $files['name'][$i]
                        ];
                    }
                }
            } else {
                if ($files['error'] === UPLOAD_ERR_OK) {
                    $fileList[] = [
                        'tmp_name' => $files['tmp_name'],
                        'name' => $files['name']
                    ];
                }
            }

            if (empty($fileList)) {
                echo json_encode(['resultado' => 'error', 'mensaje' => 'No se seleccionó ningún archivo o ocurrió un error al subir.']);
                exit();
            }

            $carpetaDestino = __DIR__ . '/../databases/Restauraciones/';
            if (!is_dir($carpetaDestino)) {
                mkdir($carpetaDestino, 0755, true);
            }

            $mensajesExito = [];
            $mensajesError = [];

            foreach ($fileList as $file) {
                $nombreArchivo = $file['name'];
                $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

                if (strtolower($extension) !== 'sql') {
                    $mensajesError[] = "El archivo '$nombreArchivo' no es un archivo .sql válido.";
                    continue;
                }

                $rutaDestino = $carpetaDestino . time() . '_' . basename($nombreArchivo);

                if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                    $resultado = $obj_respaldo->restaurarBaseDatos($rutaDestino, $nombreArchivo);

                    if (isset($resultado['success'])) {
                        $mensajesExito[] = $resultado['success'];
                    } else {
                        $mensajesError[] = $resultado['error'] ?? "Error al restaurar '$nombreArchivo'.";
                    }
                } else {
                    $mensajesError[] = "Error al mover '$nombreArchivo'.";
                }
            }

            if (!empty($mensajesError)) {
                $msg = implode(" | ", $mensajesError);
                if (!empty($mensajesExito)) {
                    $msg .= " (Parcial: " . implode(" | ", $mensajesExito) . ")";
                }
                echo json_encode(['resultado' => 'error', 'mensaje' => $msg]);
            } else {
                echo json_encode(['resultado' => 'exito', 'mensaje' => implode("<br>", $mensajesExito)]);
            }
        } else {
            echo json_encode(['resultado' => 'error', 'mensaje' => 'No se ha seleccionado ningún archivo.']);
        }
        exit();
    }
}

require_once 'app/views/baseDatos_2.php';