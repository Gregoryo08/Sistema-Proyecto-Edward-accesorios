<?php

use App\Sistema\models\proveedores;
use App\Sistema\models\Usuarios;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Proveedores";

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
    $objeto = new proveedores();
    
    if ($_GET['x'] === "proveedores") {
        echo json_encode($objeto->listar());
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $objeto = new proveedores();

    if ($_POST['accion'] === 'registrarProveedor' || $_POST['accion'] === 'modificarProveedor') {
        
        $objeto->setRif_proveedor(trim($_POST['rif']));
        $objeto->setNombre_proveedor(trim($_POST['proveedor']));
        $objeto->setTelefono_proveedor(trim($_POST['telefono']));
        $objeto->setCorreo_proveedor(trim($_POST['correo']));
        $objeto->setUbicacion_proveedor(trim($_POST['ubicacion']));

        
        $idActual = ($_POST['accion'] === 'modificarProveedor') ? trim($_POST['rif']) : null;
        $existeRif = $objeto->procesarSolicitud('existeRif', [
            'rif' => $objeto->getRif_proveedor(),
            'id' => $idActual
        ]);
        if ($existeRif) {
            echo json_encode(["error" => "Este Rif ya está registrado.", "input" => "rif"]);
            exit();
        }

        $res = $objeto->procesarSolicitud(
            $_POST['accion'] === 'registrarProveedor' ? 'registrar' : 'modificar'
        );
        
        if ($res === true) {
            echo json_encode(["success" => "Operación realizada"]);
        } else {
            echo json_encode($res);
        }
        exit();
    }

    if ($_POST['accion'] === 'eliminarProveedor') {
        $objeto->setRif_proveedor(trim($_POST["rif"]));
        $res = $objeto->procesarSolicitud('eliminar');
        if ($res === true) {
            echo json_encode(["success" => "Eliminado"]);
        } else {
            echo json_encode($res);
        }
        exit();
    }
}

require_once 'app/views/proveedores.php';