<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use Exception;

class ReporteVentasOnline extends Conexion {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Listado de pedidos de la tienda online con su estado de pago y despacho.
     * Cada pedido aparece una sola vez (se agregan los reportes de pago y despachos asociados).
     */
    public function listar($filtros = null) {
        try {
            $sql = "SELECT
                        p.id_pedido,
                        p.cedula_persona,
                        p.nombre_cliente,
                        p.telefono_cliente,
                        p.fecha,
                        p.subtotal,
                        p.costo_envio,
                        p.total,
                        p.metodo_pago,
                        p.estado AS estado_pedido,
                        MAX(po.referencia)        AS referencia,
                        MAX(po.banco_emisor)      AS banco_emisor,
                        MAX(po.monto_reportado)   AS monto_reportado,
                        MAX(po.fecha_reporte)     AS fecha_reporte,
                        MAX(po.estado_verificacion) AS estado_pago,
                        COUNT(po.id_reporte)      AS total_reportes,
                        MAX(d.tipo_despacho)      AS tipo_despacho,
                        MAX(d.estado_despacho)    AS estado_despacho
                    FROM pedidos p
                    LEFT JOIN pago_online po ON po.id_pedido = p.id_pedido
                    LEFT JOIN despachos d ON d.id_pedido = p.id_pedido
                    WHERE 1=1";

            $params = [];

            if (is_array($filtros)) {
                if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
                    $sql .= " AND p.fecha BETWEEN :fecha_inicio AND :fecha_fin";
                    $params[':fecha_inicio'] = $filtros['fecha_inicio'] . " 00:00:00";
                    $params[':fecha_fin'] = $filtros['fecha_fin'] . " 23:59:59";
                }

                if (!empty($filtros['estado_pedido'])) {
                    $sql .= " AND p.estado = :estado_pedido";
                    $params[':estado_pedido'] = $filtros['estado_pedido'];
                }

                if (!empty($filtros['metodo_pago'])) {
                    $sql .= " AND p.metodo_pago = :metodo_pago";
                    $params[':metodo_pago'] = $filtros['metodo_pago'];
                }

                if (!empty($filtros['buscar'])) {
                    $sql .= " AND (
                                p.id_pedido = :buscar_id
                                OR p.nombre_cliente LIKE :buscar_nombre
                                OR p.cedula_persona LIKE :buscar_cedula
                                OR p.telefono_cliente LIKE :buscar_telefono
                                OR po.referencia LIKE :buscar_referencia
                              )";
                    $params[':buscar_id']       = $filtros['buscar'];
                    $params[':buscar_nombre']   = "%" . $filtros['buscar'] . "%";
                    $params[':buscar_cedula']   = "%" . $filtros['buscar'] . "%";
                    $params[':buscar_telefono'] = "%" . $filtros['buscar'] . "%";
                    $params[':buscar_referencia'] = "%" . $filtros['buscar'] . "%";
                }
            }

            $sql .= " GROUP BY p.id_pedido";

            // El estado del pago se evalúa sobre el último reporte agregado
            if (is_array($filtros) && !empty($filtros['estado_pago'])) {
                $sql .= " HAVING MAX(po.estado_verificacion) = :estado_pago";
                $params[':estado_pago'] = $filtros['estado_pago'];
            }

            $sql .= " ORDER BY p.id_pedido DESC";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en ReporteVentasOnline::listar -> " . $e->getMessage());
            return [];
        }
    }

    /**
     * Métodos de pago distintos encontrados en los pedidos
     * (para llenar dinámicamente el filtro del reporte).
     */
    public function listarMetodosPago() {
        try {
            $stmt = $this->prepare(
                "SELECT DISTINCT metodo_pago FROM pedidos
                 WHERE metodo_pago IS NOT NULL AND metodo_pago != ''
                 ORDER BY metodo_pago"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Error en ReporteVentasOnline::listarMetodosPago -> " . $e->getMessage());
            return [];
        }
    }
}