<?php

namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;
use \Throwable;

class principal extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Cargaringreso()
    {
        $conex = new Conexion("sistema");
        $data = [];
        try {
            $stmt = $conex->prepare("SELECT IFNULL(SUM(total_venta), 0) FROM ventas WHERE DATE(fecha_venta) = CURDATE() AND estado != 'Anulada'");
            $stmt->execute();
            $data['ingresosHoy'] = $stmt->fetchColumn();

            $stmt = $conex->prepare("SELECT IFNULL(SUM(total_venta), 0) FROM ventas WHERE YEARWEEK(fecha_venta, 1) = YEARWEEK(CURDATE(), 1) AND estado != 'Anulada'");
            $stmt->execute();
            $data['ingresosSemana'] = $stmt->fetchColumn();

            $stmt = $conex->prepare("SELECT IFNULL(SUM(total_venta), 0) FROM ventas WHERE MONTH(fecha_venta) = MONTH(CURDATE()) AND YEAR(fecha_venta) = YEAR(CURDATE()) AND estado != 'Anulada'");
            $stmt->execute();
            $data['ingresosMes'] = $stmt->fetchColumn();

            $stmt = $conex->prepare("SELECT COUNT(*) FROM productos WHERE stock_actual <= stock_minimo");
            $stmt->execute();
            $data['productosBajosStock'] = $stmt->fetchColumn();

            $stmt = $conex->prepare("SELECT COUNT(*) FROM servicio_tecnico WHERE estado = 'Pendiente'");
            $stmt->execute();
            $data['serviciosPendientes'] = $stmt->fetchColumn();

            return $data;
        } catch (PDOException $e) {
            error_log("Error en principal::Cargaringreso: " . $e->getMessage());
            return ["error" => $e->getMessage()];
        } finally {
            unset($conex);
        }
    }

    public function obtenerDatosPersonales($cedula)
    {
        $cedula = trim($cedula);
        $conex = null;
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("SELECT * FROM persona WHERE cedula_persona = :c");
            $stmt->bindParam(":c", $cedula);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            return false;
        } finally {
            unset($conex);
        }
    }
     
    public function ObtenerTotalesHoy() 
    {
        $conex = null;
        try {
            $conex = new Conexion("sistema");
            $sql = "SELECT 
                (SELECT SUM(total_venta) FROM ventas WHERE DATE(fecha_venta) = CURDATE() AND estado != 'Anulada') AS ingresosHoy,
                (SELECT COUNT(*) FROM ventas WHERE DATE(fecha_venta) = CURDATE() AND estado = 'completada') AS ventasCompletadas";
            $stmt = $conex->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        } finally {
            unset($conex);
        }
    }

    public function VentasRecientes()
    {
        $conex = null;
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("SELECT v.id_venta, p.nombre AS nombre, v.total_venta, v.fecha_venta FROM ventas v LEFT JOIN persona p ON v.cedula_persona = p.cedula_persona ORDER BY v.fecha_venta DESC LIMIT 5");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        } finally {
            unset($conex);
        }
    }

public function obtenerDatosDashboardCliente($cedula, $mesFiltro = null, $anioFiltro = null)
{
    $cedula = trim($cedula);
    $conex = null;
    $data = [];

    if ($mesFiltro === null || $anioFiltro === null) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input['mes']) && !empty($input['anio'])) {
            $mesFiltro = $input['mes'];
            $anioFiltro = $input['anio'];
        } elseif (!empty($_POST['mes']) && !empty($_POST['anio'])) {
            $mesFiltro = $_POST['mes'];
            $anioFiltro = $_POST['anio'];
        }
    }

    try {
        $conex = new Conexion("sistema");
        
        $stmtMeses = $conex->prepare("SELECT DISTINCT MONTH(c.fecha_vencimiento) as mes, YEAR(c.fecha_vencimiento) as anio, COUNT(*) as total_cuotas, SUM(f.monto_cuota) as monto_total FROM cuotas c JOIN financiamientos f ON c.id_financiamiento = f.id_financiamiento WHERE f.cedula_persona = :cedula GROUP BY YEAR(c.fecha_vencimiento), MONTH(c.fecha_vencimiento) ORDER BY c.fecha_vencimiento ASC");
        $stmtMeses->bindParam(":cedula", $cedula);
        $stmtMeses->execute();
        $data['mesesDisponibles'] = $stmtMeses->fetchAll(PDO::FETCH_ASSOC);

        if (empty($mesFiltro) || empty($anioFiltro)) {
            if (!empty($data['mesesDisponibles'])) {
                $mesFiltro = $data['mesesDisponibles'][0]['mes'];
                $anioFiltro = $data['mesesDisponibles'][0]['anio'];
            } else {
                $mesFiltro = date('m');
                $anioFiltro = date('Y');
            }
        }

        $condicionFecha = " AND MONTH(c.fecha_vencimiento) = :mes AND YEAR(c.fecha_vencimiento) = :anio";
        $params = [":cedula" => $cedula, ":mes" => $mesFiltro, ":anio" => $anioFiltro];

        $sqlMes = "SELECT IFNULL(SUM(f.monto_cuota), 0) FROM cuotas c JOIN financiamientos f ON c.id_financiamiento = f.id_financiamiento WHERE f.cedula_persona = :cedula" . $condicionFecha;
        $stmtMes = $conex->prepare($sqlMes);
        foreach ($params as $key => $val) {
            $stmtMes->bindValue($key, $val);
        }
        $stmtMes->execute();
        $data['compromisoMes'] = $stmtMes->fetchColumn();

        $sqlMesActual = "SELECT IFNULL(SUM(f.monto_cuota), 0) FROM cuotas c JOIN financiamientos f ON c.id_financiamiento = f.id_financiamiento WHERE f.cedula_persona = :cedula AND MONTH(c.fecha_vencimiento) = MONTH(CURDATE()) AND YEAR(c.fecha_vencimiento) = YEAR(CURDATE())";
        $stmtMesActual = $conex->prepare($sqlMesActual);
        $stmtMesActual->bindParam(":cedula", $cedula);
        $stmtMesActual->execute();
        $data['compromisoMesActual'] = $stmtMesActual->fetchColumn();

        $data['mesActualInfo'] = [
            'mes' => date('n'),
            'anio' => date('Y')
        ];

        $stmtProximo = $conex->prepare("SELECT f.id_financiamiento as id, f.monto_total, c.id_cuota, c.numero_cuota, f.monto_cuota as monto, c.fecha_vencimiento, c.estado_cuota, p.nombre_producto as equipo, (SELECT COUNT(*) FROM cuotas WHERE id_financiamiento = f.id_financiamiento) as total_cuotas, (SELECT COUNT(*) FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND LOWER(estado_cuota) IN ('pagada', 'pagado')) as cuotas_pagadas FROM financiamientos f JOIN cuotas c ON f.id_financiamiento = c.id_financiamiento LEFT JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento LEFT JOIN productos p ON df.id_productos = p.id_producto WHERE f.cedula_persona = :cedula AND c.estado_cuota = 'pendiente' ORDER BY c.fecha_vencimiento ASC LIMIT 1");
        $stmtProximo->bindParam(":cedula", $cedula);
        $stmtProximo->execute();
        $data['proximoEquipo'] = $stmtProximo->fetch(PDO::FETCH_ASSOC) ?: null;

        $sqlLista = "SELECT f.id_financiamiento as id, c.numero_cuota, f.monto_cuota as monto, c.fecha_vencimiento, c.estado_cuota as estado, p.nombre_producto as equipo FROM financiamientos f JOIN cuotas c ON f.id_financiamiento = c.id_financiamiento LEFT JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento LEFT JOIN productos p ON df.id_productos = p.id_producto WHERE f.cedula_persona = :cedula" . $condicionFecha . " ORDER BY c.fecha_vencimiento ASC";
        $stmtLista = $conex->prepare($sqlLista);
        foreach ($params as $key => $val) {
            $stmtLista->bindValue($key, $val);
        }
        $stmtLista->execute();
        $data['financiamientosActivos'] = $stmtLista->fetchAll(PDO::FETCH_ASSOC);

        $data['mesSeleccionado'] = ['mes' => $mesFiltro, 'anio' => $anioFiltro];

        return $data;
    } catch (PDOException $e) {
        error_log("Error en principal::obtenerDatosDashboardCliente: " . $e->getMessage());
        return ["error" => $e->getMessage()];
    } finally {
        unset($conex);
    }
}
}