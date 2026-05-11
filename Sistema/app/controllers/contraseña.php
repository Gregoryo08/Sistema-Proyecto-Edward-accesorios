<?php

use App\Sistema\models\Usuarios;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?url=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$obj_usuario->setCedula(trim($cedula)); 


$datos_personales = $obj_usuario->obtenerDatosPersonales();
if ($datos_personales) {
    $_SESSION["nombre_completo"] = $datos_personales["nombre"] . " " . $datos_personales["apellido"];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    
    $accion = $_POST['accion'];

    
    if ($accion === 'validarC') {
        $cedula_v = $_POST['cedula'];
        $obj_usuario->setCedula($cedula_v);
        $resultado = $obj_usuario->validarSeguridad(); 
        echo json_encode(["data" => $resultado]);
        exit();
    }

    
    if ($accion === 'validarSeguridad') {
        $cedula_v = $_POST['cedula'];
        $codigo_v = $_POST['seguridad'];
        $obj_usuario->setCedula($cedula_v);
        $obj_usuario->setCodigo($codigo_v);
        $resultado = $obj_usuario->validarSeguridad();
        echo json_encode(["data" => $resultado]);
        exit();
    }

   
    if ($accion === 'modificar') {
        $cedula_m = $_POST['cedula'];
        $clave_m = $_POST['clave'];
     
        
        $obj_usuario->setCedula($cedula_m);
        $obj_usuario->setClave($clave_m);
        
        $respuesta = $obj_usuario->cambiarClave();
        
        if ($respuesta === true) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode($respuesta);
        }
        exit();
    }
}


include 'app/views/contraseña.php';