<?php


use App\Sistema\models\Usuarios;
use App\Sistema\models\bancos;


$cedula = $_SESSION['username'];
$rol = $_SESSION["rol"];

if (!(isset($cedula) && isset($rol))) {
    header("Location: ?pagina=iniciarSesion");
    exit();
}

$obj_usuario = new Usuarios();
$modulo_actual = "Administrar Bancos";

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
    $obj_bancos = new bancos();
    $registros = $obj_bancos->listar();

    echo json_encode($registros);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'consultar') {
    $cedula = (int) trim($_POST["id"]);

    $obj_bancos = new bancos();
    $obj_bancos->setCedula_cuenta($cedula);
    $registros = $obj_bancos->consultarBanco();

    echo json_encode($registros);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'validar') {
    $cedula = (int) trim($_POST["cedula"]);
    $nombre = trim($_POST["banco"]);

    $obj_bancos = new bancos();
    $obj_bancos->setCedula_cuenta($cedula);
    $obj_bancos->setNombre_banco($nombre);
    $registros = $obj_bancos->validar();

    echo json_encode($registros);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registrar') {
    $nombre = trim($_POST['nombre']);
    $numero = trim($_POST['numero']);
    $telefono = trim($_POST['telefono']);
    $cedula = (int) trim($_POST['cedula']);

    $obj_bancos = new bancos();
    $obj_bancos->setNombre_banco($nombre);
    $obj_bancos->setNumero_cuenta($numero);
    $obj_bancos->setTelefono($telefono);
    $obj_bancos->setCedula_cuenta($cedula);

    $respuesta = $obj_bancos->registrar_banco();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        exit();
    }

    echo json_encode(["success" => "Banco registrado exitosamente."]);
    unset($obj_bancos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'modificar') {
    $nombre = trim($_POST['nombre']);
    $numero = trim($_POST['numero']);
    $cedula = (int) trim($_POST['cedula']);
    $telefono = trim($_POST["telefono"]);
    $id_banco = (int) trim($_POST["id"]);

    $obj_bancos = new bancos();
    $obj_bancos->setNombre_banco($nombre);
    $obj_bancos->setNumero_cuenta($numero);
    $obj_bancos->setCedula_cuenta($cedula);
    $obj_bancos->setId_banco($id_banco);
    $obj_bancos->setTelefono($telefono);
    $respuesta = $obj_bancos->modificar();

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        unset($obj_bancos);
        exit();
    }
    if (isset($respuesta["incompleto"])) {
        echo json_encode(["incompleto" => $respuesta["incompleto"], "input" => $respuesta["input"]]);
        unset($obj_bancos);
        exit();
    }
    if (isset($respuesta["invalido"])) {
        echo json_encode(["invalido" => $respuesta["invalido"], "input" => $respuesta["input"]]);
        unset($obj_bancos);
        exit();
    }

    echo json_encode(["success" => "Banco modificado exitosamente."]);
    unset($obj_bancos);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_banco = (int) trim($_POST["id"]);
    $tipo = trim($_POST["tipo"]);

    $obj_bancos = new bancos();
    $obj_bancos->setId_banco($id_banco);
    $respuesta = $obj_bancos->eliminar($tipo);

    if (isset($respuesta["error"])) {
        echo json_encode(["error" => $respuesta["error"]]);
        exit();
    }

    echo json_encode(["success" => "Servicio Eliminado exitosamente."]);
    unset($obj_bancos);
    exit();
}


$vista = 'app/views/bancos.php';

if (file_exists($vista)) {
    require_once $vista;
} else {
    require_once 'App/views/error_404.php';
}
?>
