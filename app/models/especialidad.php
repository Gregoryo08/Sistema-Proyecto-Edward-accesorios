<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class especialidad extends conexion {
    
    private $id_especialidad;
    private $nombre_especialidad;

    public function __construct() {
        parent::__construct();
    }

    public function listar() {
       
        $conex = new conexion(); 

        $stmt = $conex->query("SELECT * FROM especialidades");
        $stmt->execute();

        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $array;
    }

    public function registrar() {
        $conex = new conexion();
        
        $nombre = $this->getNombre_especialidad();

        if(empty($nombre)) {
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!"];
        }

        if (strlen($nombre) < 4 || strlen($nombre) > 30) {
            return ["incompleto" => "El campo Nombre de la especialidad debe tener entre 4 y 30 caracteres."];
        }

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $nombre))) {
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Especialidad";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO especialidades (nombre_especialidad) VALUES (:n)");
            $stmt->bindParam(":n", $nombre);

            if(!($stmt->execute())) {
                unset($conex);
                return ["error" => "Ha ocurrido un error al Registrar la Especialidad"];
            }
            
            unset($conex);
            return true;
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function modificar() {
        $id = $this->getId_especialidad();
        $nombre = $this->getNombre_especialidad();

        if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "El ID de la Especialidad no es válido."];
        }

        if(empty($nombre)) {
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!"];
        }

        if (strlen($nombre) < 4 || strlen($nombre) > 30) {
            return ["incompleto" => "El campo Nombre de la especialidad debe tener entre 4 y 30 caracteres."];
        }

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u', $nombre))) {
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        $conex = new conexion();

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Especialidad";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $queryModificar = $conex->prepare("SELECT COUNT(*) FROM especialidades WHERE LOWER(nombre_especialidad) = LOWER(:nombre_especialidad) AND id_especialidad != :id_especialidad");
            $queryModificar->bindParam(":nombre_especialidad", $nombre, PDO::PARAM_STR);
            $queryModificar->bindParam(":id_especialidad", $id, PDO::PARAM_INT);
            $queryModificar->execute();
            
            if ($queryModificar->fetchColumn()) {
                unset($conex);
                return ["error" => "Esta Especialidad ya se encuentra registrada."];
            }

            $stmt = $conex->prepare("UPDATE especialidades SET nombre_especialidad = :n WHERE id_especialidad = :id");
            $stmt->bindParam(":n", $nombre);
            $stmt->bindParam(":id", $id);

            if(!($stmt->execute())) {
                unset($conex);
                return ["error" => "Ha ocurrido un Error al Modificar la Especialidad"];
            }
             
            unset($conex);
            return true;
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar() {
        $conex = new conexion();
        $id = $this->getId_especialidad();

        if (empty($id) || $id <= 0) {
            return ["invalido" => "El ID de la Especialidad debe ser un número positivo."];
        }

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Especialidad";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM especialidades WHERE id_especialidad = :id");
            $stmt->bindParam(":id", $id);
            
            if(!($stmt->execute())) {
                unset($conex);
                return ["error" => "Ha ocurrido un Error al Eliminar la Especialidad"];
            }
            
            unset($conex);
            return true;
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => "No se puede eliminar la especialidad porque está asociada a otros registros."];
        }
    }

    public function getId_especialidad() {
        return $this->id_especialidad;
    }

    public function setId_especialidad($id) {
        $this->id_especialidad = $id; 
    }

    public function getNombre_especialidad() {
        return $this->nombre_especialidad;
    }

    public function setNombre_especialidad($nombre) {
        $this->nombre_especialidad = $nombre;
    }
}