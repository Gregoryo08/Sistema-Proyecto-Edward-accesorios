<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class categoria extends conexion{
    
    private $id_categoria;
    private $nombre_categoria;

    public function __construct()
    {
        parent::__construct();
    }

    public function listar() {
        
        $conex = new conexion(); 

        $stmt = $conex->query("SELECT * FROM categorias");
        $stmt->execute();

        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $array;
    }

    public function registrar() {
        $conex = new conexion();
        
        $nombre = $this->getNombre_categoria();

        if(empty($nombre)){
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!"];
        }

        if (strlen($nombre) < 4 || strlen($nombre) > 30) {
            return ["incompleto" => "El Nombre de la categoria debe tener entre 4 y 30 caracteres."];
        }

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Categorias";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO categorias (nombre_categoria) VALUES (:n)");
            $stmt->bindParam(":n", $nombre);

            if(!($stmt->execute())){
                unset($conex);
                return ["error" => "Ah Ocurrido un error al Registrar la Categoria"];
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
        $id = $this->getId_categoria();
        $nombre = $this->getNombre_categoria();

        if (empty($id)) {
            return ["incompleto" => "El ID de la Categoria es obligatorio."];
        }

        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return ["invalido" => "El ID de la Categoria debe ser un número entero válido."];
        }

        if ($id <= 0) {
            return ["invalido" => "El ID de la Categoria debe ser un número positivo."];
        }

        if(empty($nombre)){
            unset($this->conex);
            return ["incompleto" => "Debe Ingresar un dato para poder Continuar!"];
        }

        if (!is_string($nombre)) {
            return ["invalido" => "El nombre de la Categoria debe ser solo texto."];
        }

        if (strlen($nombre) < 4 || strlen($nombre) > 30) {
            return ["incompleto" => "El campo Nombre de la categoria debe tener entre 4 y 30 caracteres."];
        }

        if (!(preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre))) {
            unset($this->conex);
            return ["invalido" => "El Nombre '{$nombre}' no es Valido!"];
        } 

        $conex = new conexion("");

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Categorias";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $queryModificar = $conex->prepare("SELECT COUNT(*) FROM categorias WHERE LOWER(nombre_categoria) = LOWER(:nombre_categoria) AND id_categoria != :id_categoria");
            $queryModificar->bindParam(":nombre_categoria", $nombre, PDO::PARAM_STR);
            $queryModificar->bindParam(":id_categoria", $id, PDO::PARAM_INT);
            $queryModificar->execute();
            
            if ($queryModificar->fetchColumn()) {
                $conex->rollBack();
                unset($this->conex);
                return ["error" => "Esta Categoria ya se encuentra registrada."];
            }

            $stmt = $conex->prepare("UPDATE categorias SET nombre_categoria = :n WHERE id_categoria = :id");
            $stmt->bindParam(":n",$nombre);
            $stmt->bindParam(":id",$id);

            if(!($stmt->execute())){
                unset($conex);
                return ["error" => "Ah Ocurrido un Error al Modificar el nombre de la Categoria"];
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
        $id = $this->getId_categoria();

        if (empty($id) || $id <= 0) {
            return ["invalido" => "ID no válido."];
        }

        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Categorias";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM categorias WHERE id_categoria = :id");
            $stmt->bindParam(":id", $id);
            
            if(!($stmt->execute())){
                unset($conex);
                return ["error" => "Error al Eliminar la Categoria"];
            }
            
            unset($conex);
            return true;
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => "No se puede eliminar: Esta categoría podría estar siendo usada por una marca."];
        }
    }
    
    public function getId_categoria()
    {
        return $this->id_categoria;
    }
    public function setId_categoria($id)
    {
        $this->id_categoria = $id;
    }

    public function getNombre_categoria()
    {
        return $this->nombre_categoria;
    }
    public function setNombre_categoria($nombre)
    {
        $this->nombre_categoria = $nombre;
    }
}