<?php
// models/Checkout.php
// Gestión de checkout, pagos y bancos
// /src/app/models/Checkout.php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use PDOException;

class Checkout {
    private $db;
    private $id_pedido;
    private $cedula_persona;

    public function setIdPedido($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('ID de pedido inválido');
        }
        $this->id_pedido = $valor;
        return $this;
    }

    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setCedulaPersona($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('Cédula inválida');
        }
        $this->cedula_persona = $valor;
        return $this;
    }

    public function getCedulaPersona() {
        return $this->cedula_persona;
    }

    public function __construct() {
        $conexion = new Conexion('sistema_edward');
        $this->db = $conexion->getConexion();
    }

    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'obtenerBancos': return $this->_obtenerBancos();
            case 'obtenerDatosBanco': return $this->_obtenerDatosBanco($datos['banco_id']);
            case 'obtenerDatosPagoMovil': return $this->_obtenerDatosPagoMovil();
            case 'obtenerPedidoPendiente': return $this->_obtenerPedidoPendiente($datos['cedula']);
            case 'obtenerPedidoPorId': return $this->_obtenerPedidoPorId($datos['id_pedido'], $datos['cedula'] ?? null);
            case 'actualizarEstadoPedido': return $this->_actualizarEstadoPedido($datos['id_pedido'], $datos['estado']);
            case 'verificarPedido': return $this->_verificarPedido($datos['id_pedido'], $datos['cedula'] ?? null);
            default: return ['error' => 'Acción no válida'];
        }
    }

    private function _obtenerBancos() {
        try {
            $stmt = $this->db->prepare("SELECT id_banco, nombre_banco FROM bancos WHERE estado = 'activo'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en _obtenerBancos: " . $e->getMessage());
            return [];
        }
    }

    private function _obtenerDatosBanco($banco_id) {
        $datos = [
            'nombre' => 'Banco de Venezuela',
            'numero_cuenta' => '0102-0202-1545-7896-3784',
            'cedula_rif' => 'J-44445698',
            'titular' => 'Edward Accesorios C.A.',
            'tipo_cuenta' => 'Corriente'
        ];
        
        if ($banco_id) {
            try {
                $stmt = $this->db->prepare("SELECT nombre_banco, numero_cuenta, cedula_banco FROM bancos WHERE id_banco = ?");
                $stmt->execute([$banco_id]);
                $bd = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($bd) {
                    $datos['nombre'] = $bd['nombre_banco'];
                    $datos['numero_cuenta'] = $bd['numero_cuenta'] ?? $datos['numero_cuenta'];
                    $datos['cedula_rif'] = $bd['cedula_banco'] ?? $datos['cedula_rif'];
                }
            } catch (PDOException $e) {
                error_log("Error en _obtenerDatosBanco: " . $e->getMessage());
            }
        }
        
        return $datos;
    }

    private function _obtenerDatosPagoMovil() {
        return [
            'banco' => 'Banco de Venezuela',
            'telefono' => '0414-1234567',
            'cedula' => 'V-12345678',
            'rif' => 'J-44445698',
            'referencia' => 'Pago movil Edward Accesorios'
        ];
    }

    private function _obtenerPedidoPendiente($cedula_cliente) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM pedidos 
                WHERE cedula_persona = ? AND estado = 'pendiente_pago' 
                ORDER BY fecha DESC LIMIT 1
            ");
            $stmt->execute([$cedula_cliente]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en _obtenerPedidoPendiente: " . $e->getMessage());
            return null;
        }
    }

    private function _obtenerPedidoPorId($id_pedido, $cedula_cliente = null) {
        try {
            if ($cedula_cliente) {
                $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE id_pedido = ? AND cedula_persona = ?");
                $stmt->execute([$id_pedido, $cedula_cliente]);
            } else {
                $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
            }
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en _obtenerPedidoPorId: " . $e->getMessage());
            return null;
        }
    }

    private function _actualizarEstadoPedido($id_pedido, $estado) {
        try {
            $stmt = $this->db->prepare("UPDATE pedidos SET estado = :estado WHERE id_pedido = :id");
            return $stmt->execute([
                ':estado' => $estado,
                ':id' => $id_pedido
            ]);
        } catch (PDOException $e) {
            error_log("Error en _actualizarEstadoPedido: " . $e->getMessage());
            return false;
        }
    }

    private function _verificarPedido($id_pedido, $cedula_cliente = null) {
        try {
            if ($cedula_cliente) {
                $stmt = $this->db->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ? AND cedula_persona = ?");
                $stmt->execute([$id_pedido, $cedula_cliente]);
            } else {
                $stmt = $this->db->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
            }
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Error en _verificarPedido: " . $e->getMessage());
            return false;
        }
    }
}