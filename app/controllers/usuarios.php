<?php

use App\Sistema\models\Usuario;
use App\Sistema\models\Usuarios;
use App\Sistema\models\Roles;

$cedula = $_SESSION['username'] ?? null;
$rol = $_SESSION["rol"] ?? null;

if (!$cedula || !$rol) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario_crud = new Usuario();
$obj_usuario_admin = new Usuarios();
$modulo_actual = "Administrar Usuarios";

if (!$obj_usuario_admin->tienePermiso($modulo_actual, "listar")) {
    header("Location: ?pagina=principal");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['permisos'])) {
    header('Content-Type: application/json');
    echo json_encode([
        "registrar" => $obj_usuario_admin->tienePermiso($modulo_actual, "registrar"),
        "modificar" => $obj_usuario_admin->tienePermiso($modulo_actual, "modificar"),
        "eliminar"  => $obj_usuario_admin->tienePermiso($modulo_actual, "eliminar")
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax']) && $_GET['ajax'] === 'listar') {
    header('Content-Type: application/json');
    echo json_encode($obj_usuario_crud->listar());
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json');
    switch ($_POST['accion']) {

    case 'consultarUno':
    $obj_usuario_crud->setCedula_usuario($_POST['cedula']);
    echo json_encode($obj_usuario_crud->consultarUno());
    break;
        case 'consultaRoles':
            echo json_encode($obj_usuario_crud->listarRoles());
            break;

        case 'consultaCargos':
            echo json_encode($obj_usuario_crud->listarCargos());
            break;

       case 'registrar':
            $obj_usuario_crud->setCedula_usuario($_POST['cedula']);
            $obj_usuario_crud->setClave($_POST['clave']);
            
            // Validar que id_rol no vaya vacío ni como string vacío
            $id_rol = (!empty($_POST['id_rol'])) ? (int)$_POST['id_rol'] : null;
            $obj_usuario_crud->setId_rol($id_rol);
            
            $obj_usuario_crud->setTipoRegistro($_POST['tipo_usuario']);
            
            $id_cargo = (!empty($_POST['id_cargo'])) ? (int)$_POST['id_cargo'] : null;
            
            echo json_encode($obj_usuario_crud->registrarCompleto([
                "nombre"           => $_POST['nombre'],
                "apellido"         => $_POST['apellido'],
                "correo"           => $_POST['correo'] ?? 'no@correo.com',
                "telefono"         => $_POST['telefono'] ?? '0000',
                "direccion"        => $_POST['direccion'] ?? 'N/A',
                "fecha_nacimiento" => $_POST['fecha_nacimiento'] ?? null,
                "sexo"             => $_POST['sexo'] ?? 'No especificado',
                "id_cargo"         => $id_cargo
            ]));
            break;

        case 'modificar':
            $obj_usuario_crud->setCedula_usuario($_POST['cedula']);
            $obj_usuario_crud->setClave($_POST['clave'] ?? '');
            
            $id_rol = (!empty($_POST['id_rol'])) ? (int)$_POST['id_rol'] : null;
            $obj_usuario_crud->setId_rol($id_rol);
            
            echo json_encode($obj_usuario_crud->modificarPerfil([
                "nombre"           => $_POST['nombre'],
                "apellido"         => $_POST['apellido'],
                "correo"           => $_POST['correo'] ?? 'no@correo.com',
                "telefono"         => $_POST['telefono'] ?? '0000',
                "direccion"        => $_POST['direccion'] ?? 'N/A',
                "fecha_nacimiento" => $_POST['fecha_nacimiento'] ?? null,
                "sexo"             => $_POST['sexo'] ?? 'No especificado'
            ]));
            break;

        case 'estatus':
            $obj_usuario_crud->setCedula_usuario($_POST['id']);
            $obj_usuario_crud->setEstatus($_POST['estatus']);
            echo json_encode($obj_usuario_crud->cambiarEstatus() ? ["success" => true] : ["success" => false, "error" => "Error al actualizar"]);
            break;
    }
    exit();
}

require_once 'app/views/usuario.php';