<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use App\Sistema\Models\Empleados;
use \PDO;
use \PDOException;
use \Throwable;

class Usuarios extends Empleados {
    private $codigo;
    private $clave;
    private $estatus;
    private $numero;
    private $rol;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function validarSeguridad()
    {
        $cedula = $this->getCedula();
        $seguridad = $this->getCodigo();

        $conexUser = new Conexion("usuario");

        $stmt = $conexUser->prepare("SELECT COUNT(*) as conteo FROM usuarios WHERE cedula_usuario = :c and codigo = :s");

        $stmt->bindParam(":c", $cedula);
        $stmt->bindParam(":s", $seguridad);

        if (!($stmt->execute())) {
            return ["error" => "Error con el servidor"];
        }

        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        unset($conexUser);
        return $array["conteo"];
    }

    public function tienePermiso($modulo, $accion)
{
    try {
        $id_rol = $_SESSION["rol"];
        
       
        if ($id_rol == 1) {
            return true;
        }

        $conexUser = new Conexion("usuario");
        $sql = "SELECT COUNT(*) as acceso 
                FROM rol_permisos rp
                INNER JOIN modulos m ON rp.id_modulo = m.id_modulo
                INNER JOIN acciones a ON rp.id_accion = a.id_accion
                WHERE rp.id_rol = :id_rol 
                AND m.nombre_modulo = :modulo 
                AND (a.nombre_accion = :accion OR a.nombre_accion = 'control_total')";

        $stmt = $conexUser->prepare($sql);
        $stmt->bindParam(":id_rol", $id_rol);
        $stmt->bindParam(":modulo", $modulo);
        $stmt->bindParam(":accion", $accion);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($conexUser);
        
        return ($resultado['acceso'] > 0);

    } catch (Throwable $th) {
        return false;
    }
}

    public function obtenerDatosPersonales()
    {
        $cedula = trim($this->getCedula());
        
        try {
            $conex = new Conexion("sistema"); 
            $stmt = $conex->prepare("SELECT nombre, apellido FROM empleados WHERE cedula_empleado = :c");
            $stmt->bindParam(":c", $cedula);
            $stmt->execute();
            
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            return $res;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function validarPermisos($rol)
    {
        $conexUser = new Conexion("usuario");

        $sql = "SELECT 
                    rp.id_rol, 
                    r.descripcion_rol, 
                    m.id_modulo, 
                    m.nombre_modulo, 
                    a.nombre_accion 
                FROM roles as r 
                INNER JOIN rol_permisos as rp ON r.idRol = rp.id_rol 
                INNER JOIN modulos as m ON rp.id_modulo = m.id_modulo 
                INNER JOIN acciones as a ON rp.id_accion = a.id_accion 
                WHERE r.idRol = :id";

        $stmt = $conexUser->prepare($sql);
        $stmt->bindParam(":id", $rol);
        $stmt->execute();

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        unset($conexUser);
        return $resultado;
    }

    public function crearPerfil()
    {
        $usuario = $this->getCedula();
        $clave = $this->getClave();
        $codigo = $this->getCodigo();
        $rol = $this->getRol();

        if (empty($usuario) || empty($clave) || empty($codigo) || empty($rol)) {
            return ["incompleto" => "Hay campos vacíos, por favor verifique para continuar con el registro!"];
        }

        if (!(is_string($usuario) && preg_match('/^[VE]-\d{6,9}$/', $usuario) && strlen($usuario) >= 6)) {
            return ["invalido" => "Formato de la Cedula '$usuario' es Invalido!"];
        }
        
        if (!(is_string($clave) && strlen($clave) >= 5)) {
            return ["invalido" => "Formato de la Clave Invalido!"];
        }
        
        $hash = password_hash($clave, PASSWORD_DEFAULT);

        try {
            $conex = new Conexion("usuario");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo ="Administrar Usuarios";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

           
            $stmt = $conex->prepare("INSERT INTO usuarios(cedula_usuario, clave, codigo, id_rol) VALUES (:cedula, :clave, :c, :id_rol)");

            if (!($stmt->execute([":cedula" => $usuario, ":clave" => $hash, ":c" => $codigo, ":id_rol" => $rol]))) {
                $conex->rollBack();
                return ["error" => "Error al insertar en la base de datos."];
            }

            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            if(isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function cambiarClave()
    {
        $cedula = $this->getCedula();
        $clave = $this->getClave();

        if (!(preg_match('/^[VE]-\d{6,9}$/', $cedula))) {
            return ["invalido" => "Formato de Cedula Invalido!"];
        }
        if (!(is_string($clave) && strlen($clave) >= 5)) {
            return ["invalido" => "La clave debe tener al menos 5 caracteres."];
        }

        $hash = password_hash($clave, PASSWORD_DEFAULT);

        try {
            $conexUser = new Conexion("usuario");
            $conexUser->beginTransaction();
            
            $user = isset($_SESSION["username"]) ? $_SESSION["username"] : $cedula;

            $modulo ="Administrar Usuarios";
            $conexUser->exec("SET @usuario_actual = '{$user}'");
            $conexUser->exec("SET @modulo = '{$modulo}'");

            $stmt = $conexUser->prepare("UPDATE usuarios SET clave = :cl WHERE cedula_usuario = :c");
            $stmt->bindParam(":cl", $hash);
            $stmt->bindParam(":c", $cedula);

            if (!($stmt->execute())) {
                $conexUser->rollBack();
                return ["error" => "Error con el servidor"];
            }

            $conexUser->commit();
            unset($conexUser);
            return true;
        } catch (Throwable $th) {
            if(isset($conexUser)) $conexUser->rollBack();
            return ["error" => "Error con el servidor"];
        }
    }
    
    public function cambiarEstatus()
    {
        $cedula = $this->getCedula();
        $estatus = $this->getEstatus();

        if (empty($cedula) || empty($estatus)) {
            return ["incompleto" => "Cédula o Estatus vacíos."];
        }

        try {
            $conexUsuario = new Conexion("usuario");
            $conexUsuario->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Usuarios";
            $conexUsuario->exec("SET @usuario_actual = '{$user}'");
            $conexUsuario->exec("SET @modulo = '{$modulo}'");

            $stmt = $conexUsuario->prepare("UPDATE usuarios SET estatus = :e WHERE cedula_usuario = :c");

            if (!($stmt->execute([":e" => $estatus, ":c" => $cedula]))) {
                $conexUsuario->rollBack();
                return ["error" => "Error al actualizar estatus de usuario."];
            }

            $conexSistema = new Conexion("sistema");
            $conexSistema->exec("SET @usuario_actual = '{$user}'");
            $conexSistema->exec("SET @modulo = '{$modulo}'");

            $perfil = ($estatus == "Inactivo") ? "suspendido" : "si";

            $stmt = $conexSistema->prepare("UPDATE empleados SET perfil = :p WHERE cedula_empleado = :c");

            if(!($stmt->execute([":p" => $perfil, ":c" => $cedula]))){
                $conexUsuario->rollBack();
                unset($conexSistema);
                return ["error" => "Error al actualizar perfil de empleado."];
            }

            $conexUsuario->commit();
            unset($conexUsuario);
            unset($conexSistema);
            return true;
        } catch (PDOException $e) {
            if(isset($conexUsuario)) $conexUsuario->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    
    public function getCodigo() { return $this->codigo; }
    public function setCodigo($codigo) { $this->codigo = $codigo; }
    public function getClave() { return $this->clave; }
    public function setClave($clave) { $this->clave = $clave; }
    public function getEstatus() { return $this->estatus; }
    public function setEstatus($estatus) { $this->estatus = $estatus; }
    public function getNumero() { return $this->numero; }
    public function setNumero($numero) { $this->numero = $numero; }
    public function getRol() { return $this->rol; }
    public function setRol($rol) { $this->rol = $rol; }
}