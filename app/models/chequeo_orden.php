<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class chequeo_orden extends conexion
{
    private $id_orden;
    private $estado;

    public function __construct()
    {
        parent::__construct();
    }

    public function validarEstado($estado)
    {
        $valor = (string)$estado;
        if ($valor === '0' || $valor === '1') {
            return intval($valor);
        }
        return false;
    }

    public function listarPorEstado($estado)
    {
        $estado = $this->validarEstado($estado);
        if ($estado === false) {
            return [];
        }

        $conex = new conexion();
        $stmt = $conex->prepare("SELECT * FROM ordenes WHERE estado = :estado ORDER BY fecha_registro DESC");
        $stmt->bindParam(":estado", $estado, PDO::PARAM_INT);
        $stmt->execute();

        $ordenes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $fila["repuestos"] = !empty($fila["repuestos"]) ? json_decode($fila["repuestos"], true) : [];
            $ordenes[] = $fila;
        }

        unset($conex);
        return $ordenes;
    }

    public function listarPendientes()
    {
        return $this->listarPorEstado(0);
    }

    public function cambiarEstado()
    {
        $id = $this->getId_orden();
        $estado = $this->validarEstado($this->getEstado());

        if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "El ID de la orden no es válido.", "input" => "id"];
        }

        if ($estado === false) {
            return ["invalido" => "El estado de la orden debe ser 0 (Pendiente) o 1 (Reparación).", "input" => "estado"];
        }

        try {
            $conex = new conexion();
            $user = $_SESSION["username"];
            $modulo = "Administrar Orden";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("UPDATE ordenes SET estado = :estado WHERE id_orden = :id");
            $stmt->bindParam(":estado", $estado, PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                unset($conex);
                return ["error" => "Ha ocurrido un error al actualizar el estado de la orden."];
            }

            unset($conex);
            return true;
        } catch (PDOException $e) {
            return ["error" => "Error de base de datos: " . $e->getMessage()];
        }
    }

    public function procesarPago($referencia, $metodoPago = '')
    {
        $id = $this->getId_orden();
        $estado = $this->validarEstado($this->getEstado());

        if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "El ID de la orden no es válido.", "input" => "id"];
        }

        if ($estado === false) {
            return ["invalido" => "El estado de la orden debe ser 0 (Pendiente) o 1 (Reparación).", "input" => "estado"];
        }

        if (trim($referencia) === '') {
            return ["invalido" => "La referencia de pago es obligatoria.", "input" => "pago_referencia"];
        }

        $observacionesPago = "Pago procesado: {$referencia}";
        if ($metodoPago !== '') {
            $observacionesPago .= " | Método: {$metodoPago}";
        }

        try {
            $conex = new conexion();
            $user = $_SESSION["username"];
            $modulo = "Administrar Orden";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare(
                "UPDATE ordenes SET estado = :estado,
                    observaciones = CASE
                        WHEN observaciones IS NULL OR observaciones = '' THEN :observacionesPago
                        ELSE CONCAT(observaciones, CHAR(10), :observacionesPago)
                    END
                WHERE id_orden = :id"
            );
            $stmt->bindParam(":estado", $estado, PDO::PARAM_INT);
            $stmt->bindParam(":observacionesPago", $observacionesPago);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                unset($conex);
                return ["error" => "Ha ocurrido un error al procesar la orden."];
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

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }
}
