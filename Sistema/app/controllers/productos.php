<?php

use App\Sistema\models\productos;
use App\Sistema\models\Usuarios;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Productos";

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $objeto = new productos();
    
    if ($_GET['x'] === "productos") {
        echo json_encode($objeto->listar());
        exit();
    }
    if ($_GET['x'] === "categorias") {
        echo json_encode($objeto->listarCategorias());
        exit();
    }
    if ($_GET['x'] === "marcas") {
        echo json_encode($objeto->listarMarcas());
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $objeto = new productos();

    if ($_POST['accion'] === 'registrarProducto' || $_POST['accion'] === 'modificarProducto') {
        
        if ($_POST['accion'] === 'modificarProducto') {
            $objeto->setId_producto((int)$_POST["id"]);
        }

        $objeto->setNombre_producto(trim($_POST['nombre']));
        $objeto->setId_categoria((int)$_POST['id_categoria']);
        $objeto->setId_marca((int)$_POST['id_marca']);
        $objeto->setStock_minimo((int)$_POST['stock_minimo']);
        $objeto->setStock_maximo((int)$_POST['stock_maximo']);
        $objeto->setStock_actual((int)$_POST['stock_actual']);
        $objeto->setPrecio_detal((float)$_POST['precio']);

        // Datos del teléfono (vienen del modal si la categoría es Teléfonos)
        $objeto->setImei(trim($_POST['imei'] ?? ''));
        $objeto->setRam(trim($_POST['ram'] ?? ''));
        $objeto->setAlmacenamiento(trim($_POST['almacenamiento'] ?? ''));

        $respuesta = ($_POST['accion'] === 'registrarProducto') ? $objeto->registrar() : $objeto->modificar();
        
        $msj = ($_POST['accion'] === 'registrarProducto') ? "registrado" : "modificado";
        procesarRespuesta($respuesta, "Producto {$msj} exitosamente.");
    }

    if ($_POST['accion'] === 'eliminarProducto') {
        $objeto->setId_producto((int)$_POST["id"]);
        $respuesta = $objeto->eliminar();
        procesarRespuesta($respuesta, "Producto eliminado exitosamente.");
    }
}

function procesarRespuesta($respuesta, $mensajeExito) {
    if ($respuesta === true) {
        echo json_encode(["success" => $mensajeExito]);
    } else {
        echo json_encode($respuesta);
    }
    exit();
}

include 'app/views/productos.php';