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

        $stmt = $conex->prepare("SELECT COUNT(*) as conteo FROM empleados WHERE cedula_empleado = :c");

        if (!($stmt->execute([
            ":c" => $cedula
        ]))) {
            return ['error' => "Error con el servidor!"];
        }

        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        unset($conex);
        return $array["conteo"];
    }

    public function consultaPerfiles()
    {
        $cedula_usuario = $this->getCedula();
        $conex = new Conexion("sistema");

        $stmt = $conex->prepare("SELECT * FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE e.perfil != 'suspendido' && e.cedula_empleado != :c");
        $stmt->execute([":c" => $cedula_usuario]);
        
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado;
    }

    public function consultaSuspendidos()
    {
        $cedula_usuario = $this->getCedula();
        $conex = new Conexion("sistema");

        $stmt = $conex->prepare("SELECT * FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE e.perfil = 'suspendido' && e.cedula_empleado != :c");
        $stmt->execute([":c" => $cedula_usuario]);

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado;
    }

    public function consultaInactivos()
    {
        $conex = new Conexion("sistema");

        $stmt = $conex->prepare("SELECT * FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE e.estado = 'inactivo'");
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado;
    }

    public function consultarCargos()
    {
        $id_cargo = $this->getCargo();

        if(empty($id_cargo)){
            return ["incompleto" => "El identificador del registro esta vacio, por favor verifique!"];
        }

        if((is_int($id_cargo) && preg_match('/^[0-9]/',$id_cargo))){
            return ["invalido" => "El identificador del registro tiene un formato incorrecto!"];
        }

        $conex = new Conexion("sistema");

        $stmt = $conex->prepare("SELECT * FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo INNER JOIN precios_cargos as p ON c.id_cargo = p.id_cargo WHERE c.id_cargo = :id and e.estado = 'activo'");

        if(!($stmt->execute([":id" => $id_cargo]))){
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado;
    }

    public function consultar()
    {
        $cedula = $this->getCedula();
        
        $conex = new Conexion("sistema");

        $stmt = $conex->prepare("SELECT * FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE e.cedula_empleado = :c");

        if(!($stmt->execute([":c" => $cedula]))){
            return ["error" => "Ah ocurrido un error con el servidor!"];

        }

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado;
    }

    public function registroEmpleado()
    {
        $nombre = $this->getNombre();
        $apellido = $this->getApellido();
        $cedula = $this->getCedula();
        $telefono = $this->getCel();
        $direccion = $this->getDireccion();
        $correo = $this->getCorreo();
        $cargo = $this->getCargo();

        if (empty($nombre) || empty($apellido) || empty($cedula) || empty($telefono) || empty($direccion) || empty($correo) || empty($cargo)) {
            $input = "";

            if(empty($nombre)){
                $input .= "nombre-";
            }
            if(empty($apellido)){
                $input .= "apellido-";
            }
            if(empty($cedula)){
                $input .= "cedula-";
            }
            if(empty($telefono)){
                $input .= "telefono-";
            }
            if(empty($direccion)){
                $input .= "direccion-";
            }
            if(empty($correo)){
                $input .= "correo-";
            }
            if(empty($cargo)){
                $input .= "cargo-";
            }

            return ["incompleto" => "datos incompletos.", "input" => $input];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre) && strlen($nombre) >= 3)) {
            return ["invalido" => "El nombre ingresado no es valido!", "input" => "nombre"];
        }
        if (!(is_string($apellido) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido) && strlen($apellido) >= 3)) {
            return ["invalido" => "El apellido ingresado no es valido!", "input" => "apellido"];
        }
        if (!(is_string($cedula) && preg_match('/^[VE]-\d{6,9}$/', $cedula) && strlen($cedula) >= 8)) {
            return ["invalido" => "Formato de Cedula Invalido!", "input" => "cedula"];
        }
        if (!(preg_match('/^\d{10,11}$/', $telefono) && preg_match('/^[0-9]*$/', $telefono))) {
            return ["invalido" => "Formato de Número de Teléfono inválido!", "input" => "telefono"];
        }
        if (!(is_string($direccion) && strlen($direccion) >= 10)) {
            return ["invalido" => "Formato de Dirección Invalido!", "input" => "direccion"];
        }
        if (!(is_string($correo) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',$correo) && strlen($correo) >= 7)) {
            return ["invalido" => "Formato del Correo Invalido!", "input" => "correo"];
        }
        if(!(is_int($cargo) && preg_match('/^[0-9]*$/', $cargo))){
            return ["invalido" => "Formato del Cargo no valido!", "input" => "cargo"];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Empleados";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $sql = "INSERT INTO empleados (nombre, apellido, cedula_empleado, telefono, direccion, correo, id_cargo) VALUES (:nombre, :apellido, :cedula, :telefono, :direccion, :correo, :cargo)";

            $parametros = [
                ":nombre" => $nombre,
                ":apellido" => $apellido,
                ":cedula" => $cedula,
                ":telefono" => $telefono,
                ":direccion" => $direccion,
                ":correo" => $correo,
                ":cargo" => $cargo
            ];

            $obj_persona = new Persona();

            if (!($obj_persona->registrar($sql, "empleado", $parametros))) {
                $conex->rollBack();
                return ["error" => "Ah ocurrido un error con el Servidor!"];
            }

            $conex->commit();
            unset($obj_persona);
            unset($conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function ModificarEmpleado($cedula_vieja)
    {
        $nombre = $this->getNombre();
        $apellido = $this->getApellido();
        $cedula_nueva = $this->getCedula();
        $telefono = $this->getCel();
        $direccion = $this->getDireccion();
        $correo = $this->getCorreo();
        $cargo = $this->getCargo();

        if (empty($nombre) || empty($apellido) || empty($cedula_nueva) || empty($telefono) || empty($direccion) || empty($correo) || empty($cargo) || empty($cedula_vieja)) {
            $input = "";

            if(empty($nombre)){
                $input .= "nombreModificar-";
            }
            if(empty($apellido)){
                $input .= "apellidoModificar-";
            }
            if(empty($cedula_nueva)){
                $input .= "cedulaModificar-";
            }
            if(empty($telefono)){
                $input .= "telefonoModificar-";
            }
            if(empty($direccion)){
                $input .= "direccionModificar-";
            }
            if(empty($correo)){
                $input .= "correoModificar-";
            }
            if(empty($cargo)){
                $input .= "cargoModificar-";
            }

            return ["incompleto" => "datos incompletos.", "input" => $input];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre) && strlen($nombre) >= 3)) {
            return ["invalido" => "El nombre ingresado no es valido!", "input" => "nombreModificar"];
        }
        if (!(is_string($apellido) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido) && strlen($apellido) >= 3)) {
            return ["invalido" => "El apellido ingresado no es valido!", "input" => "apellidoModificar"];
        }
        if (!(is_string($cedula_nueva) && preg_match('/^[VE]-\d{6,9}$/', $cedula_nueva) && strlen($cedula_nueva) >= 8)) {
            return ["invalido" => "Formato de Cedula Invalido!", "input" => "cedulaModificar"];
        }
        if (!(preg_match('/^\d{10,11}$/', $telefono) && preg_match('/^[0-9]*$/', $telefono))) {
            return ["invalido" => "Formato de Número de Teléfono inválido!", "input" => "telefonoModificar"];
        }
        if (!(is_string($direccion) && strlen($direccion) >= 10)) {
            return ["invalido" => "Formato de Dirección Invalido!", "input" => "direccionModificar"];
        }
        if (!(is_string($correo) && preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',$correo) && strlen($correo) >= 7)) {
            return ["invalido" => "Formato del Correo Invalido!", "input" => "correoModificar"];
        }
        if(!(is_int($cargo) && preg_match('/^[0-9]*$/', $cargo))){
            return ["invalido" => "Formato del Cargo no valido!", "input" => "cargoModificar"];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Empleados";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $sql = "UPDATE empleados SET nombre=:nombre, apellido=:apellido, id_cargo=:cargo, telefono=:telefono, direccion=:direccion, correo=:correo, cedula_empleado = :cedula WHERE cedula_empleado = :cedula_id";

            $parametros = [
                ":nombre" => $nombre,
                ":apellido" => $apellido,
                ":cargo" => $cargo,
                ":telefono" => $telefono,
                ":direccion" => $direccion,
                ":correo" => $correo,
                ":cedula" => $cedula_nueva,
                ":cedula_id" => $cedula_vieja
            ];

            $obj_persona = new Persona();

            if (!($obj_persona->modificar($sql, "empleado", $parametros))) {
                $conex->rollBack();
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $conex->commit();
            unset($obj_persona);
            unset($conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminarEmpleado()
    {
        $id = $this->getCedula();
        $estado = $this->getEstado();

        if (empty($id) && empty($estado)) {
            return ["error" => "Error datos no validos!"];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Empleados";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $sql = "UPDATE empleados SET estado = :e WHERE cedula_empleado = :cedula";

            $parametros = [
                ":e" => $estado,
                ":cedula" => $id
            ];

            $obj_persona = new Persona();

            if (!($obj_persona->eliminar($sql, $parametros, "empleado"))) {
                $conex->rollBack();
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $conex->commit();
            unset($obj_persona);
            unset($conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }
}