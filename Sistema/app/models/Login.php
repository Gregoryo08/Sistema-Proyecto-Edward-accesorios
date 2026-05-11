<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;

class login
{
    private $usuario;
    private $claveU;
    private $menssage;
    private $intentos;

    public function __construct() {}

    public function getUsuario() { return $this->usuario; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }
    public function getClave() { return $this->claveU; }
    public function setClave($claveU) { $this->claveU = $claveU; }
    public function getIntentos() { return $this->intentos; }
    public function setIntentos($intentos) { $this->intentos = $intentos; }
    public function getMenssage() { return $this->menssage; }
    public function setMenssage($mensage) { $this->menssage = $mensage; }

    public function logearse()
    {
        $usuario = trim($this->getUsuario() ?? ''); 
        $clave = trim($this->getClave() ?? '');
        $intentos = $this->getIntentos();

        if ($intentos < 4) {
            if (preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/', $usuario)) {
                return $this->procesarLogin($usuario, $clave);
            } 
            else if (preg_match('/^[VE]-\d{6,9}$/', $usuario) || ctype_digit($usuario)) {
                if (ctype_digit($usuario)) {
                    $usuario = "V-" . $usuario;
                }
                return $this->procesarLogin($usuario, $clave);
            } 
            else {
                return ["incorrect" => "Formato de usuario no valido!"];
            }
        } else {
            return $this->restringir($usuario);
        }
    }

    private function procesarLogin($usuario, $clave)
    {
        try {
            $conexUser = new Conexion('usuario');
            
            $stmt = $conexUser->prepare("SELECT cedula_usuario, clave, id_rol, estatus FROM usuarios WHERE cedula_usuario = :u");
            $stmt->bindParam(':u', $usuario);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                return ["notFound" => "Usuario o Contraseña no Encontrados!"];
            }

            if (!password_verify($clave, $resultado['clave'])) {
                return ["password" => "Contraseña Incorrecta!"];
            }

            if ($resultado["estatus"] !== "Activo") {
                return ["idle" => "Comuniquese con el administrador para desbloquear su cuenta!"];
            }

            $sqlRol = "SELECT DISTINCT u.cedula_usuario, r.idRol, r.descripcion_rol 
                       FROM usuarios as u 
                       INNER JOIN roles as r ON u.id_rol = r.idRol 
                       WHERE u.cedula_usuario = :u";
            
            $stmt = $conexUser->prepare($sqlRol);
            $stmt->bindParam(':u', $usuario);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) {
                return ["error" => "Error al obtener permisos del rol."];
            }

            return ["success" => $datos];

        } catch (\PDOException $e) {
            return ["error" => "Error en el servidor: " . $e->getMessage()];
        }
    }

    public function restringir($cedula)
    {
        if (ctype_digit($cedula)) {
            $cedula = "V-" . $cedula;
        }

        try {
            $conex = new Conexion("sistema");
            $conexUser = new Conexion("usuario");
            $conexUser->beginTransaction();

            $stmt = $conexUser->prepare("UPDATE usuarios SET estatus = 'Inactivo' WHERE cedula_usuario = :c");
            $stmt->bindParam(":c", $cedula);
            $stmt->execute();

            $query = $conex->prepare("UPDATE empleados SET perfil = 'suspendido' WHERE cedula_empleado = :c");
            $query->bindParam(":c", $cedula);
            $query->execute();

            $conexUser->commit();
            return ["disabled" => "Cuenta bloqueada por exceso de intentos."];
        } catch (\PDOException $e) {
            if(isset($conexUser)) $conexUser->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
}