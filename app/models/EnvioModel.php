<?php

namespace App\Sistema\models;

use PDO;
use PDOException;

class EnvioModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function ejecutar($accion, $datos = [])
    {
        switch ($accion) {
            case 'obtenerActivos': return $this->obtenerActivos();
            case 'obtenerPorId': return $this->obtenerPorId($datos['id']);
            case 'obtenerPorPedido': return $this->obtenerPorPedido($datos['id_pedido']);
            case 'crear': return $this->crear($datos);
            case 'actualizarEstado': return $this->actualizarEstado($datos['id_despacho'], $datos['estado']);
            case 'marcarEntregado': return $this->marcarEntregado($datos['id_despacho']);
            default: return ['error' => 'Acción no válida'];
        }
    }

    

    private function validarDatosDespacho($datos) {
        if (empty($datos['id_pedido']) || $datos['id_pedido'] <= 0) {
            throw new PDOException("ID de pedido inválido");
        }
        if (!in_array($datos['tipo_despacho'] ?? 'delivery', ['tienda', 'delivery'])) {
            throw new PDOException("Tipo de despacho inválido");
        }
    }

    private function tablaExiste() {
        $stmt = $this->conn->prepare("SHOW TABLES LIKE 'despachos'");
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function obtenerDespachoConJoin($where, $params = []) {
        $sql = "
            SELECT
                d.*,
                p.id_pedido,
                p.total,
                p.direccion_entrega,
                per.nombre,
                per.apellido,
                per.telefono,
                per.correo
            FROM despachos d
            JOIN pedidos p ON d.id_pedido = p.id_pedido
            JOIN persona per ON p.cedula_persona = per.cedula_persona
        ";

        if ($where) {
            $sql .= " WHERE " . $where;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    

    private function obtenerActivos() {
        try {
            if (!$this->tablaExiste()) {
                return [];
            }

            $stmt = $this->obtenerDespachoConJoin(
                "d.estado_despacho IN ('pendiente', 'asignado', 'en_ruta')",
                []
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_obtenerActivos: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerPorId($id_despacho) {
        try {
            $stmt = $this->obtenerDespachoConJoin("d.id_despacho = ?", [$id_despacho]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    private function obtenerPorPedido($id_pedido) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM despachos
                WHERE id_pedido = ?
                ORDER BY fecha_despacho DESC
            ");
            $stmt->execute([$id_pedido]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_obtenerPorPedido: " . $e->getMessage());
            return [];
        }
    }

    private function crear($datos) {
        try {
            $this->validarDatosDespacho($datos);

            $stmt = $this->conn->prepare("
                INSERT INTO despachos (
                    id_pedido,
                    tipo_despacho,
                    fecha_despacho,
                    hora_despacho,
                    despachador_nombre,
                    despachador_telefono,
                    instrucciones_entrega,
                    fecha_entrega_estimada,
                    estado_despacho
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')
            ");

            $resultado = $stmt->execute([
                $datos['id_pedido'],
                $datos['tipo_despacho'] ?? 'delivery',
                $datos['fecha_despacho'] ?? date('Y-m-d'),
                $datos['hora_despacho'] ?? null,
                $datos['despachador_nombre'] ?? null,
                $datos['despachador_telefono'] ?? null,
                $datos['instrucciones_entrega'] ?? null,
                $datos['fecha_entrega_estimada'] ?? null
            ]);

            if ($resultado) {
                return [
                    'success' => true,
                    'id_despacho' => $this->conn->lastInsertId()
                ];
            }
            return ['error' => 'Error al crear el despacho'];

        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_crear: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function actualizarEstado($id_despacho, $estado) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE despachos
                SET estado_despacho = ?
                WHERE id_despacho = ?
            ");
            return $stmt->execute([$estado, $id_despacho]);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_actualizarEstado: " . $e->getMessage());
            return false;
        }
    }

    private function marcarEntregado($id_despacho) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE despachos
                SET estado_despacho = 'entregado',
                    fecha_entrega_real = NOW()
                WHERE id_despacho = ?
            ");
            return $stmt->execute([$id_despacho]);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_marcarEntregado: " . $e->getMessage());
            return false;
        }
    }
}
