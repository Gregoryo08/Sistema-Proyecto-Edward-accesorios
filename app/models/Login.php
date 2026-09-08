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


public function esMayorDeEdad($fechaNacimiento) {
    $fechaObj = new \DateTime($fechaNacimiento);
    $hoy = new \DateTime();
    $edad = $hoy->diff($fechaObj)->y;
    return $edad >= 18;
}

public function registrarCliente($datos)
{
    $fechaNacimiento = new \DateTime($datos['fecha_nacimiento']);
    $hoy = new \DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y;

    if ($edad < 18) {
        return ["error" => "Debe ser mayor de 18 años para registrarse."];
    }

    try {
        $conexGeneral = new Conexion('sistema');
        $conexUser = new Conexion('usuario');

        $stmtCheck = $conexGeneral->prepare("SELECT cedula_persona FROM persona WHERE cedula_persona = ?");
        $stmtCheck->execute([$datos['cedula']]);
        if ($stmtCheck->fetch()) {
            return ["error" => "La cédula ya se encuentra registrada."];
        }

        $conexGeneral->beginTransaction();
        $conexUser->beginTransaction();

        $stmtPersona = $conexGeneral->prepare("INSERT INTO persona (cedula_persona, nombre, apellido, correo, telefono, direccion, fecha_nacimiento, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtPersona->execute([
            $datos['cedula'],
            $datos['nombre'],
            $datos['apellido'],
            $datos['correo'],
            $datos['telefono'],
            $datos['residencia'],
            $datos['fecha_nacimiento'],
            $datos['sexo']
        ]);

        $stmtCliente = $conexGeneral->prepare("INSERT INTO clientes (cedula_persona, residencia, estado) VALUES (?, ?, 'activo')");
        $stmtCliente->execute([
            $datos['cedula'],
            $datos['residencia']
        ]);

        $hash = password_hash($datos['clave'], PASSWORD_DEFAULT);
        $stmtUser = $conexUser->prepare("INSERT INTO usuarios (cedula_usuario, clave, estatus, id_rol) VALUES (?, ?, 'Activo', 6)");
        $stmtUser->execute([$datos['cedula'], $hash]);

        $conexGeneral->commit();
        $conexUser->commit();
        return ["success" => "Registro exitoso"];
    } catch (\Exception $e) {
        if (isset($conexGeneral)) $conexGeneral->rollBack();
        if (isset($conexUser)) $conexUser->rollBack();
        return ["error" => "Error interno: " . $e->getMessage()];
    }
}

    private function procesarLogin($usuario, $clave)
    {
        try {
            $conexUser = Conexion::getShared('usuario')->getConexion();
            
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
            $conex = Conexion::getShared("sistema")->getConexion();
            $conexUser = Conexion::getShared("usuario")->getConexion();
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





public function registrarIntentoFail($ip) {
    try {
        $conex = Conexion::getShared('usuario')->getConexion();
        $stmt = $conex->prepare("INSERT INTO intentos_fallidos (ip) VALUES (?)");
        $stmt->execute([$ip]);
    } catch (\Exception $e) {
        
    }
}

public function verificarBloqueoIP($ip) {
    try {
        $conex = Conexion::getShared('usuario')->getConexion();
        $stmt = $conex->prepare("SELECT COUNT(*) FROM intentos_fallidos WHERE ip = ? AND fecha > (NOW() - INTERVAL 15 MINUTE)");
        $stmt->execute([$ip]);
        return (int)$stmt->fetchColumn();
    } catch (\Exception $e) {
        return 0;
    }
}

public function limpiarIntentos($ip) {
    try {
        $conex = Conexion::getShared('usuario')->getConexion();
        $stmt = $conex->prepare("DELETE FROM intentos_fallidos WHERE ip = ?");
        $stmt->execute([$ip]);
    } catch (\Exception $e) {
        
    }
}




}


