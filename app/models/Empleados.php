<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use App\Sistema\Models\Persona;
use \PDO;
use \PDOException;

class Empleados extends Persona
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validarCedula()
    {
        $cedula = $this->getCedula();
        $conex = new Conexion("sistema");
        $stmt = $conex->prepare("SELECT COUNT(*) as conteo FROM empleados WHERE cedula_persona = :c");
        $stmt->execute([":c" => $cedula]);
        $array = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($conex);
        return $array["conteo"];
    }

    

    public function consultaSuspendidos() {
        try {
            $conex = new Conexion("sistema");
            
            $sql = "SELECT e.cedula_persona, p.nombre, p.apellido, c.nombre_cargo 
                    FROM empleados e 
                    LEFT JOIN persona p ON e.cedula_persona = p.cedula_persona 
                    LEFT JOIN cargos c ON e.id_cargo = c.id_cargo 
                    WHERE e.perfil = 'suspendido'";
            
            $stmt = $conex->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            unset($conex);
            return $resultado ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

   public function consultaPerfiles() {
    try {
        $conex = new Conexion("sistema");
        
        $sql = "SELECT e.cedula_persona, p.nombre, p.apellido, c.nombre_cargo, e.perfil 
                FROM empleados e 
                LEFT JOIN persona p ON e.cedula_persona = p.cedula_persona 
                LEFT JOIN cargos c ON e.id_cargo = c.id_cargo 
                WHERE e.perfil != 'suspendido' OR e.perfil IS NULL";
        
        $stmt = $conex->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $resultado ?: [];
    } catch (PDOException $e) {
        return [];
    }
}

public function listarEmpleados()
{
    try {
        $conex = new Conexion("sistema");
        
        $sql = "SELECT e.*, p.nombre, p.apellido, p.correo, p.telefono, 
                       p.fecha_nacimiento, p.sexo, p.direccion, c.nombre_cargo, e.perfil 
                FROM empleados e 
                INNER JOIN persona p ON e.cedula_persona = p.cedula_persona 
                INNER JOIN cargos c ON e.id_cargo = c.id_cargo";
        
        $stmt = $conex->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $resultado ?: [];
    } catch (PDOException $e) {
        return [];
    }
}


public function obtenerDatosUsuario()
{
    $cedula = $this->getCedula();
    if (empty($cedula)) {
        return [];
    }

    try {
        $conex = new Conexion("sistema");
        
$sql = "SELECT p.nombre, p.apellido, p.cedula_persona, p.telefono, 
               p.correo, p.direccion, p.fecha_nacimiento, 
               c.nombre_cargo, c.id_cargo
        FROM persona p
        LEFT JOIN empleados e ON p.cedula_persona = e.cedula_persona
        LEFT JOIN cargos c ON e.id_cargo = c.id_cargo
        WHERE p.cedula_persona = :c";
        
        $stmt = $conex->prepare($sql);
        $stmt->execute([":c" => $cedula]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        unset($conex);
        return $resultado ?: [];
    } catch (PDOException $e) {
        error_log("Error en obtenerDatosUsuario: " . $e->getMessage());
        return [];
    }
}


    public function consultar()
    {
        $cedula = $this->getCedula();
        if (empty($cedula)) {
            return false;
        }

        try {
            $conex = new Conexion("sistema");
            $sql = "SELECT e.*, p.nombre, p.apellido, p.correo, p.telefono, 
                           p.fecha_nacimiento, p.sexo, p.direccion, c.nombre_cargo 
                    FROM empleados e 
                    INNER JOIN persona p ON e.cedula_persona = p.cedula_persona 
                    INNER JOIN cargos c ON e.id_cargo = c.id_cargo 
                    WHERE e.cedula_persona = :c";
            
            $stmt = $conex->prepare($sql);
            $stmt->execute([":c" => $cedula]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $conex = null; 
            return $resultado ?: []; 
        } catch (PDOException $e) {
            error_log("Error en consulta empleado: " . $e->getMessage());
            return false;
        }
    }

   
    
public function registroEmpleado()
{
    try {
        $conex = new Conexion("sistema");
        $conex->beginTransaction();

        
        $sexoInput = $this->getSexo();
        $sexoFinal = ($sexoInput === 'Masculino') ? 'M' : (($sexoInput === 'Femenino') ? 'F' : $sexoInput);

        
        $sqlPersona = "INSERT INTO persona (cedula_persona, nombre, apellido, correo, telefono, fecha_nacimiento, sexo, direccion) 
                        VALUES (:cedula, :nombre, :apellido, :correo, :telefono, :fecha, :sexo, :direccion)";
        
        $stmtP = $conex->prepare($sqlPersona);
        $stmtP->execute([
            ":cedula"    => $this->getCedula(),
            ":nombre"    => $this->getNombre(),
            ":apellido"  => $this->getApellido(),
            ":correo"    => $this->getCorreo(),
            ":telefono"  => $this->getCel(),
            ":fecha"     => $this->getEdad(),
            ":sexo"      => $sexoFinal, 
            ":direccion" => $this->getDireccion()
        ]);

        
        $sqlEmpleado = "INSERT INTO empleados (cedula_persona, id_cargo, estado) 
                        VALUES (:cedula, :cargo, 'activo')";
        
        $stmtE = $conex->prepare($sqlEmpleado);
        $stmtE->execute([
            ":cedula" => $this->getCedula(),
            ":cargo"  => $this->getCargo()
        ]);

        
        $conex->commit();
        unset($conex);
        return ["success" => true];

    } catch (PDOException $e) {
        if (isset($conex)) {
            $conex->rollBack();
            unset($conex);
        }
    
        return ["error" => "Error en BD: " . $e->getMessage()];
    }
}
public function ModificarEmpleado($cedula) 
{
    try {
        $conex = new Conexion("sistema");
        $conex->beginTransaction();

       
        $sexoFinal = ($this->getSexo() === 'Masculino' || $this->getSexo() === 'M') ? 'M' : 'F';

        
        $sqlPersona = "UPDATE persona SET 
                        nombre = :nombre, 
                        apellido = :apellido, 
                        correo = :correo, 
                        telefono = :telefono, 
                        fecha_nacimiento = :fecha, 
                        sexo = :sexo, 
                        direccion = :direccion 
                       WHERE cedula_persona = :cedula";
        
        $stmtP = $conex->prepare($sqlPersona);
        $stmtP->execute([
            ":nombre"    => $this->getNombre(),
            ":apellido"  => $this->getApellido(),
            ":correo"    => $this->getCorreo(),
            ":telefono"  => $this->getCel(),
            ":fecha"     => $this->getEdad(),
            ":sexo"      => $sexoFinal,
            ":direccion" => $this->getDireccion(),
            ":cedula"    => $cedula
        ]);

      
        $sqlEmpleado = "UPDATE empleados SET id_cargo = :cargo WHERE cedula_persona = :cedula";
        $stmtE = $conex->prepare($sqlEmpleado);
        $stmtE->execute([
            ":cargo"  => $this->getCargo(),
            ":cedula" => $cedula
        ]);

        $conex->commit();
        unset($conex);
        return ["success" => true];
    } catch (PDOException $e) {
        if (isset($conex)) $conex->rollBack();
        return ["error" => $e->getMessage()];
    }
}

    public function eliminarEmpleado()
    {
        try {
            $conex = new Conexion("sistema");
            $sql = "UPDATE empleados SET estado = :e WHERE cedula_persona = :cedula";
            $stmt = $conex->prepare($sql);
            $stmt->execute([
                ":e"      => $this->getEstado(),
                ":cedula" => $this->getCedula()
            ]);
            unset($conex);
            return ["success" => true];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}