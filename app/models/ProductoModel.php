<?php

namespace App\Sistema\models;

use PDO;

class ProductoModel
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
            case 'verificarStock': return $this->verificarStock($datos['id'], $datos['cantidad']);
            case 'descontarStock': return $this->descontarStock($datos['id'], $datos['cantidad']);
            case 'obtenerCategorias': return $this->obtenerCategorias();
            default: return ['error' => 'Acción no válida'];
        }
    }

    

    

    private function obtenerActivos() {
        try {
            $stmt = $this->conn->prepare("
                SELECT
                    id_producto,
                    nombre_producto,
                    descripcion,
                    precio_detal,
                    stock_actual,
                    imagen_principal,
                    estado,
                    id_categoria
                FROM productos
                WHERE estado = 1
                ORDER BY nombre_producto ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error en ProductoModel::_obtenerActivos: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerCategorias() {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT
                    c.id_categoria,
                    c.nombre_categoria
                FROM categorias c
                INNER JOIN productos p ON p.id_categoria = c.id_categoria
                WHERE p.estado = 1
                ORDER BY c.nombre_categoria ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error en ProductoModel::_obtenerCategorias: " . $e->getMessage());
            return [];
        }
    }

    private function obtenerPorId($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT
                    id_producto,
                    nombre_producto,
                    descripcion,
                    precio_detal,
                    stock_actual,
                    imagen_principal
                FROM productos
                WHERE id_producto = ? AND estado = 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Error en ProductoModel::_obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    private function verificarStock($id, $cantidad) {
        try {
            $stmt = $this->conn->prepare("
                SELECT stock_actual FROM productos WHERE id_producto = ? AND estado = 1
            ");
            $stmt->execute([$id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                return ['disponible' => false, 'mensaje' => 'Producto no encontrado'];
            }

            if ($producto['stock_actual'] < $cantidad) {
                return [
                    'disponible' => false,
                    'mensaje' => 'Stock insuficiente. Disponible: ' . $producto['stock_actual']
                ];
            }

            return ['disponible' => true, 'stock' => $producto['stock_actual']];

        } catch (\PDOException $e) {
            error_log("Error en ProductoModel::_verificarStock: " . $e->getMessage());
            return ['disponible' => false, 'mensaje' => 'Error al verificar stock'];
        }
    }

    private function descontarStock($id, $cantidad) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE productos
                SET stock_actual = stock_actual - ?
                WHERE id_producto = ? AND stock_actual >= ?
            ");
            $resultado = $stmt->execute([$cantidad, $id, $cantidad]);
            return $stmt->rowCount() > 0;

        } catch (\PDOException $e) {
            error_log("Error en ProductoModel::_descontarStock: " . $e->getMessage());
            return false;
        }
    }
}
