<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;

class Persona extends Conexion
{
    private $cedula;
    private $id;
    private $nombre;
    private $apellido;
    private $correo;
    private $direccion;
    private $telefono;
    private $edad;
    private $sexo;
    private $cargos;
    private $estado;

    public function __construct()
    {
        parent::__construct("sistema");
    }

    public function registrar($sql, $tipo, $parametros)
    {
        $stmt = $this->prepare($sql);

        if ($tipo == "empleado" || $tipo == "cliente") {
            $user = $_SESSION["username"];
            $modulo = ($tipo == "empleado") ? "Administrar Empleados" : "Administrar Clientes";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
        } else {
            return false;
        }

        return $stmt->execute($parametros);
    }

    public function datos()
    {
        $id = $this->getCedula();
        $stmt = $this->prepare("SELECT p.*, e.id_cargo, c.nombre_cargo 
                                FROM persona p 
                                INNER JOIN empleados e ON p.cedula_persona = e.cedula_persona 
                                INNER JOIN cargos c ON e.id_cargo = c.id_cargo 
                                WHERE p.cedula_persona != :id AND e.estado = 'activo'");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function modificar($sql, $tipo, $parametros)
    {
        $stmt = $this->prepare($sql);

        if ($tipo == "empleado" || $tipo == "cliente") {
            $user = $_SESSION["username"];
            $modulo = ($tipo == "empleado") ? "Administrar Empleados" : "Administrar Clientes";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
        } else {
            return false;
        }

        return $stmt->execute($parametros);
    }

    public function eliminar($sql, $parametros, $tipo)
    {
        $stmt = $this->prepare($sql);
        $user = $_SESSION["username"];
        $modulo = ($tipo == "empleado") ? "Administrar Empleados" : "Administrar Clientes";
        $this->exec("SET @usuario_actual = '{$user}'");
        $this->exec("SET @modulo = '{$modulo}'");

        return $stmt->execute($parametros);
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getCedula() { return $this->cedula; }
    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function getApellido() { return $this->apellido; }
    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function getSexo() { return $this->sexo; }
    public function setSexo($sexo) { $this->sexo = $sexo; }
    public function getCel() { return $this->telefono; }
    public function setCel($cel) { $this->telefono = $cel; }
    public function getDireccion() { return $this->direccion; }
    public function setDireccion($dir) { $this->direccion = $dir; }
    public function getCorreo() { return $this->correo; }
    public function setCorreo($correo) { $this->correo = $correo; }
    public function getCargo() { return $this->cargos; }
    public function setCargo($cargo) { $this->cargos = $cargo; }
    public function getEdad() { return $this->edad; }
    public function setEdad($edad) { $this->edad = $edad; }
    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}