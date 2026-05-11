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

    // Los métodos CRUD ahora usan la conexión establecida en el constructor ($this)

    public function registrar($sql, $tipo, $parametros)
    {
        // Usa $this->prepare ya que Persona es un objeto PDO (a través de Conexion)
        $stmt = $this->prepare($sql);

        if ($tipo == "empleado") {
            $user = $_SESSION["username"];
            $modulo = "Administrar Empleados";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

        } else if ($tipo == "cliente") {
            $user = $_SESSION["username"];
            $modulo = "Administrar Clientes";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
        } else {
            return false;
        }

        if ($stmt->execute($parametros)) {
            return true;
        } else {
            return false;
        }
    }

    public function datos()
    {
        $id = $this->getCedula();

        // Usa $this->prepare
        $stmt = $this->prepare("SELECT * FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE e.cedula_empleado != :id and e.estado = 'activo'");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultado;
    }

    public function modificar($sql, $tipo, $parametros)
    {
        // Usa $this->prepare
        $stmt = $this->prepare($sql);

        if ($tipo == "empleado") {
            $user = $_SESSION["username"];
            $modulo = "Administrar Empleados";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

        } else if ($tipo == "cliente") {
            $user = $_SESSION["username"];
            $modulo = "Administrar Clientes";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
        } else {
            return false;
        }

        if ($stmt->execute($parametros)) {
            return true;
        } else {
            return false;
        }
    }

    public function eliminar($sql, $parametros, $tipo)
    {
        // Usa $this->prepare
        $stmt = $this->prepare($sql);

        if($tipo == "empleado"){
            $user = $_SESSION["username"];
            $modulo = "Administrar Empleados";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
        }
        else{
            $user = $_SESSION["username"];
            $modulo = "Administrar Clientes";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
        }

        if (!($stmt->execute($parametros))) {
            return false;
        }

        return true;
    }

    // --- Getters y Setters ---

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function getCedula()
    {
        return $this->cedula;
    }
    public function setCedula($cedula)
    {
        $this->cedula = $cedula;
    }
    public function getApellido()
    {
        return $this->apellido;
    }
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }
    public function getSexo()
    {
        return $this->sexo;
    }
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }
    public function getCel()
    {
        return $this->telefono;
    }
    public function setCel($cel)
    {
        $this->telefono = $cel;
    }
    public function getDireccion()
    {
        return $this->direccion;
    }
    public function setDireccion($dir)
    {
        $this->direccion = $dir;
    }
    public function getCorreo()
    {
        return $this->correo;
    }
    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }
    public function getCargo()
    {
        return $this->cargos;
    }
    public function setCargo($cargo)
    {
        $this->cargos = $cargo;
    }

    public function getEdad()
    {
        return $this->edad;
    }
    public function setEdad($edad)
    {
        $this->edad = $edad;
    }

    public function getEstado()
    {
        return $this->estado;
    }
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}