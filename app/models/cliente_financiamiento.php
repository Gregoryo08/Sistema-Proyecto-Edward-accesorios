<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class cliente_financiamiento extends Conexion
{
    private $cedula_cliente;
    private $id_cuota;
    private $monto;
    private $id_metodo;
    private $id_banco;
    private $referencia;
    private $fecha;

    public function __construct($cedula)
    {
        parent::__construct();
        $this->cedula_cliente = $cedula;
    }

    public function setIdCuota($val) { $this->id_cuota = (int)$val; }
    public function setMonto($val) { $this->monto = (float)$val; }
    public function setIdMetodo($val) { $this->id_metodo = (int)$val; }
    public function setIdBanco($val) { $this->id_banco = (int)$val; }
    public function setReferencia($val) { $this->referencia = htmlspecialchars(trim($val)); }
    public function setFecha($val) { $this->fecha = $val; }

    public function procesar($accion, $datos = [])
    {
        switch ($accion) {
            case 'listado':
                return $this->listarFinanciamientos();
            case 'listarHistorialCuotas':
                return $this->listarHistorialCuotas($datos);
            case 'registrarPago':
                return $this->registrarSolicitudPago($datos);
            case 'listarMetodos':
                return $this->listarMetodos();
            case 'listarBancos':
                return $this->listarBancos();
            default:
                return ["error" => "Acción no permitida"];
        }
    }

    private function listarHistorialCuotas($id_financiamiento)
    {
        try {
            $conex = new conexion("sistema");
            $sql = "SELECT c.id_cuota, c.monto_pagado, c.fecha_vencimiento, c.estado_cuota, 
                           c.fecha_pago_realizado, m.nombre_metodopago, b.nombre_banco
                    FROM cuotas c
                    LEFT JOIN metodo_pago m ON c.id_metodopago = m.id_metodopago
                    LEFT JOIN bancos b ON c.id_banco = b.id_banco
                    WHERE c.id_financiamiento = ? 
                    ORDER BY c.fecha_vencimiento ASC";
            $stmt = $conex->prepare($sql);
            $stmt->execute([$id_financiamiento]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    private function listarFinanciamientos()
    {
        try {
            $conex = new conexion("sistema");
            $sql = "SELECT f.id_financiamiento, pr.nombre_producto, f.monto_total, 
                           (f.monto_total - f.pago_inicial - (SELECT IFNULL(SUM(monto_pagado), 0) 
                           FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pagado')) as saldo_pendiente,
                           (SELECT fecha_vencimiento FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pendiente' ORDER BY fecha_vencimiento ASC LIMIT 1) as proximo_vencimiento,
                           (SELECT id_cuota FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pendiente' ORDER BY fecha_vencimiento ASC LIMIT 1) as id_cuota,
                           (SELECT monto_cuota FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pendiente' ORDER BY fecha_vencimiento ASC LIMIT 1) as monto_cuota
                    FROM financiamientos f
                    JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                    JOIN productos pr ON df.id_productos = pr.id_producto
                    WHERE f.cedula_persona = ? AND f.estado_financiamiento = 'vigente'";
            
            $stmt = $conex->prepare($sql);
            $stmt->execute([$this->cedula_cliente]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    private function registrarSolicitudPago($d)
    {
        $this->setIdCuota($d['id_cuota'] ?? 0);
        $this->setMonto($d['monto'] ?? 0);
        $this->setIdMetodo($d['id_metodo'] ?? 0);
        $this->setIdBanco($d['id_banco'] ?? 0);
        $this->setReferencia($d['referencia'] ?? '');
        $this->setFecha($d['fecha'] ?? '');

        if ($this->id_cuota <= 0 || $this->monto <= 0 || $this->id_metodo <= 0 || $this->id_banco <= 0 || empty($this->referencia) || empty($this->fecha)) {
            return ["error" => "Datos de pago inválidos"];
        }

        try {
            $conex = new conexion("sistema");
            $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");
            $sql = "UPDATE cuotas 
                    SET estado_cuota = 'en_revision', 
                        monto_pagado = ?, 
                        id_metodopago = ?, 
                        id_banco = ?,
                        referencia = ?, 
                        fecha_pago_realizado = ?
                    WHERE id_cuota = ? 
                    AND estado_cuota = 'pendiente'
                    AND id_financiamiento IN (SELECT id_financiamiento FROM financiamientos WHERE cedula_persona = ?)";
            
            $stmt = $conex->prepare($sql);
            $stmt->execute([$this->monto, $this->id_metodo, $this->id_banco, $this->referencia, $this->fecha, $this->id_cuota, $this->cedula_cliente]);
            
            if ($stmt->rowCount() > 0) {
                return ["success" => true];
            }
            
            return ["error" => "No se pudo procesar la solicitud"];
        } catch (PDOException $e) {
            return ["error" => "Error interno del servidor"];
        }
    }

    private function listarMetodos()
    {
        try {
            $conex = new conexion("sistema");
            $sql = "SELECT id_metodopago, nombre_metodopago FROM metodo_pago WHERE estado = 1";
            return $conex->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

   private function listarBancos()
    {
        try {
            $conex = new conexion("sistema");
            $sql = "SELECT id_banco, nombre_banco FROM bancos WHERE estatus = 'activo'";
            return $conex->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}