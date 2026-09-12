<?php

use App\Sistema\models\productos;
use App\Sistema\models\Usuarios;
use App\Sistema\models\notificacion;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Productos";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    echo json_encode([
        "registrar" => $obj_usuario->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario->tienePermiso($modulo_actual, "eliminar"),
        "control_total" => $obj_usuario->tienePermiso($modulo_actual, "control_total")
    ]);
    exit();
}

if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $objeto = new productos();
    
    if ($_GET['x'] === "productos") {
        echo json_encode($objeto->listar());
    } else if ($_GET['x'] === "categorias") {
        echo json_encode($objeto->listarCategorias());
    } else if ($_GET['x'] === "marcas") {
        echo json_encode($objeto->listarMarcas());
    } else if ($_GET['x'] === "autoAsociarImagenes") {
        $res = $objeto->autoAsociarImagenes();
        echo json_encode($res);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $objeto = new productos();

    if ($_POST['accion'] === 'registrarProducto' || $_POST['accion'] === 'modificarProducto') {
        
        $isModificar = ($_POST['accion'] === 'modificarProducto');
        if ($isModificar) {
            $objeto->setId_producto((int)$_POST["id"]);
        }

        $objeto->setNombre_producto(trim($_POST['nombre']));
        $objeto->setDescripcion(trim($_POST['descripcion'] ?? ''));
        $objeto->setId_categoria((int)$_POST['id_categoria']);
        $objeto->setId_marca((int)$_POST['id_marca']);
        $objeto->setStock_minimo((int)$_POST['stock_minimo']);
        $objeto->setStock_maximo((int)$_POST['stock_maximo']);
        $objeto->setStock_actual((int)$_POST['stock_actual']);
        $objeto->setPrecio_detal((float)$_POST['precio']);
        $objeto->setImei(trim($_POST['imei'] ?? ''));
        $objeto->setRam(trim($_POST['ram'] ?? ''));
        $objeto->setAlmacenamiento(trim($_POST['almacenamiento'] ?? ''));

        $imagen_nombre = null;
        $quitar_imagen = (($_POST['quitar_imagen'] ?? '0') === '1');
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $directorio = __DIR__ . '/../../assets/img/productos/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            $mime = $_FILES['imagen']['type'];
            $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mime, $permitidos)) {
                echo json_encode(["invalido" => "Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP."]);
                exit();
            }

            if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                echo json_encode(["invalido" => "La imagen no puede superar 5MB."]);
                exit();
            }

            $nombre_prod = strtolower(trim($_POST['nombre']));
            $nombre_prod = preg_replace('/\s+/', '_', $nombre_prod);
            $nombre_prod = preg_replace('/[^a-z0-9_\-]/', '', $nombre_prod);

            $ext_real = strtolower(pathinfo(basename($_FILES['imagen']['name']), PATHINFO_EXTENSION));
            $imagen_nombre = $nombre_prod . '.' . $ext_real;

            $destino = $directorio . $imagen_nombre;
            $contador = 1;
            while (file_exists($destino)) {
                $imagen_nombre = $nombre_prod . '_' . $contador . '.' . $ext_real;
                $destino = $directorio . $imagen_nombre;
                $contador++;
            }

            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                echo json_encode(["invalido" => "Error al guardar la imagen en el servidor."]);
                exit();
            }

            if ($isModificar && !empty($_POST['imagen_actual'])) {
                $imagen_actual_path = $directorio . $_POST['imagen_actual'];
                if (file_exists($imagen_actual_path) && basename($imagen_actual_path) !== 'default.jpg') {
                    @unlink($imagen_actual_path);
                }
            }
        } else if (!$isModificar) {
            $nombre_para_img = strtolower(trim($_POST['nombre']));
            $nombre_para_img = preg_replace('/\s+/', '_', $nombre_para_img);
            $nombre_para_img = preg_replace('/[^a-z0-9_\-]/', '', $nombre_para_img);
            $imagen_nombre = $nombre_para_img . '.jpg';
        } else if ($quitar_imagen) {
            $imagen_nombre = 'default.jpg';
            if (!empty($_POST['imagen_actual']) && $_POST['imagen_actual'] !== 'default.jpg') {
                $directorio = __DIR__ . '/../../assets/img/productos/';
                $ruta_old = $directorio . $_POST['imagen_actual'];
                if (file_exists($ruta_old) && basename($ruta_old) !== 'default.jpg') {
                    @unlink($ruta_old);
                }
            }
        } else if ($isModificar && !empty($_POST['imagen_actual'])) {
            $imagen_nombre = $_POST['imagen_actual'];
        }

        $objeto->setImagen_principal($imagen_nombre);

        if ($objeto->existeNombre($objeto->getNombre_producto(), $objeto->getId_producto())) {
            if ($imagen_nombre && $imagen_nombre !== 'default.jpg' && isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $directorio = __DIR__ . '/../../assets/img/productos/';
                @unlink($directorio . $imagen_nombre);
            }
            echo json_encode(["invalido" => "Ya existe un producto con este nombre", "input" => "nombre"]);
            exit();
        }

        $res = ($isModificar) ? $objeto->modificar() : $objeto->registrar();
        
        if ($res === true) {
            $stock_actual = (int)$_POST['stock_actual'];
            $stock_minimo = (int)$_POST['stock_minimo'];

            if ($stock_actual <= $stock_minimo) {
                $notif = new notificacion();
                $notif->setMensaje("El producto " . trim($_POST['nombre']) . " tiene stock bajo: " . $stock_actual . " unidades.");
                $notif->setTipo("stock");
                $notif->setCedula_usuario($cedula);
                $notif->registrar();
            }

            $msg = $isModificar ? "Producto modificado" : "Producto registrado";
            if ($imagen_nombre) {
                $msg .= " con imagen: " . $imagen_nombre;
            }
            echo json_encode(["success" => $msg, "imagen" => $imagen_nombre]);
        } else {
            echo json_encode($res);
        }
        exit();
    }

    if ($_POST['accion'] === 'eliminarProducto') {
        $objeto->setId_producto((int)$_POST["id"]);
        $res = $objeto->eliminar();
        if ($res === true) {
            echo json_encode(["success" => "Eliminado"]);
        } else {
            echo json_encode($res);
        }
        exit();
    }
}



$vista = 'app/views/productos.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'app/views/error_404.php';
}