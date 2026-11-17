<?php

namespace App\Sistema\models;

use PDO;
use PDOException;

class CheckoutModel
{
    private $conn;
    private $checkout_id_pedido;
    private $checkout_cedula_persona;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function ejecutar($accion, $datos = [])
    {
        switch ($accion) {
            case 'obtenerBancos': return $this->obtenerBancos();
            case 'obtenerDatosBanco': return $this->obtenerDatosBanco($datos['banco_id']);
            case 'obtenerDatosPagoMovil': return $this->obtenerDatosPagoMovil();
            case 'obtenerPedidoPendiente': return $this->obtenerPedidoPendiente($datos['cedula']);
            case 'obtenerPedidoPorId': return $this->obtenerPedidoPorId($datos['id_pedido'], $datos['cedula'] ?? null);
            case 'actualizarEstadoPedido': return $this->actualizarEstadoPedido($datos['id_pedido'], $datos['estado']);
            case 'verificarPedido': return $this->verificarPedido($datos['id_pedido'], $datos['cedula'] ?? null);
            default: return ['error' => 'Acción no válida'];
        }
    }

public function setIdPedido($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('ID de pedido inválido');
        }
        $this->checkout_id_pedido = $valor;
        return $this;
    }

    public function getIdPedido() {
        return $this->checkout_id_pedido;
    }

    public function setCedulaPersona($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('Cédula inválida');
        }
        $this->checkout_cedula_persona = $valor;
        return $this;
    }

    public function getCedulaPersona() {
        return $this->checkout_cedula_persona;
    }

    

    

    private function obtenerBancos() {
        try {
            $stmt = $this->conn->prepare("SELECT id_banco, nombre_banco FROM bancos WHERE estado = 'activo'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en _obtenerBancos: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerDatosBanco($banco_id) {
        $datos = [
            'nombre' => 'Banco de Venezuela',
            'numero_cuenta' => '0102-0202-1545-7896-3784',
            'cedula_rif' => 'J-44445698',
            'titular' => 'Edward Accesorios C.A.',
            'tipo_cuenta' => 'Corriente'
        ];

        if ($banco_id) {
            try {
                $stmt = $this->conn->prepare("SELECT nombre_banco, numero_cuenta, cedula_banco FROM bancos WHERE id_banco = ?");
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

    private function obtenerDatosPagoMovil() {
        return [
            'banco' => 'Banco de Venezuela',
            'telefono' => '0414-1234567',
            'cedula' => 'V-12345678',
            'rif' => 'J-44445698',
            'referencia' => 'Pago movil Edward Accesorios'
        ];
    }

    private function obtenerPedidoPendiente($cedula_cliente) {
        try {
            $stmt = $this->conn->prepare("
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

    private function obtenerPedidoPorId($id_pedido, $cedula_cliente = null) {
        try {
            if ($cedula_cliente) {
                $stmt = $this->conn->prepare("SELECT * FROM pedidos WHERE id_pedido = ? AND cedula_persona = ?");
                $stmt->execute([$id_pedido, $cedula_cliente]);
            } else {
                $stmt = $this->conn->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
            }
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en _obtenerPedidoPorId: " . $e->getMessage());
            return null;
        }
    }

    private function actualizarEstadoPedido($id_pedido, $estado) {
        try {
            $stmt = $this->conn->prepare("UPDATE pedidos SET estado = :estado WHERE id_pedido = :id");
            return $stmt->execute([
                ':estado' => $estado,
                ':id' => $id_pedido
            ]);
        } catch (PDOException $e) {
            error_log("Error en _actualizarEstadoPedido: " . $e->getMessage());
            return false;
        }
    }

    private function verificarPedido($id_pedido, $cedula_cliente = null) {
        try {
            if ($cedula_cliente) {
                $stmt = $this->conn->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ? AND cedula_persona = ?");
                $stmt->execute([$id_pedido, $cedula_cliente]);
            } else {
                $stmt = $this->conn->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ?");
                $stmt->execute([$id_pedido]);
            }
            return $stmt->fetch() ? true : false;
        } catch (PDOException $e) {
            error_log("Error en _verificarPedido: " . $e->getMessage());
            return false;
        }
    }
}
