<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;
use Exception;

class chequeo extends Conexion
{
    private $id_solicitud;

    public function __construct()
    {
        parent::__construct();
    }

    public function registrarSolicitud($datos)
    {
        if (empty($datos['productos']) || empty($datos['telefono']) || empty($datos['nombre'])) {
            return ["error" => "Datos incompletos para procesar la solicitud."];
        }

        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"] ?? 'CLIENTE_WEB';
            $modulo = "Administrar Chequeo";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $sqlVenta = "INSERT INTO ventas (id_usuario, fecha, total, metodo_pago, direccion, telefono, estado) 
                         VALUES (:usuario, NOW(), :total, :metodo, :direccion, :nombre, 0)";
            
            $stmtVenta = $conex->prepare($sqlVenta);
            $resVenta = $stmtVenta->execute([
                ':usuario'   => $user,
                ':total'     => $datos['total'],
                ':metodo'    => $datos['metodo_pago'],
                ':direccion' => $datos['direccion'],
                ':nombre'    => $datos['nombre'] 
            ]);

            $this->id_solicitud = $conex->lastInsertId();

            foreach ($datos['productos'] as $prod) {
                $id_p = $prod['id'];
                $cantidad = $prod['cant'];
                $precio = $prod['pre'];

                $sqlDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) 
                               VALUES (:id_v, :id_p, :cant, :pre)";
                $stmtDetalle = $conex->prepare($sqlDetalle);
                $stmtDetalle->execute([
                    ':id_v' => $this->id_solicitud,
                    ':id_p' => $id_p,
                    ':cant' => $cantidad,
                    ':pre'  => $precio
                ]);

                $sqlStock = "UPDATE productos SET stock_actual = stock_actual - :cant 
                             WHERE id_producto = :id_p AND stock_actual >= :cant";
                $stmtStock = $conex->prepare($sqlStock);
                $stmtStock->execute([':cant' => $cantidad, ':id_p' => $id_p]);

                if ($stmtStock->rowCount() == 0) {
                    throw new Exception("El producto " . $prod['nom'] . " se agotó.");
                }
            }

            $conex->commit();
            return ["success" => true, "id_solicitud" => $this->id_solicitud];

        } catch (Exception $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function obtenerDetalle($id)
    {
        $conex = new Conexion("sistema");
        $sql = "SELECT d.*, p.nombre_producto 
                FROM detalle_ventas d 
                INNER JOIN productos p ON d.id_producto = p.id_producto 
                WHERE d.id_venta = :id";
        $stmt = $conex->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function gestionarSolicitud($datos)
    {
        try {
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $id = $datos['id'];
            $accion = $datos['tipo'];

            if ($accion === 'aceptar') {
                $sql = "UPDATE ventas SET 
                        estado = 1, 
                        referencia_pago = :ref, 
                        tipo_envio = :env, 
                        notas_envio = :not 
                        WHERE id_venta = :id";
                $stmt = $conex->prepare($sql);
                $stmt->execute([
                    ':id'  => $id,
                    ':ref' => $datos['referencia'] ?? null,
                    ':env' => $datos['envio'] ?? null,
                    ':not' => $datos['notas'] ?? null
                ]);
            } 
            else if ($accion === 'cancelar') {
                $stmtDetalles = $conex->prepare("SELECT id_producto, cantidad FROM detalle_ventas WHERE id_venta = :id");
                $stmtDetalles->execute([':id' => $id]);
                $productos = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

                foreach ($productos as $p) {
                    $stmtRestore = $conex->prepare("UPDATE productos SET stock_actual = stock_actual + :cant WHERE id_producto = :id_p");
                    $stmtRestore->execute([':cant' => $p['cantidad'], ':id_p' => $p['id_producto']]);
                }

                $stmt = $conex->prepare("UPDATE ventas SET estado = 2 WHERE id_venta = :id");
                $stmt->execute([':id' => $id]);
            }

            $conex->commit();
            return ["success" => "Operación realizada con éxito."];

        } catch (Exception $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function listarSolicitudes()
    {
        $conex = new Conexion("sistema");
        $stmt = $conex->query("SELECT * FROM ventas WHERE estado = 0 ORDER BY fecha DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}