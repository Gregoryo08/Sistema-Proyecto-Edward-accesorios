<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class Reportefinanciamiento extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    private function normalizarBusquedaFinanciamiento($valor)
    {
        if ($valor === null) {
            return ['tipo' => null, 'valor' => null, 'valor_numerico' => null];
        }

        $valor = trim((string) $valor);
        if ($valor === '') {
            return ['tipo' => null, 'valor' => null, 'valor_numerico' => null];
        }

        $valorLimpio = preg_replace('/\s+/', '', $valor);

        if (preg_match('/^[A-Za-z]?-?\d+$/', $valorLimpio)) {
           
            $numeros = preg_replace('/\D/', '', $valorLimpio);
            
            $prefijo = 'V';
            if (preg_match('/^([A-Za-z])/', $valorLimpio, $coincidencias)) {
                $prefijo = strtoupper($coincidencias[1]);
            }

            return [
                'tipo' => 'cedula',
                'valor' => $prefijo . '-' . $numeros, // Formato "V-30753799"
                'valor_numerico' => $numeros           // Formato "30753799"
            ];
        }

        return [
            'tipo' => 'nombre',
            'valor' => preg_replace('/\s+/', ' ', $valor),
            'valor_numerico' => null
        ];
    }

    public function obtenerConteoFinanciamientosPorEstado($cedula = null, $estado = null, $fecha_desde = null, $fecha_hasta = null, $monto_min = null, $monto_max = null)
    {
        try {
            $sql = "SELECT estado_financiamiento AS estado, COUNT(*) AS total 
                    FROM financiamientos f
                    LEFT JOIN clientes c ON f.cedula_persona = c.cedula_persona
                    LEFT JOIN persona per ON c.cedula_persona = per.cedula_persona
                    WHERE 1=1";
            
            $params = [];
            if (!empty($cedula)) {
                $busqueda = $this->normalizarBusquedaFinanciamiento($cedula);
                if ($busqueda['tipo'] === 'cedula') {
                    $sql .= " AND (
                        LOWER(f.cedula_persona) = LOWER(:cedula) 
                        OR LOWER(per.cedula_persona) = LOWER(:cedula)
                        OR LOWER(f.cedula_persona) = LOWER(:cedula_num) 
                        OR LOWER(per.cedula_persona) = LOWER(:cedula_num)
                    )";
                    $params[':cedula'] = $busqueda['valor'];
                    $params[':cedula_num'] = $busqueda['valor_numerico'];
                } else {
                    $sql .= " AND LOWER(TRIM(CONCAT(COALESCE(per.nombre, ''), ' ', COALESCE(per.apellido, '')))) LIKE :nombre";
                    $params[':nombre'] = '%' . strtolower($busqueda['valor']) . '%';
                }
            }
            if (!empty($estado) && $estado !== 'todos') {
                $sql .= " AND estado_financiamiento = :estado";
                $params[':estado'] = $estado;
            }
            if (!empty($fecha_desde)) {
                $sql .= " AND fecha_inicio >= :fecha_desde";
                $params[':fecha_desde'] = $fecha_desde;
            }
            if (!empty($fecha_hasta)) {
                $sql .= " AND fecha_inicio <= :fecha_hasta";
                $params[':fecha_hasta'] = $fecha_hasta;
            }
            if (!empty($monto_min) && floatval($monto_min) > 0) {
                $sql .= " AND monto_total >= :monto_min";
                $params[':monto_min'] = $monto_min;
            }
            if (!empty($monto_max) && floatval($monto_max) > 0) {
                $sql .= " AND monto_total <= :monto_max";
                $params[':monto_max'] = $monto_max;
            }

            $sql .= " GROUP BY estado_financiamiento";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerCuotasPorMes($fecha_desde = null, $fecha_hasta = null)
    {
        try {
            $sql = "SELECT DATE_FORMAT(fecha_vencimiento, '%Y-%m') AS mes, 
                           COUNT(id_cuota) AS total_cuotas 
                    FROM cuotas WHERE 1=1";
            
            $params = [];
            if (!empty($fecha_desde)) {
                $sql .= " AND fecha_vencimiento >= :fecha_desde";
                $params[':fecha_desde'] = $fecha_desde;
            }
            if (!empty($fecha_hasta)) {
                $sql .= " AND fecha_vencimiento <= :fecha_hasta";
                $params[':fecha_hasta'] = $fecha_hasta;
            }

            $sql .= " GROUP BY mes ORDER BY mes DESC";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerReporteFinanciamientoFiltro($cedula = null, $estado = null, $fecha_desde = null, $fecha_hasta = null)
    {
        try {
            $sql = "SELECT f.id_financiamiento, per.cedula_persona, 
                           CONCAT(per.nombre, ' ', per.apellido) AS nombre_cliente, 
                           p.nombre_producto, f.monto_total, f.estado_financiamiento, 
                           df.estado_equipo, f.fecha_inicio AS fecha_financiamiento
                    FROM financiamientos f
                    LEFT JOIN clientes c ON f.cedula_persona = c.cedula_persona
                    LEFT JOIN persona per ON c.cedula_persona = per.cedula_persona
                    LEFT JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                    LEFT JOIN productos p ON df.id_productos = p.id_producto 
                    WHERE 1=1";

            $params = [];
            if (!empty($cedula)) {
                $busqueda = $this->normalizarBusquedaFinanciamiento($cedula);
                if ($busqueda['tipo'] === 'cedula') {
                    $sql .= " AND (
                        LOWER(f.cedula_persona) = LOWER(:cedula) 
                        OR LOWER(per.cedula_persona) = LOWER(:cedula)
                        OR LOWER(f.cedula_persona) = LOWER(:cedula_num) 
                        OR LOWER(per.cedula_persona) = LOWER(:cedula_num)
                    )";
                    $params[':cedula'] = $busqueda['valor'];
                    $params[':cedula_num'] = $busqueda['valor_numerico'];
                } else {
                    $sql .= " AND LOWER(TRIM(CONCAT(COALESCE(per.nombre, ''), ' ', COALESCE(per.apellido, '')))) LIKE :nombre";
                    $params[':nombre'] = '%' . strtolower($busqueda['valor']) . '%';
                }
            }
            if (!empty($estado) && $estado !== 'todos') {
                $sql .= " AND f.estado_financiamiento = :estado";
                $params[':estado'] = $estado;
            }
            if (!empty($fecha_desde)) {
                $sql .= " AND f.fecha_inicio >= :fecha_desde";
                $params[':fecha_desde'] = $fecha_desde;
            }
            if (!empty($fecha_hasta)) {
                $sql .= " AND f.fecha_inicio <= :fecha_hasta";
                $params[':fecha_hasta'] = $fecha_hasta;
            }

            $sql .= " ORDER BY f.id_financiamiento DESC";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerMorosidadActual()
    {
        try {
            $sql = "SELECT f.id_financiamiento, per.nombre, per.apellido, 
                           COUNT(cu.id_cuota) AS cuotas_vencidas
                    FROM financiamientos f
                    JOIN clientes c ON f.cedula_persona = c.cedula_persona
                    JOIN persona per ON c.cedula_persona = per.cedula_persona
                    JOIN cuotas cu ON f.id_financiamiento = cu.id_financiamiento
                    WHERE cu.estado_cuota = 'pendiente' 
                    AND cu.fecha_vencimiento < CURDATE()
                    AND f.estado_financiamiento = 'vigente'
                    GROUP BY f.id_financiamiento";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerReporteAvanzado(array $filtros = [])
    {
        try {
            $sql = "SELECT 
                        per.cedula_persona, 
                        CONCAT(per.nombre, ' ', per.apellido) AS cliente,
                        COUNT(f.id_financiamiento) AS total_financiamientos,
                        SUM(f.monto_total) AS monto_acumulado,
                        GROUP_CONCAT(DISTINCT p.nombre_producto SEPARATOR ', ') AS productos
                    FROM financiamientos f
                    JOIN clientes c ON f.cedula_persona = c.cedula_persona
                    JOIN persona per ON c.cedula_persona = per.cedula_persona
                    LEFT JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                    LEFT JOIN productos p ON df.id_productos = p.id_producto
                    WHERE 1=1";

            $params = [];

            if (!empty($filtros['cedula'])) {
                $busqueda = $this->normalizarBusquedaFinanciamiento($filtros['cedula']);
                if ($busqueda['tipo'] === 'cedula') {
                    $sql .= " AND (
                        LOWER(f.cedula_persona) = LOWER(:cedula) 
                        OR LOWER(per.cedula_persona) = LOWER(:cedula)
                        OR LOWER(f.cedula_persona) = LOWER(:cedula_num) 
                        OR LOWER(per.cedula_persona) = LOWER(:cedula_num)
                    )";
                    $params[':cedula'] = $busqueda['valor'];
                    $params[':cedula_num'] = $busqueda['valor_numerico'];
                } else {
                    $sql .= " AND LOWER(TRIM(CONCAT(COALESCE(per.nombre, ''), ' ', COALESCE(per.apellido, '')))) LIKE :nombre";
                    $params[':nombre'] = '%' . strtolower($busqueda['valor']) . '%';
                }
            }

            if (!empty($filtros['estado']) && $filtros['estado'] !== 'todos') {
                $sql .= " AND f.estado_financiamiento = :estado";
                $params[':estado'] = $filtros['estado'];
            }

            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND f.fecha_inicio >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }

            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND f.fecha_inicio <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }

            if (!empty($filtros['monto_min']) && floatval($filtros['monto_min']) > 0) {
                $sql .= " AND f.monto_total >= :monto_min";
                $params[':monto_min'] = $filtros['monto_min'];
            }

            if (!empty($filtros['monto_max']) && floatval($filtros['monto_max']) > 0) {
                $sql .= " AND f.monto_total <= :monto_max";
                $params[':monto_max'] = $filtros['monto_max'];
            }

            $sql .= " GROUP BY per.cedula_persona";

            $campoOrden = ($filtros['ordenar_por'] ?? '') === 'monto' ? 'monto_acumulado' : 'total_financiamientos';
            $sql .= " ORDER BY $campoOrden DESC";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}