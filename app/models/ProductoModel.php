<?php
// /src/app/models/ProductoModel.php
// =============================================
// MODELO DE PRODUCTOS
// =============================================

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;

class ProductoModel {
    private $conn;
    private $id_producto;
    private $nombre;
    private $precio;
    private $stock;

    public function setIdProducto($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('ID de producto inválido');
        }
        $this->id_producto = $valor;
        return $this;
    }

    public function getIdProducto() {
        return $this->id_producto;
    }

    public function setNombre($valor) {
        $valor = trim($valor);
        if (empty($valor)) {
            throw new \InvalidArgumentException('Nombre de producto inválido');
        }
        $this->nombre = $valor;
        return $this;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setPrecio($valor) {
        if (!is_numeric($valor) || $valor < 0) {
            throw new \InvalidArgumentException('Precio inválido');
        }
        $this->precio = $valor;
        return $this;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function setStock($valor) {
        if (!is_numeric($valor) || $valor < 0) {
            throw new \InvalidArgumentException('Stock inválido');
        }
        $this->stock = (int)$valor;
        return $this;
    }

    public function getStock() {
        return $this->stock;
    }

    public function __construct() {
        $this->conn = Conexion::getShared('sistema')->getConexion();
    }

    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'obtenerActivos': return $this->_obtenerActivos();
            case 'obtenerPorId': return $this->_obtenerPorId($datos['id']);
            case 'verificarStock': return $this->_verificarStock($datos['id'], $datos['cantidad']);
            case 'descontarStock': return $this->_descontarStock($datos['id'], $datos['cantidad']);
            case 'obtenerCategorias': return $this->_obtenerCategorias();
            default: return ['error' => 'Acción no válida'];
        }
    }

    private function _obtenerActivos() {
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

    private function _obtenerCategorias() {
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

    private function _obtenerPorId($id) {
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

    private function _verificarStock($id, $cantidad) {
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

    private function _descontarStock($id, $cantidad) {
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