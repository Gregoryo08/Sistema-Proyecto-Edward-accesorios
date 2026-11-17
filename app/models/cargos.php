<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class cargos extends conexion {
    private $id_cargo;
    private $nombre_cargo;

    public function listar()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT id_cargo, nombre_cargo FROM cargos");
        $stmt->execute();
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $array;
    }

    public function validar()
    {
        $nombre = $this->getNombre_cargo();
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT COUNT(*) as conteo FROM cargos WHERE nombre_cargo = :n");
        $stmt->bindParam(":n", $nombre);

        if(!($stmt->execute())){
            unset($conex);
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($conex);
        return $resultado["conteo"];
    }

    public function registrar()
    {
        $nombre = $this->getNombre_cargo();

        if(empty($nombre)){
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!", "input" => "cargo"];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            return ["invalido" => "Formato del Nombre No Valido!", "input" => "nombre"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Cargos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO cargos (nombre_cargo) VALUES (:n)");

            if(!($stmt->execute([":n" => $nombre]))){
                $conex->rollBack();
                unset($conex);
                return ["error" => "Ah Ocurrido un Error con el Servidor!"];
            }

            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            if(isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function modificar()
    {
        $id = $this->getId_cargo();
        $nombre = $this->getNombre_cargo();

        if(empty($id) || empty($nombre)){
            $input = empty($id) ? "id_cargo" : "cargo_modificar";
            return ["incompleto" => "Datos Incompletos", "input" => $input];
        }

        if(!(is_int($id))){
            return ["invalido" => "Error Con el ID: $id", "input" => "id_cargo"];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            return ["invalido" => "Formato del Nombre No Valido!", "input" => "cargo_modificar"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Cargos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("UPDATE cargos SET nombre_cargo = :n WHERE id_cargo = :id");
            $stmt->bindParam(":n", $nombre);
            $stmt->bindParam(":id", $id);

            if(!($stmt->execute())){
                $conex->rollBack();
                unset($conex);
                return ["error" => "Ah Ocurrido un Error con el Servidor!"];
            }

            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            if(isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar()
    {
        $id = $this->getId_cargo();

        if(empty($id) && !(is_int($id))){
            return ["error" => "Ah Ocurrido un Error con el Servidor!"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Cargos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM cargos WHERE id_cargo = :id");
            $stmt->bindParam(":id", $id);
            
            if(!($stmt->execute())){
                $conex->rollBack();
                unset($conex);
                return ["error" => "Ah Ocurrido un Error con el Servidor!"];
            }
            
            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            if(isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    
    public function getId_cargo()
    {
        return $this->id_cargo;
    }
    public function setId_cargo($id)
    {
        $this->id_cargo = $id;
    }

    public function getNombre_cargo()
    {
        return $this->nombre_cargo;
    }
    public function setNombre_cargo($nombre)
    {
        $this->nombre_cargo = $nombre;
    }
}