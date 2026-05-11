<?php

namespace App\Sistema\models; 

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class roles extends conexion{
    private $id_rol;
    private $nombre_rol;
    private $permisos;

       public function __construct()
    {
        parent::__construct();
        
    }

    public function listar()
    {
        $conex = new conexion("usuario");

        $stmt = $conex->prepare("SELECT * FROM roles WHERE descripcion_rol != 'Superusuario'");
        $stmt->execute();

        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $array;
    }

    public function validarRol()
    {
        $nombre = $this->getNombre_rol();
        $conex = new conexion("usuario");

        $sql = "SELECT COUNT(descripcion_rol) as conteo FROM roles WHERE descripcion_rol = :n";
        $stmt = $conex->prepare($sql);
        
        if(!($stmt->execute([":n" => $nombre]))){
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }

        $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);

        unset($conex);
        return $respuesta;
    }

    public function consultarPermisos()
    {
        $id_rol = $this->getId_rol();

        if(empty($id_rol)){
            return ["incompleto" => "El identificador del registro no existe!"];
        }

        if(!(is_int($id_rol) && preg_match('/^[0-9]*$/',$id_rol))){
            return ["invalido" => "El identificador del registro no es valido!"];
        }

        $conex = new conexion("usuario");

        $stmt = $conex->prepare("SELECT * FROM rol_permisos as rp INNER JOIN roles as r ON rp.id_rol = r.idRol INNER JOIN modulos as m ON rp.id_modulo = m.id_modulo WHERE id_rol = :id");

        if(!($stmt->execute([":id" => $id_rol]))){
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }

        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $array;
    }

    public function registrar()
    {
        $nombre = $this->getNombre_rol();
        $permisos = $this->getPermisos();

        if(empty($nombre) && empty($permisos)){
            $input = "";

            if(empty($nombre)){
                $input = "rol";
            }

            return ["incompleto" => "El campo '$input' esta vacio, por favor verifique para continuar con el registro!", "input" => $input];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre) && strlen($nombre) >= 4)) {
            return ["invalido" => "El rol ingresado '{$nombre}' no es valido!", "input" => "rol"];
        } 

        try {
            $conex = new conexion("usuario");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Roles";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO roles (descripcion_rol) VALUES (:n)");

            if(!($stmt->execute([":n" => $nombre]))){
                $conex->rollBack();
                return ["error" => "Ah Ocurrido un Error con el Servidor!"];
            }

            $id_rol = $conex->lastInsertId();

            if(count($permisos) > 0){
                for($i = 0; $i < count($permisos); $i++){
                    $permiso = $permisos[$i];
                    $resultado = $this->registrarPermisos($id_rol, $permiso, $conex);
    
                    if(!($resultado == true)){
                        $conex->rollBack();
                        return ["error" => "Ah Ocurrido un Error con el Servidor!"];
                    }
                }
            }
            else{
                return ["incompleto" => "El registro debe tener al menos un permiso de usuario!"];
            }
            
            $conex->commit();
            unset($this->conex);
            return true;
        } catch (PDOException $e) {
            unset($this->conex);
            return ["error" => $e->getMessage()];
        }
    }

   public function registrarPermisos($id_rol, $permiso, $conex)
{
    $id_modulo = $permiso["id_modulo"];
    
    $acciones_map = [
        "registrar"     => 1,
        "consultar"     => 2,
        "modificar"     => 3,
        "eliminar"      => 4,
        "listar"        => 5,
        "control_total" => 6
    ];

    $stmt = $conex->prepare("INSERT INTO rol_permisos (id_rol, id_modulo, id_accion) VALUES (:idR, :idM, :idA)");

    foreach ($acciones_map as $nombre_accion => $id_accion) {
        if (isset($permiso[$nombre_accion]) && $permiso[$nombre_accion] == 1) {
            if (!($stmt->execute([
                ":idR" => $id_rol,
                ":idM" => $id_modulo,
                ":idA" => $id_accion
            ]))) {
                return false;
            }
        }
    }

    return true;
}

    public function modificar()
    {
        $id_rol = $this->getId_rol();
        $permisos = $this->getPermisos();

        if(empty($id_rol) && empty($permisos)){
            return ["incompleto" => "El identificador del registro esta vacio, por favor verifique para continuar con el registro!"];
        }

        if (!(is_int($id_rol) && preg_match('/^[0-9]+$/', $id_rol))) {
            return ["invalido" => "El identificador del registro no tiene un formato valido!"];
        } 

        try {
            $conex = new conexion("usuario");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Roles";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM rol_permisos WHERE id_rol = :id");

            if(!($stmt->execute([":id" => $id_rol]))){
                $conex->rollBack();
                return ["error" => "Ah Ocurrido un Error con el Servidor!"];
            }

            if(count($permisos) > 0){
                for($i = 0; $i < count($permisos); $i++){
                    $permiso = $permisos[$i];
                    $resultado = $this->registrarPermisos($id_rol, $permiso, $conex);
    
                    if(!($resultado == true)){
                        $conex->rollBack();
                        return ["error" => "Ah Ocurrido un Error con el Servidor!"];
                    }
                }
            }
            else{
                return ["incompleto" => "El registro debe tener al menos un permiso de usuario!"];
            }
            
            $conex->commit();
            unset($this->conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar()
    {
        $id_rol = $this->getId_rol();

        if(empty($id_rol)){
            return ["incompleto" => "El identificador del registro no existe!"];
        }

        if(!(is_int($id_rol) && preg_match('/^[0-9]*$/',$id_rol))){
            return ["invalido" => "El identificador del registro no es valido!"];
        }

        try {
            $conex = new conexion("usuario");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Roles";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM roles WHERE idRol = :id");
            
            if(!($stmt->execute([":id" => $id_rol]))){
                $conex->rollBack();
                return ["error" => "Ah Ocurrido un Error con el Servidor!"];
            }

            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    
    public function getId_rol()
    {
        return $this->id_rol;
    }
    public function setId_rol($id)
    {
        $this->id_rol = $id;
    }

    public function getNombre_rol()
    {
        return $this->nombre_rol;
    }
    public function setNombre_rol($nombre)
    {
        $this->nombre_rol = $nombre;
    }

    public function getPermisos()
    {
        return $this->permisos;
    }
    public function setPermisos($permisos)
    {
        $this->permisos = $permisos;
    }
}