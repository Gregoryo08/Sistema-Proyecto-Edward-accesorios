<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class telefono extends Conexion
{
    private $id_telefono;
    private $marca; 
    private $modelo;
    private $almacenamiento;
    private $ram;
    private $imei;

    public function __construct()
    {
        parent::__construct();
    }

    public function registrar()
    {
        $marca = $this->getMarca();
        $modelo = $this->getModelo();
        $almacenamiento = $this->getAlmacenamiento();
        $ram = $this->getRam();
        $imei = $this->getImei();

        if (empty($marca) || empty($modelo) || empty($almacenamiento) || empty($ram) || empty($imei)) {
            $input = "";
            if(empty($marca)) $input .= "marca-";
            if(empty($modelo)) $input .= "modelo-";
            if(empty($almacenamiento)) $input .= "almacenamiento-";
            if(empty($ram)) $input .= "ram-";
            if(empty($imei)) $input .= "imei-";
            return ["incompleto" => "Datos Incompletos", "input" => $input];
        }

        try {
            $conex = new conexion("sistema");

            $check = $conex->prepare("SELECT imei FROM telefonos WHERE imei = :im LIMIT 1");
            $check->execute([":im" => $imei]);
            if ($check->fetch()) {
                return ["invalido" => "El IMEI ya se encuentra registrado en el sistema", "input" => "imei"];
            }

            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Telefonos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO telefonos(id_marca, modelo, almacenamiento, ram, imei) VALUES (:ma, :mo, :al, :ra, :im)");

            if (!($stmt->execute([
                ":ma" => $marca,
                ":mo" => $modelo,
                ":al" => $almacenamiento,
                ":ra" => $ram,
                ":im" => $imei
            ]))) {
                $conex->rollBack();
                return ["error" => "Ha ocurrido un error con el servidor!"];
            }

            $conex->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            if(isset($conex) && $conex->inTransaction()) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function listar()
    {
        try {
            $conex = new conexion("sistema");
            $stmt = $conex->query("SELECT t.*, m.nombre_marca 
                                 FROM telefonos t 
                                 INNER JOIN marcas m ON t.id_marca = m.id_marca");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function listarMarcas()
    {
        try {
            $conex = new conexion("sistema");
            $stmt = $conex->query("SELECT id_marca, nombre_marca FROM marcas ORDER BY nombre_marca ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function consultarTelefono()
    {
        try {
            $conex = new conexion("sistema");
            $stmt = $conex->prepare("SELECT * FROM telefonos WHERE id_telefono = :id");
            $stmt->execute([":id" => $this->getId_telefono()]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function modificar()
    {
        $id = $this->getId_telefono();
        $marca = $this->getMarca();
        $modelo = $this->getModelo();
        $almacenamiento = $this->getAlmacenamiento();
        $ram = $this->getRam();
        $imei = $this->getImei();

        if (empty($id) || empty($marca) || empty($modelo) || empty($almacenamiento) || empty($ram) || empty($imei)) {
            return ["incompleto" => "Datos Incompletos"];
        }

        try {
            $conex = new conexion("sistema");

            $check = $conex->prepare("SELECT id_telefono FROM telefonos WHERE imei = :im AND id_telefono != :id LIMIT 1");
            $check->execute([":im" => $imei, ":id" => $id]);
            if ($check->fetch()) {
                return ["invalido" => "Este IMEI ya está asignado a otro dispositivo", "input" => "imei_modificar"];
            }

            $conex->beginTransaction();
            $user = $_SESSION["username"];
            $modulo = "Administrar Telefonos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("UPDATE telefonos SET id_marca = :ma, modelo = :mo, almacenamiento = :al, ram = :ra, imei = :im WHERE id_telefono = :id");

            if (!($stmt->execute([
                ":ma" => $marca, 
                ":mo" => $modelo, 
                ":al" => $almacenamiento, 
                ":ra" => $ram, 
                ":im" => $imei, 
                ":id" => $id
            ]))) {
                $conex->rollBack();
                return ["error" => "Error al actualizar datos"];
            }

            $conex->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            if(isset($conex) && $conex->inTransaction()) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar()
    {
        $id = $this->getId_telefono();
        if (empty($id)) return ["error" => "ID no válido"];

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();
            
            $user = $_SESSION["username"];
            $modulo = "Administrar Telefonos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("DELETE FROM telefonos WHERE id_telefono = :id");
            if (!$stmt->execute([":id" => $id])) {
                $conex->rollBack();
                return ["error" => "No se pudo eliminar el registro"];
            }

            $conex->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            if(isset($conex) && $conex->inTransaction()) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function getId_telefono() { return $this->id_telefono; }
    public function setId_telefono($id) { $this->id_telefono = $id; }
    public function getMarca() { return $this->marca; }
    public function setMarca($marca) { $this->marca = $marca; }
    public function getModelo() { return $this->modelo; }
    public function setModelo($modelo) { $this->modelo = $modelo; }
    public function getAlmacenamiento() { return $this->almacenamiento; }
    public function setAlmacenamiento($almacenamiento) { $this->almacenamiento = $almacenamiento; }
    public function getRam() { return $this->ram; }
    public function setRam($ram) { $this->ram = $ram; }
    public function getImei() { return $this->imei; }
    public function setImei($imei) { $this->imei = $imei; }
}