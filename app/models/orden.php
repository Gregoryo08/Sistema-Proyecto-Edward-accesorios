<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class orden extends Conexion
{
    private $id_orden;

    public function __construct()
    {
        parent::__construct();
    }

    public function consultar($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return ["error" => "El ID de la orden no es válido."];
        }

        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare(
                "SELECT o.id_orden,
                        p.cedula_persona AS cedula_cliente,
                        CONCAT(COALESCE(p.nombre, ''), ' ', COALESCE(p.apellido, '')) AS cliente,
                        o.id_marca,
                        o.equipo,
                        o.imei,
                        o.horas_estimadas,
                        o.estado,
                        o.fecha_registro,
                        o.observaciones,
                        o.monto_reparacion,
                        o.fecha_entrega,
                        COALESCE(SUM(r.cantidad * r.precio_unitario), 0) AS total_repuestos
                 FROM ordenes o
                 LEFT JOIN persona p ON p.cedula_persona = o.cedula_persona
                 LEFT JOIN orden_repuestos r ON r.id_orden = o.id_orden
                 WHERE o.id_orden = :id
                 GROUP BY o.id_orden, p.cedula_persona, p.nombre, p.apellido,
                          o.id_marca, o.equipo, o.imei, o.horas_estimadas,
                          o.estado, o.fecha_registro, o.observaciones,
                          o.monto_reparacion, o.fecha_entrega"
            );
            $stmt->execute([":id" => $id]);
            $orden = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$orden) {
                unset($conex);
                return ["error" => "Orden no encontrada."];
            }

            $orden["total_repuestos"] = (float) $orden["total_repuestos"];
            $orden["monto_reparacion"] = (float) $orden["monto_reparacion"];
            $orden["total_orden"] = $orden["total_repuestos"] + $orden["monto_reparacion"];

            $stmtDetalle = $conex->prepare(
                "SELECT nombre, cantidad, precio_unitario AS precio
                 FROM orden_repuestos
                 WHERE id_orden = :id
                 ORDER BY id"
            );
            $stmtDetalle->execute([":id" => $id]);
            $orden["repuestos"] = array_map(function ($fila) {
                $fila["cantidad"] = (int) $fila["cantidad"];
                $fila["precio"] = (float) $fila["precio"];
                return $fila;
            }, $stmtDetalle->fetchAll(PDO::FETCH_ASSOC));

            unset($conex);
            return $orden;
        } catch (PDOException $e) {
            return ["error" => "Error de base de datos: " . $e->getMessage()];
        }
    }

    public function eliminar()
    {
        $id = $this->getId_orden();

        if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "El ID de la orden no es válido.", "input" => "id"];
        }

        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("DELETE FROM ordenes WHERE id_orden = :id");
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() === 0) {
                unset($conex);
                return ["error" => "La orden no existe o ya fue eliminada."];
            }

            unset($conex);
            return true;
        } catch (PDOException $e) {
            return ["error" => "Error de base de datos: " . $e->getMessage()];
        }
    }

    public function getId_orden()
    {
        return $this->id_orden;
    }

    public function setId_orden($id)
    {
        $this->id_orden = $id;
    }
}