<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\modulo;
use App\Sistema\models\roles;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Roles";


if (!$obj_usuario->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $obj_roles = new roles();
    $roles = $obj_roles->listar();
    echo json_encode($roles);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents("php://input");
    $data_form_ajax = json_decode($json_data, true);
    
    if ($data_form_ajax === null && json_last_error() !== JSON_ERROR_NONE) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(["error" => "Ha ocurrido un error con el servidor!"]);
        exit();
    }

    $accion = $data_form_ajax["accion"] ?? null;

    if ($accion === 'consultaModulos') {
        $obj_modulo = new modulo();
        echo json_encode(["modulo" => $obj_modulo->listar()]);
        exit();
    }

    if ($accion === "validarRol") {
        $obj_roles = new roles();
        $obj_roles->setNombre_rol(trim($data_form_ajax["nombre"]));
        echo json_encode($obj_roles->validarRol());
        exit();
    }

    if ($accion === "consultarPermisos") {
        $obj_roles = new roles();
        $obj_roles->setId_rol($data_form_ajax["id_rol"]);
        $respuesta = $obj_roles->consultarPermisos();
        echo json_encode($respuesta);
        exit();
    }

    if ($accion === "registrar") {
        if (!$obj_usuario->tienePermiso($modulo_actual, "registrar")) {
            echo json_encode(["error" => "No tienes permisos para registrar roles."]);
            exit();
        }
        $obj_roles = new roles();
        $obj_roles->setNombre_rol($data_form_ajax["nombre"]);
        $obj_roles->setPermisos($data_form_ajax["permisos"]);
        $respuesta = $obj_roles->registrar();
        echo json_encode(isset($respuesta["success"]) ? $respuesta : ["success" => "Rol Registrado con Éxito!"]);
        exit();
    }

    if ($accion == "modificar") {
        if (!$obj_usuario->tienePermiso($modulo_actual, "modificar")) {
            echo json_encode(["error" => "No tienes permisos para modificar roles."]);
            exit();
        }
        $obj_roles = new roles();
        $obj_roles->setId_rol((int)($data_form_ajax["id"] ?? 0));
        $obj_roles->setPermisos($data_form_ajax["permisos"]);
        $respuesta = $obj_roles->modificar(); 

        if (isset($respuesta["error"]) || isset($respuesta["incompleto"]) || isset($respuesta["invalido"])) {
            echo json_encode($respuesta);
        } else {
            echo json_encode(["success" => "Rol Actualizado con Éxito!"]);
        }
        exit();
    }

    if ($accion === "eliminar") {
        if (!$obj_usuario->tienePermiso($modulo_actual, "eliminar")) {
            echo json_encode(["error" => "No tienes permisos para eliminar roles."]);
            exit();
        }
        $obj_roles = new roles();
        $obj_roles->setId_rol((int)$data_form_ajax["id"]);
        echo json_encode($obj_roles->eliminar());
        exit();
    }
}

include 'App/views/roles.php';