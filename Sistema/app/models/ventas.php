<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use Exception;

class ventas extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listarMetodos()
    {
        $conex = new Conexion("sistema");
        $stmt = $conex->query("SELECT * FROM metodo_pago WHERE estatus = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarProductos($busqueda)
    {
        $conex = new Conexion("sistema");
        $sql = "SELECT id_producto as id, nombre_producto as text, precio_detal as precio, stock_actual as stock 
                FROM productos 
                WHERE nombre_producto LIKE :q AND estado = 1 AND stock_actual > 0";
        $stmt = $conex->prepare($sql);
        $stmt->execute([':q' => '%' . $busqueda . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarVentasHistorial()
    {
        $conex = new Conexion("sistema");
        $sql = "SELECT v.id_venta, v.fecha, v.total, v.metodo_pago, v.direccion as cliente, v.referencia_pago 
                FROM ventas v 
                WHERE v.estado = 1 
                ORDER BY v.fecha DESC";
        $stmt = $conex->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleVenta($id)
{
    $conex = new Conexion("sistema");
    $sql = "SELECT d.*, p.nombre_producto 
            FROM detalle_ventas d 
            INNER JOIN productos p ON d.id_producto = p.id_producto 
            WHERE d.id_venta = :id";
    $stmt = $conex->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function registrarVentaDirecta($datos)
    {
        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = "Cliente Genérico";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = 'Administrar Ventas'");

            $sqlVenta = "INSERT INTO ventas (id_usuario, fecha, total, metodo_pago, direccion, telefono, estado, referencia_pago) 
                         VALUES (:u, NOW(), :t, :m, 'Venta Presencial', 'N/A', 1, :r)";
            $stmtVenta = $conex->prepare($sqlVenta);
            $stmtVenta->execute([
                ':u' => $user,
                ':t' => $datos['total'],
                ':m' => $datos['metodo'],
                ':r' => $datos['referencia'] ?? null
            ]);

            $id_venta = $conex->lastInsertId();

            foreach ($datos['productos'] as $p) {
                $stmtDetalle = $conex->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                $stmtDetalle->execute([$id_venta, $p['id'], $p['cant'], $p['pre']]);

                $stmtStock = $conex->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ? AND stock_actual >= ?");
                $stmtStock->execute([$p['cant'], $p['id'], $p['cant']]);

                if ($stmtStock->rowCount() == 0) {
                    throw new Exception("Stock insuficiente para uno de los productos.");
                }
            }

            $conex->commit();
            return ["success" => true, "mensaje" => "Venta #" . $id_venta . " registrada exitosamente."];

        } catch (Exception $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
}