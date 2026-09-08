<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class reportesentradas extends Conexion
{
    private $conex;

    public function __construct()
    {
        parent::__construct();
        $this->conex = new Conexion("sistema");
    }

    public function obtenerEntradasPorCategoria($fechaInicio = null, $fechaFin = null)
    {
        try {
            $sql = "SELECT c.nombre_categoria AS nombre, SUM(d.cantidad_entrada) AS total 
                    FROM detalles_entrada d
                    INNER JOIN productos p ON d.id_producto_fk = p.id_producto
                    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                    INNER JOIN entradas_productos e ON d.id_entrada_fk = e.id_entrada";

            $params = [];
            $sql .= " WHERE 1=1";

            if (!empty($fechaInicio)) {
                $sql .= " AND e.fecha_entrada >= :inicio";
                $params[':inicio'] = $fechaInicio;
            }
            if (!empty($fechaFin)) {
                $sql .= " AND e.fecha_entrada <= :fin";
                $params[':fin'] = $fechaFin . " 23:59:59";
            }

            $sql .= " GROUP BY c.id_categoria ORDER BY total DESC";

            $stmt = $this->conex->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function obtenerTopProductos($fechaInicio = null, $fechaFin = null)
    {
        try {
            $sql = "SELECT p.nombre_producto AS nombre, SUM(d.cantidad_entrada) AS total 
                    FROM detalles_entrada d
                    INNER JOIN productos p ON d.id_producto_fk = p.id_producto
                    INNER JOIN entradas_productos e ON d.id_entrada_fk = e.id_entrada";

            $params = [];
            $sql .= " WHERE 1=1";

            if (!empty($fechaInicio)) {
                $sql .= " AND e.fecha_entrada >= :inicio";
                $params[':inicio'] = $fechaInicio;
            }
            if (!empty($fechaFin)) {
                $sql .= " AND e.fecha_entrada <= :fin";
                $params[':fin'] = $fechaFin . " 23:59:59";
            }

            $sql .= " GROUP BY p.id_producto ORDER BY total DESC LIMIT 10";

            $stmt = $this->conex->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function obtenerReporteDetallado($fecha = null, $producto = null)
    {
        try {
            $sql = "SELECT 
                        e.id_entrada,
                        p.nombre_proveedor,
                        pr.nombre_producto,
                        d.cantidad_entrada AS cantidad,
                        e.fecha_entrada,
                        d.dias_garantia
                   FROM entradas_productos e
                   INNER JOIN proveedores p ON e.rif_proveedor_fk = p.rif_proveedor
                   INNER JOIN detalles_entrada d ON e.id_entrada = d.id_entrada_fk
                   INNER JOIN productos pr ON d.id_producto_fk = pr.id_producto
                   WHERE 1=1";

            $params = [];

            if (!empty($fecha)) {
                $sql .= " AND DATE(e.fecha_entrada) = :fecha";
                $params[':fecha'] = $fecha;
            }
            if (!empty($producto)) {
                $sql .= " AND pr.id_producto = :producto";
                $params[':producto'] = $producto;
            }

            $sql .= " ORDER BY e.fecha_entrada DESC, e.id_entrada DESC";

            $stmt = $this->conex->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
}