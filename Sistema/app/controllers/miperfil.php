<?php

use App\Sistema\models\Usuarios;
use App\Sistema\models\Empleados;

$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];


if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consulta') {
    $obj_perfil = new Usuarios();
    $obj_perfil->setCedula($cedula);
    $resultado = $obj_perfil->consultar();

    if(isset($resultado["error"])){
        echo json_encode(["error" => $resultado["error"]]);
        exit();
    }

    echo json_encode($resultado);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $cedula_m = trim($_POST['cedula']);
    $cargo = (int) trim($_POST['cargo']);

    $obj_perfil = new Empleados();
    $obj_perfil->setNombre($nombre);
    $obj_perfil->setApellido($apellido);
    $obj_perfil->setCedula($cedula_m);
    $obj_perfil->setCel($telefono);
    $obj_perfil->setDireccion($direccion);
    $obj_perfil->setCorreo($correo);
    $obj_perfil->setCargo($cargo);
    
    
    $resultado = $obj_perfil->ModificarEmpleado($cedula_m);

    if (is_array($resultado)) {
        if (isset($resultado["error"])) {
            echo json_encode(["error" => $resultado["error"]]);
        } else if (isset($resultado["incompleto"])) {
            echo json_encode(["incompleto" => $resultado["incompleto"]]);
        } else if (isset($resultado["invalido"])) {
            echo json_encode(["invalido" => $resultado["invalido"]]);
        }
        exit();
    }

    echo json_encode(["success" => "Datos modificados exitosamente."]);
    exit();
}

include 'app/views/miperfil.php';