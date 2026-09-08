<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class Usuario extends Conexion
{
    private $id;
    private $cedula_usuario;
    private $clave;
    private $id_rol;
    private $estatus;
    private $tipo_registro;

    public function __construct()
    {
        parent::__construct();
    }

    public function consultarUno()
{
    $conexUser = new Conexion("usuario");
    $sqlU = "SELECT u.cedula_usuario, u.id_rol FROM usuarios u WHERE u.cedula_usuario = :c";
    $stmtU = $conexUser->prepare($sqlU);
    $stmtU->execute([":c" => $this->cedula_usuario]);
    $usuario = $stmtU->fetch(PDO::FETCH_ASSOC);
    unset($conexUser);

    if (!$usuario) return null;

    $conexSistema = new Conexion("sistema");
    $sqlP = "SELECT nombre, apellido, correo, telefono, direccion, fecha_nacimiento, sexo FROM persona WHERE cedula_persona = :c";
    $stmtP = $conexSistema->prepare($sqlP);
    $stmtP->execute([":c" => $this->cedula_usuario]);
    $persona = $stmtP->fetch(PDO::FETCH_ASSOC);
    unset($conexSistema);

    return $persona ? array_merge($usuario, $persona) : $usuario;
}

    public function listarRoles()
    {
        $conex = new Conexion("usuario");
        $sql = "SELECT idRol, descripcion_rol FROM roles";
        $stmt = $conex->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $resultado;
    }

    public function listarCargos()
    {
        $conex = new Conexion("sistema");
        $sql = "SELECT id_cargo, nombre_cargo FROM cargos";
        $stmt = $conex->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $resultado;
    }

   public function registrarCompleto($datosPersona)
{
    try {
        $cedulaLimpia = trim($this->cedula_usuario);
        if (!preg_match('/^[VE]-/', $cedulaLimpia)) {
            $this->cedula_usuario = 'V-' . preg_replace('/^[VEve]-?/', '', $cedulaLimpia);
        }

        $conexSistema = new Conexion("sistema");
        $conexSistema->beginTransaction();

        $sqlPersona = "INSERT INTO persona (cedula_persona, nombre, apellido, correo, telefono, direccion, fecha_nacimiento, sexo) VALUES (:c, :n, :a, :cor, :tel, :dir, :fn, :sex)";
        $stmtP = $conexSistema->prepare($sqlPersona);
        $stmtP->execute([
            ":c" => $this->cedula_usuario,
            ":n" => $datosPersona['nombre'],
            ":a" => $datosPersona['apellido'],
            ":cor" => $datosPersona['correo'] ?? 'no@correo.com',
            ":tel" => $datosPersona['telefono'] ?? '0000',
            ":dir" => $datosPersona['direccion'] ?? 'N/A',
            ":fn"  => $datosPersona['fecha_nacimiento'] ?? null,
            ":sex" => $datosPersona['sexo'] ?? 'No especificado'
        ]);

        if ($this->tipo_registro === 'cliente') {
            $sqlTipo = "INSERT INTO clientes (cedula_persona) VALUES (:c)";
            $stmtT = $conexSistema->prepare($sqlTipo);
            $stmtT->execute([":c" => $this->cedula_usuario]);
        } else {
            $sqlTipo = "INSERT INTO empleados (cedula_persona, id_cargo) VALUES (:c, :id_cargo)";
            $stmtT = $conexSistema->prepare($sqlTipo);
            $stmtT->execute([
                ":c" => $this->cedula_usuario,
                ":id_cargo" => $datosPersona['id_cargo'] ?? null
            ]);
        }
        
        $conexSistema->commit();

        $conexUser = new Conexion("usuario");
        $sqlUser = "INSERT INTO usuarios (cedula_usuario, clave, id_rol, estatus) VALUES (:c, :cl, :r, 'Activo')";
        $stmtU = $conexUser->prepare($sqlUser);
        $stmtU->execute([
            ":c" => $this->cedula_usuario,
            ":cl" => password_hash($this->clave, PASSWORD_BCRYPT),
            ":r" => $this->id_rol
        ]);
        
        return ["success" => true];
    } catch (PDOException $e) {
        if (isset($conexSistema) && $conexSistema->inTransaction()) {
            $conexSistema->rollBack();
        }
        return ["success" => false, "error" => $e->getMessage()];
    }
}

   public function modificarPerfil($datosPersona = null)
    {
        try {
            $conexUser = new Conexion("usuario");
            $params = [":r" => $this->id_rol, ":c" => $this->cedula_usuario];
            $sql = "UPDATE usuarios SET id_rol = :r WHERE cedula_usuario = :c";
            
            if (!empty($this->clave)) {
                $sql = "UPDATE usuarios SET clave = :cl, id_rol = :r WHERE cedula_usuario = :c";
                $params[":cl"] = password_hash($this->clave, PASSWORD_BCRYPT);
            }
            
            $stmt = $conexUser->prepare($sql);
            $stmt->execute($params);

            if ($datosPersona) {
                $conexSistema = new Conexion("sistema");
                $sqlP = "UPDATE persona SET nombre = :n, apellido = :a, correo = :cor, telefono = :tel, direccion = :dir, fecha_nacimiento = :fn, sexo = :sex WHERE cedula_persona = :c";
                $stmtP = $conexSistema->prepare($sqlP);
                $stmtP->execute([
                    ":n"   => $datosPersona['nombre'],
                    ":a"   => $datosPersona['apellido'],
                    ":cor" => $datosPersona['correo'] ?? 'no@correo.com',
                    ":tel" => $datosPersona['telefono'] ?? '0000',
                    ":dir" => $datosPersona['direccion'] ?? 'N/A',
                    ":fn"  => $datosPersona['fecha_nacimiento'] ?? null,
                    ":sex" => $datosPersona['sexo'] ?? 'No especificado',
                    ":c"   => $this->cedula_usuario
                ]);
            }
            return ["success" => true];
        } catch (PDOException $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    public function listar()
    {
        $conexUser = new Conexion("usuario");
        $sqlU = "SELECT u.cedula_usuario, u.estatus, r.descripcion_rol, u.id_rol 
                 FROM usuarios u 
                 INNER JOIN roles r ON u.id_rol = r.idRol";
        
        $stmtU = $conexUser->prepare($sqlU);
        $stmtU->execute();
        $usuarios = $stmtU->fetchAll(PDO::FETCH_ASSOC);
        unset($conexUser);

        if (empty($usuarios)) return [];

        $conexSistema = new Conexion("sistema");
        $sqlP = "SELECT cedula_persona, nombre, apellido FROM persona";
        $stmtP = $conexSistema->prepare($sqlP);
        $stmtP->execute();
        $personas = $stmtP->fetchAll(PDO::FETCH_ASSOC);
        unset($conexSistema);

        $mapaPersonas = [];
        foreach ($personas as $p) {
            $mapaPersonas[$p['cedula_persona']] = $p;
        }

        foreach ($usuarios as &$u) {
            $cedula = $u['cedula_usuario'];
            if (isset($mapaPersonas[$cedula])) {
                $u['nombre'] = $mapaPersonas[$cedula]['nombre'];
                $u['apellido'] = $mapaPersonas[$cedula]['apellido'];
            } else {
                $u['nombre'] = '';
                $u['apellido'] = '';
            }
        }
        return $usuarios;
    }

    public function cambiarEstatus()
    {
        $conex = new Conexion("usuario");
        $sql = "UPDATE usuarios SET estatus = :e WHERE cedula_usuario = :c";
        $stmt = $conex->prepare($sql);
        $resultado = $stmt->execute([":e" => $this->estatus, ":c" => $this->cedula_usuario]);
        unset($conex);
        return $resultado;
    }

    public function eliminar()
    {
        $conex = new Conexion("usuario");
        $sql = "UPDATE usuarios SET estatus = 'Inactivo' WHERE cedula_usuario = :c";
        $stmt = $conex->prepare($sql);
        $resultado = $stmt->execute([":c" => $this->cedula_usuario]);
        unset($conex);
        return $resultado;
    }

    public function setTipoRegistro($tipo) { $this->tipo_registro = $tipo; }
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getCedula_usuario() { return $this->cedula_usuario; }
    public function setCedula_usuario($cedula) { $this->cedula_usuario = $cedula; }
    public function getClave() { return $this->clave; }
    public function setClave($clave) { $this->clave = $clave; }
    public function getId_rol() { return $this->id_rol; }
    public function setId_rol($rol) { $this->id_rol = $rol; }
    public function setEstatus($estatus) { $this->estatus = $estatus; }
}