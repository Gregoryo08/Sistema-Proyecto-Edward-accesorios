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
                $sql .= " AND (CAST(st.id_servicio AS CHAR) LIKE :buscar_id
                    OR st.cedula_persona LIKE :buscar_cedula
                    OR per.nombre LIKE :buscar_nombre
                    OR per.apellido LIKE :buscar_apellido
                    OR st.equipo_descripcion LIKE :buscar_equipo
                    OR st.falla_inicial LIKE :buscar_falla)";
                $params[':buscar_id'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar_cedula'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar_nombre'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar_apellido'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar_equipo'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar_falla'] = '%' . $filtros['buscar'] . '%';
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
