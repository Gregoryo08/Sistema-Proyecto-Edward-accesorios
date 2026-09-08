<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class proveedores extends Conexion
{
    private $rif_proveedor;
    private $nombre_proveedor;
    private $telefono_proveedor;
    private $correo_proveedor;
    private $ubicacion_proveedor;

    public function __construct()
    {
        parent::__construct();
    }

    public function existeRif($rif, $id = null)
    {
        $conex = new Conexion("sistema");
        $sql = "SELECT COUNT(*) FROM proveedores WHERE rif_proveedor = :rif";
        if ($id) {
            $sql .= " AND rif_proveedor != :id";
        }
        
        $stmt = $conex->prepare($sql);
        $params = [":rif" => $rif];
        if ($id) $params[":id"] = $id;

        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function registrar()
    {
        // Validaciones de campos
        if (empty($this->rif_proveedor)) {
            return ["error" => "El Rif del proveedor es obligatorio."];
        }
        if (!preg_match('/^[VEPJG]-\d{6,9}$/', $this->rif_proveedor)) {
            return ["error" => "El Rif no tiene un formato válido. Ejemplo: J-12345678"];
        }

        if (empty($this->nombre_proveedor)) {
            return ["error" => "El nombre del proveedor es obligatorio."];
        }
        if (!is_string($this->nombre_proveedor)) {
            return ["error" => "El nombre debe ser solo texto."];
        }
        if (strlen($this->nombre_proveedor) < 1 || strlen($this->nombre_proveedor) > 50) {
            return ["error" => "El nombre debe tener entre 1 y 50 caracteres."];
        }

        if (empty($this->telefono_proveedor)) {
            return ["error" => "El número de teléfono es obligatorio."];
        }
        if (!preg_match('/^\d{11}$/', $this->telefono_proveedor)) {
            return ["error" => "El teléfono debe contener 11 dígitos."];
        }

        if (empty($this->correo_proveedor)) {
            return ["error" => "El correo electrónico es obligatorio."];
        }
        if (!filter_var($this->correo_proveedor, FILTER_VALIDATE_EMAIL)) {
            return ["error" => "El correo electrónico no es válido."];
        }

        if (empty($this->ubicacion_proveedor)) {
            return ["error" => "La ubicación del proveedor es obligatoria."];
        }

        // Verificar RIF duplicado
        if ($this->existeRif($this->rif_proveedor)) {
            return ["error" => "Ya existe un proveedor con este Rif."];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Proveedores";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $sql = "INSERT INTO proveedores(rif_proveedor, nombre_proveedor, telefono_proveedor, correo_proveedor, ubicacion_proveedor) 
                    VALUES (:rif, :nom, :tel, :cor, :ubi)";
            $stmt = $conex->prepare($sql);

            $stmt->bindParam(":rif", $this->rif_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":nom", $this->nombre_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":tel", $this->telefono_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":cor", $this->correo_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":ubi", $this->ubicacion_proveedor, PDO::PARAM_STR);

            $stmt->execute();

            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => "Error al registrar el proveedor."];
        }
    }

    public function modificar()
    {
        // Validaciones de campos
        if (empty($this->rif_proveedor)) {
            return ["error" => "El Rif del proveedor es obligatorio."];
        }
        if (!preg_match('/^[VEPJG]-\d{6,9}$/', $this->rif_proveedor)) {
            return ["error" => "El Rif no tiene un formato válido. Ejemplo: J-12345678"];
        }

        if (empty($this->nombre_proveedor)) {
            return ["error" => "El nombre del proveedor es obligatorio."];
        }
        if (!is_string($this->nombre_proveedor)) {
            return ["error" => "El nombre debe ser solo texto."];
        }
        if (strlen($this->nombre_proveedor) < 1 || strlen($this->nombre_proveedor) > 50) {
            return ["error" => "El nombre debe tener entre 1 y 50 caracteres."];
        }

        if (empty($this->telefono_proveedor)) {
            return ["error" => "El número de teléfono es obligatorio."];
        }
        if (!preg_match('/^\d{11}$/', $this->telefono_proveedor)) {
            return ["error" => "El teléfono debe contener 11 dígitos."];
        }

        if (empty($this->correo_proveedor)) {
            return ["error" => "El correo electrónico es obligatorio."];
        }
        if (!filter_var($this->correo_proveedor, FILTER_VALIDATE_EMAIL)) {
            return ["error" => "El correo electrónico no es válido."];
        }

        if (empty($this->ubicacion_proveedor)) {
            return ["error" => "La ubicación del proveedor es obligatoria."];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Proveedores";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $sql = "UPDATE proveedores SET 
                    nombre_proveedor = :nom, 
                    telefono_proveedor = :tel, 
                    correo_proveedor = :cor, 
                    ubicacion_proveedor = :ubi
                    WHERE rif_proveedor = :rif";
            
            $stmt = $conex->prepare($sql);
            $stmt->bindParam(":rif", $this->rif_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":nom", $this->nombre_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":tel", $this->telefono_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":cor", $this->correo_proveedor, PDO::PARAM_STR);
            $stmt->bindParam(":ubi", $this->ubicacion_proveedor, PDO::PARAM_STR);

            $stmt->execute();

            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => "Error al modificar el proveedor."];
        }
    }

    public function eliminar()
    {
        if (empty($this->rif_proveedor)) {
            return ["error" => "El Rif del proveedor es obligatorio."];
        }
        if (!preg_match('/^[VEPJG]-\d{6,9}$/', $this->rif_proveedor)) {
            return ["error" => "El Rif no tiene un formato válido. Ejemplo: J-12345678"];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();
            
            $user = $_SESSION["username"];
            $modulo = "Administrar Proveedores";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");
            
            $stmt = $conex->prepare("DELETE FROM proveedores WHERE rif_proveedor = :rif");
            $stmt->bindParam(":rif", $this->rif_proveedor, PDO::PARAM_STR);
            $stmt->execute();
            
            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => "Error al eliminar el proveedor."];
        }
    }

    public function listar()
    {
        $conex = new Conexion("sistema");
        $stmt = $conex->prepare("SELECT * FROM proveedores ORDER BY rif_proveedor DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Getters y Setters
    public function getRif_proveedor() { return $this->rif_proveedor; }
    public function setRif_proveedor($rif) { $this->rif_proveedor = $rif; }
    public function getNombre_proveedor() { return $this->nombre_proveedor; }
    public function setNombre_proveedor($nombre) { $this->nombre_proveedor = $nombre; }
    public function getTelefono_proveedor() { return $this->telefono_proveedor; }
    public function setTelefono_proveedor($telefono) { $this->telefono_proveedor = $telefono; }
    public function getCorreo_proveedor() { return $this->correo_proveedor; }
    public function setCorreo_proveedor($correo) { $this->correo_proveedor = $correo; }
    public function getUbicacion_proveedor() { return $this->ubicacion_proveedor; }
    public function setUbicacion_proveedor($ubicacion) { $this->ubicacion_proveedor = $ubicacion; }
}