<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class modulo extends conexion{
    
    private $id_modulo;
    private $nombre_modulo;

      public function __construct()
    {
        parent::__construct();
        
    }



    public function listar()
    {
        $conex = new conexion("usuario");

        $stmt = $conex->query("SELECT * FROM modulos");
        $stmt->execute();

        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $array;
    }





    public function registrar()
    {
        $conex = new conexion("usuario");
        
        $nombre = $this->getNombre_modulo();

        if(empty($nombre)){
            unset($conex);
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!"];
        }

        if (!is_string($nombre)) {
            return ["invalido" => "El nombre del Modulo debe ser solo texto."];
        }

        if (strlen($nombre) < 4 || strlen($nombre) > 30) {
            return ["incompleto" => "El campo Nombre del modulo debe tener entre 4 y 30 caracteres."];
        }

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            unset($conex);
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Modulos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO modulos (nombre_modulo) VALUES (:n)");
            $stmt->bindParam(":n",$nombre);

            if(!($stmt->execute())){
                unset($conex);
                return ["error" => "Ah Ocurrido un error al Registrar el Modulo"];
            }
            
            unset($conex);
            return true;
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }




    public function modificar()
    {

        $id = $this->getId_modulo();
        $nombre = $this->getNombre_modulo();

        if (empty($id)) {
            return ["incompleto" => "El ID del Modulo es obligatorio."];
        }

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return ["invalido" => "El ID del Modulo debe ser un número entero válido."];
        }

        if ($id <= 0) {
            return ["invalido" => "El ID del Modulo debe ser un número positivo."];
        }

        if(empty($nombre)){
            unset($this->conex);
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!"];
        }

        if (!is_string($nombre)) {
            return ["invalido" => "El nombre del Modulo debe ser solo texto."];
        }

        if (strlen($nombre) < 4 || strlen($nombre) > 30) {
            return ["incompleto" => "El campo Nombre del modulo debe tener entre 4 y 30 caracteres."];
        }

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            unset($this->conex);
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            unset($this->conex);
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        
        $conex = new conexion("usuario");

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Modulos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $queryModificar = $conex->prepare("SELECT COUNT(*) FROM modulos WHERE LOWER(nombre_modulo) = LOWER(:nombre_modulo) AND id_modulo != :id_modulo");
            $queryModificar->bindParam(":nombre_modulo", $nombre, PDO::PARAM_STR);
            $queryModificar->bindParam(":id_modulo", $id, PDO::PARAM_INT);
            $queryModificar->execute();
            
            if ($queryModificar->fetchColumn()) {
                $conex->rollBack();
                unset($this->conex);
                return ["error" => "Este Modulo ya se encuentra registrado."];
            }

            $stmt = $conex->prepare("UPDATE modulos SET nombre_modulo = :n WHERE id_modulo = :id");
            $stmt->bindParam(":n",$nombre);
            $stmt->bindParam(":id",$id);

            if(!($stmt->execute())){
                unset($conex);
                return ["error" => "Ah Ocurrido un Error al Modificar el nombre del Modulo"];
            }
             
            unset($conex);
            return true;
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar()
    {
        $conex = new conexion("usuario");

        $id = $this->getId_modulo();

        if (empty($id)) {
            return ["incompleto" => "El ID del Modulo es obligatorio."];
        }

        if ($id <= 0) {
            return ["invalido" => "El ID del Modulo debe ser un número positivo."];
        }

        if(!(is_int($id))){
            unset($this->conex);
            return ["error" => "El ID del Modulo debe ser un numero entero"];
        }

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Modulos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM modulos WHERE id_modulo = :id");
            $stmt->bindParam(":id",$id);
            
            if(!($stmt->execute())){
                unset($this->conex);
                return ["error" => "Ah Ocurrido un Error al Eliminar el Modulo"];
            }
            
            unset($this->conex);
            return true;
        } catch (PDOException $e) {
            unset($this->conex);
            return ["error" => $e->getMessage()];
        }
    }
    
    public function getId_modulo()
    {
        return $this->id_modulo;
    }
    public function setId_modulo($id)
    {
        $this->id_modulo = $id;
    }

    public function getNombre_modulo()
    {
        return $this->nombre_modulo;
    }
    public function setNombre_modulo($nombre)
    {
        $this->nombre_modulo = $nombre;
    }
}