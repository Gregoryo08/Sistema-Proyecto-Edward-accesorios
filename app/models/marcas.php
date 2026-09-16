<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class marcas extends Conexion
{
    private $id_marca;
    private $nombre_marca;

    public function __construct()
    {
        parent::__construct();
    }

    public function procesarSolicitud($accion)
    {
        switch ($accion) {
            case 'consultar': return $this->consultarMarca();
            case 'registrar': return $this->registrar();
            case 'modificar': return $this->modificar();
            case 'eliminar': return $this->eliminar();
            default: return ["error" => "Acción no reconocida"];
        }
    }

    private function existeNombre($nombre, $id = null)
    {
        $conex = new conexion("sistema");
        
        $sql = "SELECT COUNT(*) FROM marcas WHERE nombre_marca = :n";
        if ($id) {
            $sql .= " AND id_marca != :id";
        }
        
        $stmt = $conex->prepare($sql);
        $params = [":n" => $nombre];
        if ($id) $params[":id"] = $id;

        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    private function registrar()
    {
        $nombre = $this->getNombre_marca();

        if (empty($nombre)) {
            return ["incompleto" => "Datos Incompletos", "input" => "nombre"];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s-]+$/', $nombre))) {
            return ["invalido" => "Formato del Nombre No Valido!", "input" => "nombre"];
        }

        
        if ($this->existeNombre($nombre)) {
            return ["invalido" => "¡La marca '{$nombre}' ya se encuentra registrada!", "input" => "nombre"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Marcas";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO marcas(nombre_marca) VALUES (:n)");

            if (!($stmt->execute([":n" => $nombre]))) {
                $conex->rollBack();
                return ["error" => "Ha ocurrido un error con el servidor!"];
            }

            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    private function modificar()
    {
        $nombre = $this->getNombre_marca();
        $id = $this->getId_marca();

        if (empty($nombre) || empty($id)) {
            $input = empty($nombre) ? "nombreModificar-" : "";
            $input .= empty($id) ? "id_marca-" : "";
            return ["incompleto" => "Datos Incompletos", "input" => $input];
        }

        
        if ($this->existeNombre($nombre, $id)) {
            return ["invalido" => "¡Ya existe otra marca con el nombre '{$nombre}'!", "input" => "nombreModificar"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Marcas";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("UPDATE marcas SET nombre_marca = :n WHERE id_marca = :id");

            if (!($stmt->execute([":n" => $nombre, ":id" => $id]))) {
                $conex->rollBack();
                return ["error" => "Ha ocurrido un error con el servidor!"];
            }

            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function listar()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->query("SELECT * FROM marcas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function consultarMarca()
    {
        $id = $this->getId_marca();
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT * FROM marcas WHERE id_marca = :id");

        if(!($stmt->execute([":id" => $id]))){
            return ["error" => "Ha ocurrido un error con el servidor!"];
        }
        
        $query = $stmt->fetch(PDO::FETCH_ASSOC);
        return $query;
    }

    private function eliminar()
    {
        $id = $this->getId_marca();
        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();
            
            $user = $_SESSION["username"];
            $modulo = "Administrar Marcas";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");
            
            $stmt = $conex->prepare("DELETE FROM marcas WHERE id_marca = :id");
            if (!($stmt->execute([":id" => $id]))) {
                $conex->rollBack();
                return ["error" => "No se pudo eliminar"];
            }
            
            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function getId_marca() { return $this->id_marca; }
    public function setId_marca($id) { $this->id_marca = $id; }
    public function getNombre_marca() { return $this->nombre_marca; }
    public function setNombre_marca($nombre) { $this->nombre_marca = $nombre; }
}