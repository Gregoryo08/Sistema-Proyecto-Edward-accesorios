<?php
namespace App\Sistema\models;

use App\Sistema\models\Persona;
use \PDO;
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

    public function __construct() { parent::__construct(); }

    public function procesarSolicitud($accion, $datos = [])
    {
        switch ($accion) {
            case 'validarCedula': return $this->validarCedula();
            case 'consultar': return $this->consultarCliente();
            case 'registrar': return $this->registroCliente();
            case 'registrarPerfil': return $this->registrarPerfilFinanciero();
            case 'modificar': return $this->ModificarCliente();
            case 'eliminar': return $this->eliminarClientes($datos['estado'] ?? null);
            default: return ["error" => "Acción no reconocida"];
        }
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

    private function validarCedula()
    {
        $cedula = $this->getCedula();
        $stmt = $this->prepare("SELECT COUNT(*) as conteo FROM clientes WHERE cedula_persona = :c");
        if (!($stmt->execute([":c" => $cedula]))) return ['error' => "Error al validar!"];
        return $stmt->fetch(PDO::FETCH_ASSOC)["conteo"];
    }

   public function datosClientes()
{
   
    $sql = "SELECT p.cedula_persona, p.nombre, p.apellido, p.sexo, p.telefono, p.correo, p.fecha_nacimiento, p.direccion,
                   c.estado, 
                   pf.tipo_residencia, pf.carga_familiar, pf.estado_civil, pf.profesion, 
                   pf.ocupacion, pf.ingresos_mensuales, pf.score_credito 
            FROM persona p
            INNER JOIN clientes c ON p.cedula_persona = c.cedula_persona
            LEFT JOIN perfiles_financiamiento pf ON p.cedula_persona = pf.cedula_persona 
            ORDER BY p.nombre ASC";
            
    return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

    private function registroCliente()
    {
        $cedula = $this->getCedula();
        if (!preg_match('/^\d{10,11}$/', (string) $this->getCel())) {
            return ["error" => "El teléfono debe tener entre 6 y 7 dígitos después de la operadora."];
        }

        $stmtExiste = $this->prepare("SELECT COUNT(*) FROM persona WHERE cedula_persona = :cedula");
        $stmtExiste->execute([":cedula" => $cedula]);
        if ((int) $stmtExiste->fetchColumn() > 0) {
            return ["error" => "Esta cédula ya está registrada."];
        }
        
        try {
            $this->beginTransaction();
            
            $sqlPersona = "INSERT INTO persona (cedula_persona, nombre, apellido, telefono, correo, sexo, fecha_nacimiento, direccion) 
                           VALUES (:cedula, :nombre, :apellido, :telefono, :correo, :sexo, :fecha, :direccion)";
            $stmtPersona = $this->prepare($sqlPersona);
            $stmtPersona->execute([
                ":cedula" => $cedula, ":nombre" => $this->getNombre(), ":apellido" => $this->getApellido(),
                ":telefono" => $this->getCel(), ":correo" => $this->getCorreo(), 
                ":sexo" => $this->getSexo(), ":fecha" => $this->getEdad(), ":direccion" => $this->getDireccion()
            ]);

            $sqlCliente = "INSERT INTO clientes (cedula_persona, estado) VALUES (:cedula, 'activo')";
$this->prepare($sqlCliente)->execute([":cedula" => $cedula]);

            $sqlPerfil = "INSERT INTO perfiles_financiamiento (cedula_persona, tipo_residencia, carga_familiar, estado_civil, profesion, ocupacion, ingresos_mensuales, score_credito) 
                          VALUES (:cedula, :t_res, :c_fam, :e_civil, :prof, :ocup, :ingresos, 5)";
            $this->prepare($sqlPerfil)->execute([
                ":cedula" => $cedula, ":t_res" => $this->getResidenciaTipo() ?? 'Familiar',
                ":c_fam" => $this->getCargaFamiliar() ?? 0, ":e_civil" => $this->getEstadoCivil() ?? 'Soltero',
                ":prof" => $this->getProfesion() ?? 'Empleado', ":ocup" => $this->getOcupacion() ?? '',
                ":ingresos" => $this->getIngresos() ?? 0
            ]);

            $this->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->inTransaction()) $this->rollBack();
            if ((int) $e->getCode() === 23000) {
                return ["error" => "Esta cédula ya está registrada."];
            }
            return ["error" => $e->getMessage()];
        }
    }

    private function registrarPerfilFinanciero()
{
    $cedula = $this->getCedula();
    try {
        $sql = "INSERT INTO perfiles_financiamiento 
                (cedula_persona, tipo_residencia, carga_familiar, estado_civil, profesion, ocupacion, ingresos_mensuales, score_credito) 
                VALUES (:cedula, :t_res, :c_fam, :e_civil, :prof, :ocup, :ingresos, 5)";
        
        $stmt = $this->prepare($sql);
        $stmt->execute([
            ":cedula"   => $cedula,
            ":t_res"    => $this->getResidenciaTipo() ?? 'Familiar',
            ":c_fam"    => $this->getCargaFamiliar() ?? 0,
            ":e_civil"  => $this->getEstadoCivil() ?? 'Soltero',
            ":prof"     => $this->getProfesion() ?? 'Empleado',
            ":ocup"     => $this->getOcupacion() ?? '',
            ":ingresos" => $this->getIngresos() ?? 0
        ]);
        
        return true;
    } catch (PDOException $e) {
        return ["error" => $e->getMessage()];
    }
}

    private function ModificarCliente()
{
    $cedula = $this->getCedula();
    if (!preg_match('/^\d{10,11}$/', (string) $this->getCel())) {
        return ["error" => "El teléfono debe tener entre 6 y 7 dígitos después de la operadora."];
    }
    try {
        $this->beginTransaction();
        
        $sqlPersona = "UPDATE persona SET nombre=:nombre, apellido=:apellido, correo=:correo, telefono=:telefono, sexo=:sexo, direccion=:direccion WHERE cedula_persona = :cedula";
        $this->prepare($sqlPersona)->execute([
            ":nombre" => $this->getNombre(), ":apellido" => $this->getApellido(),
            ":correo" => $this->getCorreo(), ":telefono" => $this->getCel(),
            ":sexo" => $this->getSexo(), ":direccion" => $this->getDireccion(), ":cedula" => $cedula
        ]);

       

        $sqlPerfil = "UPDATE perfiles_financiamiento SET tipo_residencia=:t_res, carga_familiar=:c_fam, estado_civil=:e_civil, profesion=:prof, ocupacion=:ocup, ingresos_mensuales=:ingresos WHERE cedula_persona = :cedula";
        $this->prepare($sqlPerfil)->execute([
            ":t_res" => $this->getResidenciaTipo(), ":c_fam" => $this->getCargaFamiliar(),
            ":e_civil" => $this->getEstadoCivil(), ":prof" => $this->getProfesion(),
            ":ocup" => $this->getOcupacion(), ":ingresos" => $this->getIngresos(), ":cedula" => $cedula
        ]);

        $this->commit();
        return true;
    } catch (PDOException $e) {
        if ($this->inTransaction()) $this->rollBack();
        if ((int) $e->getCode() === 23000) {
            return ["error" => "Esta cédula ya está registrada."];
        }
        return ["error" => $e->getMessage()];
    }
}

    private function eliminarClientes($estado)
    {
        $cedula = $this->getCedula();
        try {
            $this->beginTransaction();
            $user = $_SESSION["username"];
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Clientes'");

            $sql = "UPDATE clientes SET estado = :estado WHERE cedula_persona = :cedula";
            $stmt = $this->prepare($sql);
            
            if (!($stmt->execute([":estado" => $estado, ":cedula" => $cedula]))) {
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

    private function consultarCliente()
    {
        $cedula = $this->getCedula();
        if (empty($cedula)) return ["incompleto" => "ID vacío!"];
        $stmt = $this->prepare("SELECT p.*, c.estado, pf.tipo_residencia, pf.carga_familiar, pf.estado_civil, pf.profesion, pf.ocupacion, pf.ingresos_mensuales, pf.score_credito 
                                FROM persona p 
                                INNER JOIN clientes c ON p.cedula_persona = c.cedula_persona 
                                LEFT JOIN perfiles_financiamiento pf ON p.cedula_persona = pf.cedula_persona 
                                WHERE p.cedula_persona = :c");
        if (!($stmt->execute([":c" => $cedula]))) {
            return ["error" => "Error al consultar cliente!"];
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}