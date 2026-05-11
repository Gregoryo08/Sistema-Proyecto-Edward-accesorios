<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\Empleados;
use App\Sistema\models\roles;
use App\Sistema\models\cargos;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!$cedula || !$rol) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $obj_empleado = new empleados();
    $obj_empleado->setCedula($cedula);
    
    if (isset($_GET['x'])) {
        $resultados = $obj_empleado->consultaSuspendidos(); 
    } else {
        $resultados = $obj_empleado->consultaPerfiles(); 
    }

    echo json_encode($resultados); 
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultaRoles') {
    $obj_rol = new roles();
    echo json_encode($obj_rol->listar());
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'perfil') {

    $cedula_p = isset($_POST['cedula']) ? trim($_POST['cedula']) : "";
    $clave_p  = isset($_POST['clave']) ? trim($_POST['clave']) : "";
    $rol_p    = isset($_POST['id_rol']) ? (int)$_POST['id_rol'] : 0; 
    $codigo_p = isset($_POST['codigo']) ? trim($_POST['codigo']) : "";

    
    if (empty($cedula_p) || empty($clave_p) || empty($rol_p) || empty($codigo_p)) {
        echo json_encode(["incompleto" => "Hay campos vacíos, por favor verifique para continuar!"]);
        exit();
    }

    $obj_perfil = new usuarios();
    $obj_perfil->setCedula($cedula_p);
    $obj_perfil->setClave($clave_p);
    $obj_perfil->setCodigo($codigo_p);
    $obj_perfil->setRol($rol_p);
    
    $respuesta = $obj_perfil->crearPerfil();
    
    echo json_encode(is_array($respuesta) ? $respuesta : ["success" => "Perfil creado exitosamente."]);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'estatus') {
    $id_user = $_POST['id'];
    $estatus_user = $_POST['estatus'];

    $obj_estatus = new usuarios();
    $obj_estatus->setCedula($id_user);
    $obj_estatus->setEstatus($estatus_user);
    $respuesta = $obj_estatus->cambiarEstatus();
   
    if (is_array($respuesta) && isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
    } else {
        echo json_encode(["success" => "Estatus actualizado exitosamente."]);
    }
    exit();
}

include 'app/views/usuarios.php';