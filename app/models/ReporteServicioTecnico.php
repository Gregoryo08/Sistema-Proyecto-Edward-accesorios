<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use PDOException;

class ReporteServicioTecnico extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    private function normalizarBusquedaServicio($valor)
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
                'valor' => $prefijo . '-' . $numeros, 
                'valor_numerico' => $numeros           
            ];
        }

        return [
            'tipo' => 'texto',
            'valor' => preg_replace('/\s+/', ' ', $valor),
            'valor_numerico' => null
        ];
    }

    public function listar(array $filtros = [])
    {
        try {
            $sql = "SELECT
                        st.id_servicio,
                        st.cedula_persona,
                        CONCAT(COALESCE(per.nombre, ''), ' ', COALESCE(per.apellido, '')) AS cliente,
                        st.equipo_descripcion,
                        st.falla_inicial,
                        st.estado,
                        COALESCE(st.monto_total, 0) AS monto_total,
                        st.fecha_registro,
                        COALESCE(e.nombre_especialidad, 'Sin especialidad') AS especialidad,
                        dst.diagnostico,
                        dst.nota_tecnico
                    FROM servicio_tecnico st
                    LEFT JOIN persona per ON per.cedula_persona = st.cedula_persona
                    LEFT JOIN detalles_servicio_tecnico dst ON dst.id_servicio = st.id_servicio
                    LEFT JOIN especialidades e ON e.id_especialidad = dst.id_especialidad
                    WHERE 1 = 1";
            $params = [];

            if (!empty($filtros['buscar'])) {
                $busqueda = $this->normalizarBusquedaServicio($filtros['buscar']);

                if ($busqueda['tipo'] === 'cedula') {
                   
                    $sql .= " AND (
                        LOWER(st.cedula_persona) = LOWER(:cedula_formato)
                        OR LOWER(per.cedula_persona) = LOWER(:cedula_formato)
                        OR LOWER(st.cedula_persona) = LOWER(:cedula_num)
                        OR LOWER(per.cedula_persona) = LOWER(:cedula_num)
                        OR CAST(st.id_servicio AS CHAR) = :id_servicio
                    )";
                    $params[':cedula_formato'] = $busqueda['valor'];
                    $params[':cedula_num'] = $busqueda['valor_numerico'];
                    $params[':id_servicio'] = $busqueda['valor_numerico'];
                } else {
                    
                    $sql .= " AND (
                        LOWER(TRIM(CONCAT(COALESCE(per.nombre, ''), ' ', COALESCE(per.apellido, '')))) LIKE :texto
                        OR LOWER(st.equipo_descripcion) LIKE :texto
                        OR LOWER(st.falla_inicial) LIKE :texto
                    )";
                    $params[':texto'] = '%' . strtolower($busqueda['valor']) . '%';
                }
            }

            if (!empty($filtros['estado'])) {
                $sql .= " AND st.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }

            if (!empty($filtros['especialidad'])) {
                $sql .= " AND dst.id_especialidad = :especialidad";
                $params[':especialidad'] = (int) $filtros['especialidad'];
            }

            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND st.fecha_registro >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'] . ' 00:00:00';
            }

            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND st.fecha_registro <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'] . ' 23:59:59';
            }

            $sql .= " ORDER BY st.id_servicio DESC";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error en reporte de servicio técnico: ' . $e->getMessage());
            return [];
        }
    }

    public function listarEspecialidades()
    {
        try {
            $stmt = $this->query("SELECT id_especialidad, nombre_especialidad FROM especialidades ORDER BY nombre_especialidad");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}