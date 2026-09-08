<?php
// controllers/loginCajera.php - Login para cajeras (usa BD sistema_edward_usuario)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../config/database.php';

$error = '';

// Si ya está logueada
if (isset($_SESSION['cajera_logueada']) && $_SESSION['cajera_logueada'] === true) {
    header('Location: /Sistema_edward/src/Sistema/tienda/index.php?pagina=cajeraPanel');
    exit;
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
   // Conectar a la base de datos de USUARIOS
       $database = new Database('usuario');
       $conn = $database->getConnection();
    
    // Buscar en tabla usuarios (está en sistema_edward_usuario)
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE cedula_usuario = ? AND estatus = 'Activo'");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['clave'])) {
        $_SESSION['cajera_logueada'] = true;
        $_SESSION['cajera_cedula'] = $user['cedula_usuario'];
        $_SESSION['cajera_rol'] = $user['id_rol'];
        
        header('Location: /Sistema_edward/src/Sistema/tienda/index.php?pagina=cajeraPanel');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}

require_once __DIR__ . '/../views/admin/loginCajera.php';
?>