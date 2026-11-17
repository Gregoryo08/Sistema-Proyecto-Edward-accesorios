<?php

namespace App\Sistema\models;

use PDO;

class DashboardModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

public function topClientes()
    {
        $filas = $this->consultar(
            "SELECT CONCAT(per.nombre, ' ', per.apellido) as cliente, SUM(p.total) as total
             FROM pedidos p
             JOIN persona per ON p.cedula_persona = per.cedula_persona
             GROUP BY p.cedula_persona
             ORDER BY total DESC
             LIMIT 5"
        );

        return [
            'categorias' => array_column($filas, 'cliente'),
            'valores' => array_map('floatval', array_column($filas, 'total'))
        ];
    }

    public function topProductos()
    {
        $filas = $this->consultar(
            "SELECT pr.nombre_producto as producto, SUM(dp.cantidad) as total
             FROM detalle_pedido dp
             JOIN productos pr ON dp.id_producto = pr.id_producto
             GROUP BY dp.id_producto
             ORDER BY total DESC
             LIMIT 5"
        );

        return [
            'categorias' => array_column($filas, 'producto'),
            'valores' => array_map('intval', array_column($filas, 'total'))
        ];
    }

    public function ventasDiarias()
    {
        $filas = $this->consultar(
            "SELECT DATE(p.fecha) as dia, SUM(p.total) as total
             FROM pedidos p
             WHERE p.estado = 'pagado'
             AND p.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY DATE(p.fecha)
             ORDER BY dia ASC
             LIMIT 7"
        );

        return [
            'categorias' => array_column($filas, 'dia'),
            'valores' => array_map('floatval', array_column($filas, 'total'))
        ];
    }

    public function ventasMensuales()
    {
        $filas = $this->consultar(
            "SELECT WEEK(p.fecha) - WEEK(DATE_SUB(p.fecha, INTERVAL DAYOFMONTH(p.fecha)-1 DAY)) + 1 as semana, SUM(p.total) as total
             FROM pedidos p
             WHERE p.estado = 'pagado'
             AND MONTH(p.fecha) = MONTH(CURDATE())
             AND YEAR(p.fecha) = YEAR(CURDATE())
             GROUP BY semana
             ORDER BY semana ASC"
        );

        return [
            'categorias' => array_map(function ($f) { return 'Sem ' . $f['semana']; }, $filas),
            'valores' => array_map('floatval', array_column($filas, 'total'))
        ];
    }

    public function ventasNoConcretadas()
    {
        $filas = $this->consultar(
            "SELECT estado_verificacion, COUNT(*) as total
             FROM pago_online
             WHERE estado_verificacion IN ('rechazado', 'pendiente')
             GROUP BY estado_verificacion"
        );

        $datos = [];

        foreach ($filas as $fila) {
            $datos[] = [
                'name' => $fila['estado_verificacion'] === 'rechazado' ? 'Pago rechazado' : 'No verificado',
                'y' => (int) $fila['total']
            ];
        }

        return $datos;
    }

    public function metodosPago()
    {
        $filas = $this->consultar(
            "SELECT metodo_pago, COUNT(*) as total
             FROM pedidos
             WHERE metodo_pago IS NOT NULL
             GROUP BY metodo_pago"
        );

        $datos = [];

        foreach ($filas as $fila) {
            $datos[] = ['name' => ucfirst($fila['metodo_pago']), 'y' => (int) $fila['total']];
        }

        return $datos;
    }

    public function despachosMensuales()
    {
        $filas = $this->consultar(
            "SELECT WEEK(fecha_despacho) - WEEK(DATE_SUB(fecha_despacho, INTERVAL DAYOFMONTH(fecha_despacho)-1 DAY)) + 1 as semana, COUNT(*) as total
             FROM despachos
             WHERE MONTH(fecha_despacho) = MONTH(CURDATE())
             AND YEAR(fecha_despacho) = YEAR(CURDATE())
             GROUP BY semana
             ORDER BY semana ASC"
        );

        return [
            'categorias' => array_map(function ($f) { return 'Sem ' . $f['semana']; }, $filas),
            'valores' => array_map('intval', array_column($filas, 'total'))
        ];
    }

    public function contarPagosPendientes()
    {
        return $this->contar("SELECT COUNT(*) as total FROM pago_online WHERE estado_verificacion = 'pendiente'");
    }

    public function contarPagosAprobados()
    {
        return $this->contar("SELECT COUNT(*) as total FROM pago_online WHERE estado_verificacion = 'aprobado'");
    }

    public function contarDespachosActivos()
    {
        return $this->contar("SELECT COUNT(*) as total FROM despachos WHERE estado_despacho IN ('pendiente', 'asignado', 'en_ruta')");
    }

    public function contarPedidos()
    {
        return $this->contar("SELECT COUNT(*) as total FROM pedidos");
    }

    public function pagosPendientes()
    {
        return $this->consultar(
            "SELECT po.*, p.total, p.fecha, p.direccion_entrega,
                    per.nombre, per.apellido, per.telefono, per.correo
             FROM pago_online po
             JOIN pedidos p ON po.id_pedido = p.id_pedido
             JOIN persona per ON p.cedula_persona = per.cedula_persona
             WHERE po.estado_verificacion = 'pendiente'
             ORDER BY po.fecha_reporte ASC"
        );
    }

    public function pagosAprobados()
    {
        return $this->consultar(
            "SELECT po.*, p.total, p.direccion_entrega,
                    per.nombre, per.apellido, per.telefono, per.correo
             FROM pago_online po
             JOIN pedidos p ON po.id_pedido = p.id_pedido
             JOIN persona per ON p.cedula_persona = per.cedula_persona
             WHERE po.estado_verificacion = 'aprobado'
             AND NOT EXISTS (SELECT 1 FROM despachos d WHERE d.id_pedido = p.id_pedido)
             ORDER BY po.fecha_verificacion ASC"
        );
    }

    public function despachosActivos()
    {
        return $this->consultar(
            "SELECT d.id_despacho, d.id_pedido, d.estado_despacho, d.despachador_nombre,
                    d.despachador_telefono, d.fecha_entrega_estimada,
                    p.total, p.direccion_entrega,
                    per.nombre, per.apellido, per.telefono
             FROM despachos d
             JOIN pedidos p ON d.id_pedido = p.id_pedido
             JOIN persona per ON p.cedula_persona = per.cedula_persona
             WHERE d.estado_despacho IN ('pendiente', 'asignado', 'en_ruta')
             ORDER BY d.fecha_despacho ASC"
        );
    }

    private function consultar($sql)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function contar($sql)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ? (int) $fila['total'] : 0;
    }
}
