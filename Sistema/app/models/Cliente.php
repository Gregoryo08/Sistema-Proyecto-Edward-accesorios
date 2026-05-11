<?php

namespace App\Sistema\models; 

use App\Sistema\models\Persona;
use \PDO;
use \DateTime;
use \PDOException;

class Cliente extends Persona
{
    private $ingresos_mensuales;
    private $tipo_residencia;
    private $carga_familiar;
    private $estado_civil;
    private $profesion;
    private $ocupacion;
    private $score_credito;

    public function __construct()
    {
        parent::__construct();
    }

    public function setIngresos($valor) { $this->ingresos_mensuales = $valor; }
    public function getIngresos() { return $this->ingresos_mensuales; }
    
    public function setResidenciaTipo($valor) { $this->tipo_residencia = $valor; }
    public function getResidenciaTipo() { return $this->tipo_residencia; }

    public function setCargaFamiliar($valor) { $this->carga_familiar = $valor; }
    public function getCargaFamiliar() { return $this->carga_familiar; }

    public function setEstadoCivil($valor) { $this->estado_civil = $valor; }
    public function getEstadoCivil() { return $this->estado_civil; }

    public function setProfesion($valor) { $this->profesion = $valor; }
    public function getProfesion() { return $this->profesion; }

    public function setOcupacion($valor) { $this->ocupacion = $valor; }
    public function getOcupacion() { return $this->ocupacion; }

    public function setScore($valor) { $this->score_credito = $valor; }
    public function getScore() { return $this->score_credito; }

    public function validarCedula()
    {
        $cedula = $this->getCedula();
        $stmt = $this->prepare("SELECT COUNT(*) as conteo FROM clientes WHERE cedula_cliente = :c");
        if (!($stmt->execute([":c" => $cedula]))) {
            return ['error' => "Error con el servidor al validar cédula!"];
        }
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        return $array["conteo"];
    }

    public function datosClientesActivos()
    {
        $stmt = $this->prepare("SELECT * FROM clientes WHERE estado = 'activo' ORDER BY nombre ASC");
        if (!($stmt->execute())) {
            return ["error" => "Error al cargar clientes activos!"];
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultaInactivos()
    {
        $stmt = $this->prepare("SELECT * FROM clientes WHERE estado = 'inactivo' ORDER BY nombre ASC");
        if (!($stmt->execute())) {
            return ["error" => "Error al cargar clientes inactivos!"];
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarCliente()
    {
        $cedula = $this->getCedula();
        if (empty($cedula)) return ["incompleto" => "ID vacío!"];
        $stmt = $this->prepare("SELECT * FROM clientes WHERE cedula_cliente = :c");
        if (!($stmt->execute([":c" => $cedula]))) {
            return ["error" => "Error al consultar cliente!"];
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registroCliente()
    {
        $cedula    = $this->getCedula();
        $nombre    = $this->getNombre();
        $apellido  = $this->getApellido();
        $telefono  = $this->getCel();
        $direccion = $this->getDireccion();
        $correo    = $this->getCorreo();
        $sexo      = $this->getSexo();
        $fecha     = $this->getEdad();
        
        $ingresos   = $this->getIngresos() ?? 0;
        $t_res      = $this->getResidenciaTipo() ?? 'No especificado';
        $c_fam      = $this->getCargaFamiliar() ?? 0;
        $e_civil    = $this->getEstadoCivil() ?? 'Soltero/a';
        $prof       = $this->getProfesion() ?? 'No especificado';
        $ocup       = $this->getOcupacion() ?? 'No especificado';

        if (empty($cedula) || empty($nombre) || empty($apellido)) {
            return ["incompleto" => "Datos obligatorios faltantes."];
        }

        try {
            $this->beginTransaction();
            $user = $_SESSION["username"];
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Clientes'");

            $sql = "INSERT INTO clientes (cedula_cliente, nombre, apellido, telefono, residencia, correo, sexo, fecha_nacimiento, 
                    tipo_residencia, carga_familiar, estado_civil, profesion, ocupacion, ingresos_mensuales, score_credito, estado) 
                    VALUES (:cedula, :nombre, :apellido, :telefono, :direccion, :correo, :sexo, :fecha, 
                    :t_res, :c_fam, :e_civil, :prof, :ocup, :ingresos, 5, 'activo')";

            $parametros = [
                ":cedula"    => $cedula,
                ":nombre"    => $nombre,
                ":apellido"  => $apellido,
                ":telefono"  => $telefono,
                ":direccion" => $direccion,
                ":correo"    => $correo,
                ":sexo"      => $sexo,
                ":fecha"     => $fecha,
                ":t_res"     => $t_res,
                ":c_fam"     => $c_fam,
                ":e_civil"   => $e_civil,
                ":prof"      => $prof,
                ":ocup"      => $ocup,
                ":ingresos"  => $ingresos
            ];

            if (!($this->registrar($sql, "cliente", $parametros))) {
                $this->rollBack();
                return ["error" => "Error en el registro."];
            }

            $this->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->inTransaction()) $this->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function ModificarCliente()
    {
        $cedula    = $this->getCedula();
        $nombre    = $this->getNombre();
        $apellido  = $this->getApellido();
        $telefono  = $this->getCel();
        $direccion = $this->getDireccion();
        $correo    = $this->getCorreo();
        $sexo      = $this->getSexo();
        
        $ingresos   = $this->getIngresos() ?? 0;
        $t_res      = $this->getResidenciaTipo() ?? 'No especificado';
        $c_fam      = $this->getCargaFamiliar() ?? 0;
        $e_civil    = $this->getEstadoCivil() ?? 'Soltero/a';
        $prof       = $this->getProfesion() ?? 'No especificado';
        $ocup       = $this->getOcupacion() ?? 'No especificado';

        try {
            $this->beginTransaction();
            $user = $_SESSION["username"];
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Clientes'");

            $sql = "UPDATE clientes SET nombre=:nombre, apellido=:apellido, correo=:correo, telefono=:telefono, 
                    sexo=:sexo, residencia=:direccion, ingresos_mensuales=:ingresos, tipo_residencia=:t_res, 
                    carga_familiar=:c_fam, estado_civil=:e_civil, profesion=:prof, ocupacion=:ocup 
                    WHERE cedula_cliente = :cedula";

            $parametros = [
                ":nombre"    => $nombre,
                ":apellido"  => $apellido,
                ":correo"    => $correo,
                ":telefono"  => $telefono,
                ":sexo"      => $sexo,
                ":direccion" => $direccion,
                ":ingresos"  => $ingresos,
                ":t_res"     => $t_res,
                ":c_fam"     => $c_fam,
                ":e_civil"   => $e_civil,
                ":prof"      => $prof,
                ":ocup"      => $ocup,
                ":cedula"    => $cedula
            ];

            if (!($this->modificar($sql, "cliente", $parametros))) {
                $this->rollBack();
                return ["error" => "Error al modificar."];
            }

            $this->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->inTransaction()) $this->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminarClientes($estado)
    {
        $id = $this->getCedula();
        try {
            $this->beginTransaction();
            $user = $_SESSION["username"];
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Clientes'");

            $sql = "UPDATE clientes SET estado = :estado WHERE cedula_cliente = :cedula";
            $parametros = [":estado" => $estado, ":cedula" => $id];

            if (!($this->eliminar($sql, $parametros, "cliente"))) {
                $this->rollBack();
                return ["error" => "Error al cambiar estado."];
            }
            $this->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->inTransaction()) $this->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
}