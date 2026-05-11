<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;
use \DateTime;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class recuperacion 
{
    private $email;
    private $cedula;
    private $token;
    private $clave;
    private $clave_repetir;
    private $mensaje; 

    public function __construct() {}

    public function setEmail($email) { $this->email = $email; }
    public function getEmail() { return $this->email; }
    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function getCedula() { return $this->cedula; }
    public function setToken($token) { $this->token = $token; }
    public function getToken() { return $this->token; }
    public function setClave($clave) { $this->clave = $clave; }
    public function getClave() { return $this->clave; }
    public function setClaveRepetir($clave_repetir) { $this->clave_repetir = $clave_repetir; }
    public function getClaveRepetir() { return $this->clave_repetir; }
    public function getMensaje() { return $this->mensaje; }
    public function setMensaje($mensaje) { $this->mensaje = $mensaje; }

    public function generarTokenRecuperacion()
    {
        $email = $this->getEmail();

        if (empty($email)) {
            return ["incompleto" => "El correo electrónico es requerido."];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["invalido" => "Formato de correo electrónico inválido."];
        }

        $conexPrincipal = null; 
        $conexUsuarios = null;  

        try {
            $conexPrincipal = new Conexion(); 
            
            $stmt_cedula = $conexPrincipal->prepare("SELECT cedula_empleado FROM empleados WHERE correo = :correo");
            $stmt_cedula->bindParam(":correo", $email);
            $stmt_cedula->execute();
            $resultado_cedula = $stmt_cedula->fetch(PDO::FETCH_ASSOC);

            if (!$resultado_cedula || empty($resultado_cedula['cedula_empleado'])) {
                return ["success" => "Si el correo electrónico existe en nuestro sistema, se ha enviado un enlace de recuperación."]; 
            }

            $cedula_usuario = $resultado_cedula['cedula_empleado'];
            $token = bin2hex(random_bytes(32));
            $expira_en = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $conexUsuarios = new Conexion("usuario"); 
            $conexUsuarios->beginTransaction();

            $stmt_update_token = $conexUsuarios->prepare("UPDATE usuarios SET reset_token = :token, reset_token_expires_at = :expira WHERE cedula_usuario = :cedula");
            $stmt_update_token->execute([
                ":token" => $token,
                ":expira" => $expira_en,
                ":cedula" => $cedula_usuario
            ]);

            if (!$this->enviarCorreoRecuperacion($email, $token)) {
                $conexUsuarios->rollBack();
                return ["error" => "Error al enviar el correo."];
            }

            $conexUsuarios->commit();
            return ["success" => "Si el correo electrónico existe en nuestro sistema, se ha enviado un enlace de recuperación."];

        } catch (PDOException $e) {
            if ($conexUsuarios && $conexUsuarios->inTransaction()) $conexUsuarios->rollBack();
            return ["error" => "Error de base de datos."];
        } catch (Exception $e) {
            if ($conexUsuarios && $conexUsuarios->inTransaction()) $conexUsuarios->rollBack();
            return ["error" => "Error en el proceso."];
        }
    }

    public function validarTokenYRestablecerClave()
    {
        $token = $this->getToken();
        $nueva_clave = $this->getClave();
        $repetir_clave = $this->getClaveRepetir();

        if (empty($token) || empty($nueva_clave) || empty($repetir_clave)) {
            return ["incompleto" => "Todos los campos son requeridos."];
        }

        if ($nueva_clave !== $repetir_clave) {
            return ["invalido" => "Las contraseñas no coinciden."];
        }

        $conex = null; 
        try {
            $conex = new Conexion("usuario"); 
            $conex->beginTransaction();

            $stmt_select = $conex->prepare("SELECT cedula_usuario, reset_token_expires_at FROM usuarios WHERE reset_token = :token");
            $stmt_select->execute([":token" => $token]);
            $usuario = $stmt_select->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                $conex->rollBack();
                return ["error" => "Token inválido o ya utilizado."];
            }

            if (new DateTime() > new DateTime($usuario['reset_token_expires_at'])) {
                $conex->rollBack();
                return ["error" => "El enlace ha expirado."];
            }

            $hashed_clave = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $stmt_update = $conex->prepare("UPDATE usuarios SET clave = :clave, reset_token = NULL, reset_token_expires_at = NULL WHERE cedula_usuario = :cedula");
            $stmt_update->execute([
                ":clave" => $hashed_clave,
                ":cedula" => $usuario['cedula_usuario']
            ]);

            $conex->commit();
            return ["success" => "Contraseña restablecida exitosamente."];

        } catch (PDOException $e) {
            if ($conex && $conex->inTransaction()) $conex->rollBack();
            return ["error" => "Error del servidor."];
        }
    }

    public function validarTokenExistente()
    {
        $token = $this->getToken();
        if (empty($token)) return ["valido" => false, "mensaje" => "Token ausente."];

        try {
            $conex = new Conexion("usuario"); 
            $stmt = $conex->prepare("SELECT cedula_usuario, reset_token_expires_at FROM usuarios WHERE reset_token = :token");
            $stmt->execute([":token" => $token]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return ["valido" => false, "mensaje" => "Token no encontrado."];
            }

            if (new DateTime() > new DateTime($usuario['reset_token_expires_at'])) {
                return ["valido" => false, "mensaje" => "El token ha expirado."];
            }

            return ["valido" => true, "cedula_usuario" => $usuario['cedula_usuario']];
        } catch (PDOException $e) {
            return ["valido" => false, "mensaje" => "Error de conexión."];
        }
    }

    private function enviarCorreoRecuperacion($email, $token)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'soportehotelelmeson@gmail.com';
            $mail->Password = 'bcxy zubw eamp foet';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('soportehotelelmeson@gmail.com', 'Sistema Edward Accesorios');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de Contraseña';

            
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            
            
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); 
            
            
            $resetLink = $protocol . "://" . $host . $uri . "/index.php?pagina=iniciarSesion&token=" . urlencode($token);

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
                    <h2 style='color: #2c3e50; text-align: center;'>Solicitud de Cambio de Contraseña</h2>
                    <p>Hola,</p>
                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en <strong>Accesorios Edward</strong>.</p>
                    <p>Para continuar con el proceso, haz clic en el botón de abajo:</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' style='background-color: #00bcd4; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Restablecer Contraseña</a>
                    </p>
                    <p style='font-size: 12px; color: #777;'>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                    <p style='font-size: 12px; color: #00bcd4;'>{$resetLink}</p>
                    <p>Este enlace es válido por <strong>1 hora</strong>. Si no solicitaste este cambio, puedes ignorar este correo.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 11px; text-align: center; color: #999;'>Sistema de Gestión - Accesorios Edward</p>
                </div>";

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }
}