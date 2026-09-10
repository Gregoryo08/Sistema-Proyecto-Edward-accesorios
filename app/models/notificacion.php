<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;
use \DateTime;
use \DateTimeZone;

class notificacion extends Conexion
{
    private $id_notificacion;
    private $mensaje;
    private $tipo;
    private $cedula_usuario;

    public function __construct() { parent::__construct(); }

    public function listarPendientes() {
        $conex = Conexion::getShared("usuario")->getConexion();
        $sql = "SELECT n.id_notificacion, n.mensaje, n.tipo, n.fecha_creacion 
                FROM notificaciones n
                JOIN notificaciones_usuario nu ON n.id_notificacion = nu.id_notificacion
                WHERE nu.cedula_usuario = :c AND nu.leida = 0
                ORDER BY n.fecha_creacion DESC";
        $stmt = $conex->prepare($sql);
        $stmt->execute([":c" => $this->cedula_usuario]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->aplicarReparacion($filas);
    }

    public function contarPendientes() {
        $conex = Conexion::getShared("usuario")->getConexion();
        $sql = "SELECT COUNT(*) FROM notificaciones_usuario 
                WHERE cedula_usuario = :c AND leida = 0";
        $stmt = $conex->prepare($sql);
        $stmt->execute([":c" => $this->cedula_usuario]);
        return $stmt->fetchColumn();
    }

    public function listar($mostrarSoloPendientes = true) {
        $conex = Conexion::getShared("usuario")->getConexion();
        $sql = "SELECT n.id_notificacion, n.mensaje, n.tipo, n.fecha_creacion, nu.leida 
                FROM notificaciones n
                JOIN notificaciones_usuario nu ON n.id_notificacion = nu.id_notificacion
                WHERE nu.cedula_usuario = :c";
                
        if ($mostrarSoloPendientes) {
            $sql .= " AND nu.leida = 0";
        }
        
        $sql .= " ORDER BY n.fecha_creacion DESC";
        $stmt = $conex->prepare($sql);
        $stmt->execute([":c" => $this->cedula_usuario]);
        return $this->aplicarReparacion($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function repararAcentos($texto)
    {
        if (!is_string($texto) || $texto === '') {
            return $texto;
        }
        if (strpos($texto, "\u{251C}") === false) {
            return $texto;
        }
        $bytes = @iconv('UTF-8', 'CP437', $texto);
        if ($bytes === false || !mb_check_encoding($bytes, 'UTF-8')) {
            return $texto;
        }
        if (strpos($bytes, "\u{251C}") !== false) {
            return $texto;
        }
        return $bytes;
    }

    private function aplicarReparacion($filas)
    {
        if (!is_array($filas)) {
            return $filas;
        }
        foreach ($filas as &$fila) {
            if (isset($fila['mensaje'])) {
                $fila['mensaje'] = $this->repararAcentos($fila['mensaje']);
            }
            if (isset($fila['fecha_creacion'])) {
                try {
                    $dt = new DateTime($fila['fecha_creacion'], new DateTimeZone('UTC'));
                    $dt->setTimezone(new DateTimeZone('America/Caracas'));
                    $hora = $dt->format('g:i');
                    $ampm = strtolower($dt->format('A')) === 'am' ? 'a. m.' : 'p. m.';
                    $fila['fecha_creacion'] = $dt->format('Y-m-d') . ' ' . $hora . ' ' . $ampm;
                } catch (\Exception $e) {
                }
            }
        }
        return $filas;
    }

    public function marcarLeida() {
        $conex = Conexion::getShared("usuario")->getConexion();
        $stmt = $conex->prepare("UPDATE notificaciones_usuario SET leida = 1 WHERE id_notificacion = :id AND cedula_usuario = :c");
        return $stmt->execute([":id" => $this->id_notificacion, ":c" => $this->cedula_usuario]);
    }

    public function registrar() {
        $conex = Conexion::getShared("usuario")->getConexion();
        try {
            $conex->beginTransaction(); 

            $stmt = $conex->prepare("INSERT INTO notificaciones (mensaje, tipo) VALUES (:m, :t)");
            $stmt->execute([":m" => $this->mensaje, ":t" => $this->tipo]);
            $id = $conex->lastInsertId();
            
            $stmt2 = $conex->prepare("INSERT INTO notificaciones_usuario (id_notificacion, cedula_usuario, leida) VALUES (:id, :c, 0)");
            $stmt2->execute([":id" => $id, ":c" => $this->cedula_usuario]);
            
            $conex->commit(); 
            return true;
        } catch (PDOException $e) {
            $conex->rollBack(); 
            error_log("ERROR TRANSACCION: " . $e->getMessage());
            return false;
        }
    }
    
    public function setId_notificacion($id) { $this->id_notificacion = $id; }
    public function setCedula_usuario($c) { $this->cedula_usuario = $c; }
    public function setMensaje($m) { $this->mensaje = $m; }
    public function setTipo($t) { $this->tipo = $t; }
}