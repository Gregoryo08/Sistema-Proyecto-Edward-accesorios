<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use Exception;
use PDOException;

class reporteVentas extends Conexion {

    public function __construct(){
        parent::__construct();
    }

    public function procesarSolicitud($accion, $id_venta = null, $datos = null){
        switch($accion){
            case 'listarVentas':
                return $this->listarVentas($datos);
            case 'detallesVentas':
                return $this->detallesVentas($id_venta, $datos);            
            case 'anularVenta':
                return $this->anularVenta($id_venta, $datos);
            default:
                return [
                    "success" => false, 
                    "mensaje" => "Acción no reconocida en el modelo de reporte ventas."
                ];
        }
    }

    private function listarVentas($filtros = null){
        try {
            $sql = "SELECT 
                        v.id_venta,
                        v.fecha_venta,
                        v.origen_venta,
                        v.total_venta,
                        c.cedula_persona AS cedula_cliente,  
                        e.cedula_persona AS cedula_empleado,
                        v.estado AS estado_venta,
                        c.nombre AS nombre_cliente,
                        c.apellido AS apellido_cliente,
                        e.nombre AS nombre_empleado,
                        e.apellido AS apellido_empleado
                    FROM ventas v
                    LEFT JOIN persona c ON v.cedula_persona = c.cedula_persona
                    LEFT JOIN empleados emp ON v.cedula_empleado = emp.cedula_persona
                    LEFT JOIN persona e ON emp.cedula_persona = e.cedula_persona
                    WHERE 1=1"; // 1=1 permite concatenar condiciones dinámicamente

            $params = [];

            if (is_array($filtros) && !empty($filtros)) {
                
                if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
                    $sql .= " AND v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin";
                    $params[':fecha_inicio'] = $filtros['fecha_inicio'] . " 00:00:00";
                    $params[':fecha_fin'] = $filtros['fecha_fin'] . " 23:59:59";
                }

                if (!empty($filtros['origen'])) {
                    $sql .= " AND v.origen_venta = :origen";
                    $params[':origen'] = $filtros['origen'];
                }

                if (!empty($filtros['estado'])) {
                    $sql .= " AND v.estado = :estado";
                    $params[':estado'] = $filtros['estado'];
                }

                if (!empty($filtros['buscar'])) {
                    $sql .= " AND (v.id_venta = :buscar_id 
                                OR c.nombre LIKE :buscar_text 
                                OR c.apellido LIKE :buscar_text 
                                OR c.cedula_persona LIKE :buscar_ced
                                OR emp.cedula_persona LIKE :buscar_emp
                                OR e.nombre LIKE :buscar_text
                                OR e.apellido LIKE :buscar_text)";
                    
                    $params[':buscar_id']  = $filtros['buscar'];
                    $params[':buscar_text'] = "%" . $filtros['buscar'] . "%";
                    $params[':buscar_ced']  = "%" . $filtros['buscar'] . "%";
                    $params[':buscar_emp']  = "%" . $filtros['buscar'] . "%";
                }
            }

            $sql .= " ORDER BY v.id_venta DESC;";

            $stmt = $this->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en ventas::listarVentas -> " . $e->getMessage());
            return [];
        }
    }

    private function detallesVentas($id_venta, $datos){
        try {
            if ($id_venta === null) {
                return [];
            }

            $sql = "SELECT 
                        dv.id_producto,
                        p.nombre_producto,
                        dv.cantidad,
                        dv.precio_unitario,
                        dv.subtotal
                    FROM sistema_edward.detalle_venta dv
                    INNER JOIN sistema_edward.productos p ON dv.id_producto = p.id_producto
                    WHERE dv.id_venta = :id_venta";
                    
            $stmt = $this->prepare($sql);
            $stmt->execute([':id_venta' => $id_venta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ventas::detallesVentas -> " . $e->getMessage());
            return [];
        }
    }

    public function anularVenta($id_venta, $datos){
        if($id_venta === null){
            return [
                "success" => false,
                "mensaje" => "Falta el ID de la venta para anular."
            ];
        }

        try {
            $this->beginTransaction();

            $sqlSesion = "SET @usuario_actual = :usuario, @modulo = :modulo";
            $stmtSesion = $this->prepare($sqlSesion);
            $stmtSesion->execute([
                ':usuario' => $datos['cedula_usuario'],
                ':modulo'  => 'Administrar Ventas'                  
            ]);

            $sqlCheck = "SELECT estado, origen_venta, total_venta, cedula_persona FROM ventas WHERE id_venta = :id_venta FOR UPDATE";
            $stmtCheck = $this->prepare($sqlCheck);
            $stmtCheck->execute([':id_venta' => $id_venta]);
            $venta = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                $this->rollBack();
                return ["success" => false, "mensaje" => "La venta no existe."];
            }

            if (strtolower($venta['estado']) === 'anulada') {
                $this->rollBack();
                return ["success" => false, "mensaje" => "Esta venta ya se encuentra anulada."];
            }

            $sqlDetalle = "SELECT id_producto, cantidad FROM detalle_venta WHERE id_venta = :id_venta";
            $stmtDetalle = $this->prepare($sqlDetalle);
            $stmtDetalle->execute([':id_venta' => $id_venta]);
            $productosVendidos = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

            $sqlStock = "UPDATE productos 
                        SET stock_actual = stock_actual + :cantidad 
                        WHERE id_producto = :id_producto";
            $stmtStock = $this->prepare($sqlStock);

            foreach ($productosVendidos as $prod) {
                $stmtStock->execute([
                    ':cantidad'   => $prod['cantidad'],
                    ':id_producto' => $prod['id_producto']
                ]);
            }

            $sqlAnular = "UPDATE ventas 
                        SET estado = 'anulada' 
                        WHERE id_venta = :id_venta";
            $stmtAnular = $this->prepare($sqlAnular);
            $stmtAnular->execute([':id_venta' => $id_venta]);

            $this->commit();

            return [
                "success" => true,
                "mensaje" => "La venta #{$id_venta} ha sido anulado(a) con éxito."
            ];

        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error General en reporteVentas::anularVenta -> " . $e->getMessage());
            return [
                "success" => false,
                "mensaje" => "Error General: " . $e->getMessage()
            ];
        }
    }
}