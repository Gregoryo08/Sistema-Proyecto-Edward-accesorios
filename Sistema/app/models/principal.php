<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;
use \Throwable;

class Dashboard extends Conexion
{
    public function __construct()
    {
    }

    public function Cargaringreso()
    {
        $conex = new Conexion("sistema");
        $data = [];
        try {
            $stmt = $conex->prepare("SELECT IFNULL(SUM(ph.monto), 0) AS ingresos_hoy FROM pagos_habitacion ph WHERE DATE(ph.fecha_pago) = CURDATE();");
            $stmt->execute();
            $data['ingresosHoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['ingresos_hoy'];

            $stmt = $conex->prepare("SELECT IFNULL(SUM(ph.monto), 0) AS ingresos_semana FROM pagos_habitacion ph WHERE YEARWEEK(ph.fecha_pago, 1) = YEARWEEK(CURDATE(), 1);");
            $stmt->execute();
            $data['ingresosSemana'] = $stmt->fetch(PDO::FETCH_ASSOC)['ingresos_semana'];

            $stmt = $conex->prepare("SELECT IFNULL(SUM(ph.monto), 0) AS ingresos_mes FROM pagos_habitacion ph WHERE MONTH(ph.fecha_pago) = MONTH(CURDATE()) AND YEAR(ph.fecha_pago) = YEAR(CURDATE());");
            $stmt->execute();
            $data['ingresosMes'] = $stmt->fetch(PDO::FETCH_ASSOC)['ingresos_mes'];

            $stmt = $conex->prepare("SELECT COUNT(*) AS habitaciones_ocupadas FROM habitacion WHERE estado = 'ocupada';");
            $stmt->execute();
            $data['habitacionesOcupadas'] = $stmt->fetch(PDO::FETCH_ASSOC)['habitaciones_ocupadas'];

            $stmt = $conex->prepare("SELECT COUNT(*) AS total_habitaciones FROM habitacion;");
            $stmt->execute();
            $data['totalHabitaciones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_habitaciones'];
            
            $stmt = $conex->prepare("SELECT COUNT(*) AS llegadas_hoy FROM alquiler WHERE DATE(fecha_entrada) = CURDATE() AND tipo = 'alquiler';");
            $stmt->execute();
            $data['llegadasHoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['llegadas_hoy'];

            $stmt = $conex->prepare("SELECT COUNT(*) AS salidas_hoy FROM salida_alquiler WHERE DATE(fecha_salida) = CURDATE();");
            $stmt->execute();
            $data['salidasHoy'] = $stmt->fetch(PDO::FETCH_ASSOC)['salidas_hoy'];

            return $data;
        } catch (PDOException $e) {
            error_log("Error en Dashboard::getOverviewData: " . $e->getMessage());
            return ["error" => "Error al obtener datos de visión general: " . $e->getMessage()];
        } finally {
            unset($conex);
        }
    }


    public function obtenerDatosPersonales($cedula)
    {
        $cedula = trim($cedula);
        
        try {
            $conex = new Conexion("sistema"); 
            $stmt = $conex->prepare("SELECT nombre, apellido FROM empleados WHERE cedula_empleado = :c");
            $stmt->bindParam(":c", $cedula);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            error_log("Error SQL en obtenerDatosPersonales: " . $th->getMessage());
            return false;
        }
    }

    public function Estadohabitacion()
    {
        $conex = new Conexion("sistema");
        try {
            $stmt = $conex->prepare("SELECT estado AS estado, COUNT(*) AS cantidad FROM habitacion GROUP BY estado;");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $estadosExistentes = array_column($data, 'estado');
            $estadosCompletos = ['Ocupado', 'disponible', 'Limpieza', 'Mantenimiento', 'fuera de servicio'];
            
            foreach ($estadosCompletos as $estado) {
                if (!in_array($estado, $estadosExistentes)) {
                    $data[] = ['estado' => $estado, 'cantidad' => 0];
                }
            }

            return $data;
        } catch (PDOException $e) {
            error_log("Error en Dashboard::Estadohabitacion: " . $e->getMessage());
            return ["error" => "Error al obtener datos de estado de habitaciones: " . $e->getMessage()];
        } finally {
            unset($conex);
        }
    }

    public function Alquileractivo()
    {
        $conex = new Conexion("sistema");
        try {
            $stmt = $conex->prepare("
                SELECT
                    hpa.id_habitacion,
                    c.nombre AS nombre_cliente,
                    c.apellido AS apellido_cliente,
                    DATE_FORMAT(a.fecha_entrada, '%Y-%m-%d %H:%i') AS fecha_entrada_formato,
                    a.tiempo_alquiler,
                    (SELECT COUNT(*) FROM acompanantes aa WHERE aa.id_alquiler = a.id_alquiler) AS num_acompanantes,
                    a.placa
                FROM
                    alquiler a
                JOIN
                    clientes c ON a.cedula_cliente = c.cedula_cliente
                JOIN
                    habitaciones_por_alquier hpa ON a.id_alquiler = hpa.id_alquiler
                WHERE
                    a.status = 'en curso'
                ORDER BY a.fecha_entrada DESC;
            ");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $data;
        } catch (PDOException $e) {
            error_log("Error en Dashboard::Alquileractivo: " . $e->getMessage());
            return ["error" => "Error al obtener alquileres activos: " . $e->getMessage()];
        } finally {
            unset($conex);
        }
    }
    
    public function Reservacionsemanal()
    {
        $conex = new Conexion("sistema");
        try {
            $stmt = $conex->prepare("
                SELECT
                    hpa.id_habitacion,
                    c.nombre AS nombre_cliente,
                    c.apellido AS apellido_cliente,
                    DATE_FORMAT(a.fecha_entrada, '%Y-%m-%d') AS fecha_entrada_formato
                FROM
                    alquiler a
                JOIN
                    clientes c ON a.cedula_cliente = c.cedula_cliente
                JOIN
                    habitaciones_por_alquier hpa ON a.id_alquiler = hpa.id_alquiler
                WHERE
                    a.tipo = 'reservacion' AND a.status = 'en espera'
                    AND a.fecha_entrada >= CURDATE()
                ORDER BY a.fecha_entrada ASC
                LIMIT 7;
            ");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $data;
        } catch (PDOException $e) {
            error_log("Error en Dashboard::Reservacionsemanal: " . $e->getMessage());
            return ["error" => "Error al obtener próximas reservaciones: " . $e->getMessage()];
        } finally {
            unset($conex);
        }
    }

    public function Menucomprado()
    {
        $conex = new Conexion("sistema");
        try {
            $stmt = $conex->prepare("
                SELECT
                    m.nombre_menu,
                    SUM(dd.cantidad) AS total_vendido
                FROM detalles_despacho dd
                JOIN despacho d ON dd.id_despacho = d.id_despacho
                JOIN menu m ON dd.id_menu = m.id_menu
                WHERE MONTH(d.fecha_compra) = MONTH(CURDATE()) AND YEAR(d.fecha_compra) = YEAR(CURDATE())
                GROUP BY m.nombre_menu
                ORDER BY total_vendido DESC
                LIMIT 5;
            ");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $data;
        } catch (PDOException $e) {
            error_log("Error en Dashboard::getTopMenuItems: " . $e->getMessage());
            return ["error" => "Error al obtener los ítems del menú más vendidos: " . $e->getMessage()];
        } finally {
            unset($conex);
        }
    }
}