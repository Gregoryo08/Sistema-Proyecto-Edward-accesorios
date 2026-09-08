<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class servicio_venta extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    public function registrar($direccion, $metodo_pago, $referencia_pago, $total)
    {
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare(
                "INSERT INTO servicio_venta (fecha, direccion, metodo_pago, referencia_pago, total)
                 VALUES (NOW(), :direccion, :metodo_pago, :referencia_pago, :total)"
            );
            $stmt->execute([
                ':direccion' => $direccion,
                ':metodo_pago' => $metodo_pago,
                ':referencia_pago' => $referencia_pago,
                ':total' => $total
            ]);

            return [
                'success' => true,
                'id_servicio_venta' => $conex->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['error' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function listar()
    {
        $conex = new Conexion("sistema");
        $stmt = $conex->query(
            "SELECT fecha, direccion, metodo_pago, referencia_pago, total
             FROM servicio_venta
             ORDER BY fecha DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
