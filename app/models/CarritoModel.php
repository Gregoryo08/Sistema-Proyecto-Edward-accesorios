<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;

class CarritoModel {
    private $conn;

    public function __construct() {
        $this->conn = Conexion::getShared('sistema')->getConexion();
    }

    public function obtenerProductos($ids) {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->conn->prepare("
            SELECT id_producto, nombre_producto, precio_detal, stock_actual, imagen_principal
            FROM productos
            WHERE id_producto IN ($placeholders) AND estado = 1
        ");
        $stmt->execute($ids);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = [];
        foreach ($productos as $p) {
            $resultado[$p['id_producto']] = $p;
        }
        return $resultado;
    }

    public function calcularTotal($items) {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['precio'] ?? 0) * ($item['cantidad'] ?? 0);
        }
        $costo_envio = $subtotal >= 100 ? 0 : 10;
        return [
            'subtotal' => round($subtotal, 2),
            'costo_envio' => $costo_envio,
            'total' => round($subtotal + $costo_envio, 2)
        ];
    }

    public function verificarStock($id_producto, $cantidad) {
        $stmt = $this->conn->prepare("
            SELECT stock_actual FROM productos WHERE id_producto = ? AND estado = 1
        ");
        $stmt->execute([$id_producto]);
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
    }
}
