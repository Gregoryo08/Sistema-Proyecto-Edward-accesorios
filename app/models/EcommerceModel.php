<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use PDOException;

class EcommerceModel extends Conexion
{
    private $conn;
    private $producto_model;
    private $envio_model;
    private $checkout_model;
    private $dashboard_model;

    public function __construct()
    {
        parent::__construct('sistema');
        $this->conn = $this;
    }

    public function ejecutar($accion, $datos = [])
    {
        $partes = explode('.', $accion, 2);
        if (count($partes) !== 2) {
            return ['error' => 'Accion sin dominio: ' . $accion];
        }

        switch ($partes[0]) {
            case 'pago':
                return $this->ejecutarPago($partes[1], $datos);
            case 'despacho':
                return $this->envioModel()->ejecutar($partes[1], $datos);
            case 'producto':
                return $this->productoModel()->ejecutar($partes[1], $datos);
            case 'checkout':
                return $this->checkoutModel()->ejecutar($partes[1], $datos);
        }

        return ['error' => 'Dominio no valido: ' . $partes[0]];
    }

    private function productoModel()
    {
        if (!$this->producto_model) {
            $this->producto_model = new ProductoModel($this->conn);
        }
        return $this->producto_model;
    }

    private function envioModel()
    {
        if (!$this->envio_model) {
            $this->envio_model = new EnvioModel($this->conn);
        }
        return $this->envio_model;
    }

    private function checkoutModel()
    {
        if (!$this->checkout_model) {
            $this->checkout_model = new CheckoutModel($this->conn);
        }
        return $this->checkout_model;
    }

    public function PedidoiniciarTransaccion() {
        $this->conn->beginTransaction();
    }

    public function PedidoconfirmarTransaccion() {
        $this->conn->commit();
    }

    public function PedidorevertirTransaccion() {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
    }

    public function PedidogetEstadoData($estado, $motivo = null) {
        $this->Pedido_validarEstado($estado);
        
        return [
            'estado' => $estado,
            'color' => $this->Pedido_getColor($estado),
            'icono' => $this->Pedido_getIcono($estado),
            'mensaje' => $this->Pedido_getMensaje($estado, $motivo),
            'mostrar_boton' => $this->Pedido_mostrarBoton($estado),
            'texto_boton' => $this->Pedido_getTextoBoton($estado),
            'clase_alerta' => $this->Pedido_getClaseAlerta($estado),
            'boton_estilo' => $this->Pedido_getBotonEstilo($estado),
            'boton_icono' => $this->Pedido_getBotonIcono($estado)
        ];
    }

    private function Pedido_validarEstado($estado) {
        $estados_validos = [
            'pendiente', 'revision', 'aprobado',
            'enviado', 'entregado', 'rechazado', 'cancelado'
        ];
        if (!in_array($estado, $estados_validos)) {
            error_log("Estado no válido en PedidoModel: '$estado'");
        }
    }

    private function Pedido_getColor($estado) {
        $colores = [
            'pendiente' => 'secondary',
            'revision' => 'warning',
            'aprobado' => 'success',
            'enviado' => 'primary',
            'entregado' => 'success',
            'rechazado' => 'danger',
            'cancelado' => 'secondary'
        ];
        return $colores[$estado] ?? 'secondary';
    }

    private function Pedido_getIcono($estado) {
        $iconos = [
            'pendiente' => 'fa-clock',
            'revision' => 'fa-spinner',
            'aprobado' => 'fa-check-circle',
            'enviado' => 'fa-truck-fast',
            'entregado' => 'fa-check-double',
            'rechazado' => 'fa-times-circle',
            'cancelado' => 'fa-ban'
        ];
        return $iconos[$estado] ?? 'fa-info-circle';
    }

    private function Pedido_getMensaje($estado, $motivo = null) {
        $mensajes = [
            'pendiente' => [
                'titulo' => 'Pendiente de pago',
                'cuerpo' => 'Realiza el pago para continuar con tu pedido.',
                'boton' => 'Reportar Pago'
            ],
            'revision' => [
                'titulo' => 'Pago en revisión',
                'cuerpo' => 'Tu pago está siendo verificado por la cajera.',
                'boton' => null
            ],
            'aprobado' => [
                'titulo' => '¡Pago aprobado!',
                'cuerpo' => 'Tu pedido está siendo preparado para despacho.',
                'boton' => null
            ],
            'enviado' => [
                'titulo' => '¡Pedido en camino!',
                'cuerpo' => 'Tu pedido fue despachado y está en ruta hacia tu dirección.',
                'boton' => null
            ],
            'entregado' => [
                'titulo' => '¡Pedido entregado!',
                'cuerpo' => 'Gracias por comprar en Edward Accesorios. ¡Vuelve pronto!',
                'boton' => null
            ],
            'rechazado' => [
                'titulo' => 'Pago Rechazado',
                'cuerpo' => $motivo ?? 'Tu pago no cumple con los requisitos de compra.',
                'boton' => 'Reintentar Pago'
            ],
            'cancelado' => [
                'titulo' => 'Pedido Cancelado',
                'cuerpo' => 'Este pedido fue cancelado. Contacta con soporte.',
                'boton' => null
            ]
        ];
        return $mensajes[$estado] ?? [
            'titulo' => 'Estado desconocido',
            'cuerpo' => 'Contacta con soporte.',
            'boton' => null
        ];
    }

    private function Pedido_mostrarBoton($estado) {
        return in_array($estado, ['monto_pendiente', 'rechazado']);
    }

    private function Pedido_getTextoBoton($estado) {
        $mensaje = $this->Pedido_getMensaje($estado);
        return $mensaje['boton'];
    }

    private function Pedido_getClaseAlerta($estado) {
        return $this->Pedido_getColor($estado);
    }

    private function Pedido_getBotonEstilo($estado) {
        if ($estado === 'pendiente') {
            return 'warning';
        } elseif ($estado === 'rechazado') {
            return 'danger';
        }
        return 'secondary';
    }

    private function Pedido_getBotonIcono($estado) {
        if ($estado === 'pendiente') {
            return 'fa-credit-card';
        } elseif ($estado === 'rechazado') {
            return 'fa-redo';
        }
        return 'fa-info-circle';
    }

    public function PedidoobtenerPorId($id_pedido) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.*,
                    per.nombre,
                    per.apellido,
                    per.telefono,
                    per.correo,
                    per.direccion as direccion
                FROM pedidos p
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                LEFT JOIN clientes cl ON p.cedula_persona = cl.cedula_persona
                WHERE p.id_pedido = ?
            ");
            $stmt->execute([$id_pedido]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en obtenerPorId: " . $e->getMessage());
        }
    }

    

    public function PedidoobtenerPorCliente($cedula_persona) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    p.*,
                    per.nombre,
                    per.apellido,
                    per.telefono,
                    per.correo
                FROM pedidos p
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                WHERE p.cedula_persona = ?
                ORDER BY p.fecha DESC
            ");
            $stmt->execute([$cedula_persona]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en obtenerPorCliente: " . $e->getMessage());
        }
    }

    public function PedidoobtenerDetalle($id_pedido) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    dp.*,
                    pr.nombre_producto,
                    pr.imagen_principal
                FROM detalle_pedido dp
                JOIN productos pr ON dp.id_producto = pr.id_producto
                WHERE dp.id_pedido = ?
            ");
            $stmt->execute([$id_pedido]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en obtenerDetalle: " . $e->getMessage());
        }
    }

    public function PedidoobtenerPorIdYCliente($id_pedido, $cedula_persona) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM pedidos 
                    WHERE id_pedido = ? AND cedula_persona = ?
            ");
            $stmt->execute([$id_pedido, $cedula_persona]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en obtenerPorIdYCliente: " . $e->getMessage());
        }
    }

    public function Pedidocrear($cedula, $nombre, $telefono, $email, $subtotal, $costo_envio, $total, $metodo_pago) {
        try {
            $sql = "INSERT INTO pedidos 
                    (cedula_persona, nombre_cliente, telefono_cliente, email_invitado, 
                     subtotal, costo_envio, total, metodo_pago, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute([$cedula, $nombre, $telefono, $email, $subtotal, $costo_envio, $total, $metodo_pago]);
            if ($resultado) {
                return $this->conn->lastInsertId();
            }
            throw new \Exception("No se pudo ejecutar la inserción del pedido.");
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en crear pedido: " . $e->getMessage());
        }
    }

    public function PedidocrearConArray($datos) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO pedidos 
                (cedula_persona, subtotal, costo_envio, total, estado, metodo_pago, direccion_entrega)
                VALUES (?, ?, ?, ?, 'pendiente', ?, ?)
            ");
            $resultado = $stmt->execute([
                $datos['cedula_persona'],
                $datos['subtotal'],
                $datos['costo_envio'],
                $datos['total'],
                $datos['metodo_pago'],
                $datos['direccion_entrega'] ?? null
            ]);
            if ($resultado) {
                return $this->conn->lastInsertId();
            }
            throw new \Exception("No se pudo ejecutar la inserción del pedido con array.");
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en crearConArray: " . $e->getMessage());
        }
    }

    public function PedidoactualizarTotalBs($id_pedido, $total_bs, $tasa_bs) {
        try {
            $stmt = $this->conn->prepare("UPDATE pedidos SET total_bs = ?, tasa_bs = ? WHERE id_pedido = ?");
            $stmt->execute([$total_bs, $tasa_bs, $id_pedido]);
            return true;
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en actualizarTotalBs: " . $e->getMessage());
        }
    }

    public function PedidoagregarDetalle($id_pedido, $id_producto, $cantidad, $precio_unitario, $subtotal) {
        try {
            $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute([$id_pedido, $id_producto, $cantidad, $precio_unitario, $subtotal]);
            if ($resultado) {
                return true;
            }
            throw new \Exception("No se pudo insertar el detalle del producto ID: " . $id_producto);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en agregarDetalle: " . $e->getMessage());
        }
    }

    public function PedidoactualizarEstado($id_pedido, $estado, $motivo = null) {
        try {
            $this->Pedido_validarEstado($estado);
            
            $sql = "UPDATE pedidos SET estado = ?";
            $params = [$estado];
            
            if ($motivo !== null && ($estado === 'rechazado' || $estado === 'pendiente')) {
                $sql .= ", motivo_rechazo = ?";
                $params[] = $motivo;
            }
            
            $sql .= " WHERE id_pedido = ?";
            $params[] = $id_pedido;
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en actualizarEstado: " . $e->getMessage());
        }
    }

    public function PedidoactualizarDireccionEntrega($id_pedido, $direccion) {
        try {
            $sql = "UPDATE pedidos SET direccion_entrega = ? WHERE id_pedido = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$direccion, $id_pedido]);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en actualizarDireccionEntrega: " . $e->getMessage());
        }
    }

    public function PedidoactualizarDatosPago($id_pedido, $datos_json) {
        try {
            $sql = "UPDATE pedidos SET datos_pago = ? WHERE id_pedido = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$datos_json, $id_pedido]);
        } catch (PDOException $e) {
            throw new \Exception("Error SQL en actualizarDatosPago: " . $e->getMessage());
        }
    }

    private function ejecutarPago($accion, $datos = [])
    {
        $accionesValidas = [
            'obtener_pendientes',
            'obtener_aprobados_sin_despacho',
            'obtener_por_id',
            'crear',
            'aprobar',
            'rechazar',
            'existe_por_pedido',
            'procesar_verificacion'
        ];

        if (!in_array($accion, $accionesValidas)) {
            return ['error' => 'Acción no válida: ' . $accion];
        }

        $metodo = 'Pago_' . $accion;
        if (method_exists($this, $metodo)) {
            return $this->$metodo($datos);
        }

        return ['error' => 'Método no implementado: ' . $accion];
        }

    

    public function PagogetConexion() {
        return $this->conn;
    }

    private function Pago_obtener_pendientes($datos = []) {
    try {
        $stmt = $this->conn->prepare("
            SELECT 
                po.*,
                p.total,
                p.fecha,
                per.nombre,
                per.apellido,
                per.telefono,
                per.correo
            FROM pago_online po
            JOIN pedidos p ON po.id_pedido = p.id_pedido
            JOIN persona per ON p.cedula_persona = per.cedula_persona
            WHERE po.estado_verificacion = 'pendiente'
            GROUP BY po.id_reporte
            ORDER BY po.fecha_reporte ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("PagoOnlineModel::_obtener_pendientes: " . $e->getMessage());
        return [];
    }
}

    private function Pago_obtener_aprobados_sin_despacho($datos = []) {
    try {
        $sinDespacho = "";
        $check = $this->conn->query("SHOW TABLES LIKE 'despachos'");
        if ($check && $check->rowCount() > 0) {
            $sinDespacho = "AND p.id_pedido NOT IN (SELECT COALESCE(id_pedido, 0) FROM despachos)";
        }
        
        $stmt = $this->conn->prepare("
            SELECT 
                po.*,
                p.total,
                p.direccion_entrega,
                per.nombre,
                per.apellido,
                per.telefono,
                per.correo
            FROM pago_online po
            JOIN pedidos p ON po.id_pedido = p.id_pedido
            JOIN persona per ON p.cedula_persona = per.cedula_persona
            WHERE po.estado_verificacion = 'aprobado'
            $sinDespacho
            GROUP BY po.id_reporte
            ORDER BY po.fecha_verificacion ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        error_log("PagoOnlineModel::_obtener_aprobados_sin_despacho: " . $e->getMessage());
        return [];
    }
}

    private function Pago_obtener_por_id($datos = []) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    po.*,
                    per.nombre,
                    per.apellido,
                    per.telefono,
                    per.correo
                FROM pago_online po
                JOIN pedidos p ON po.id_pedido = p.id_pedido
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                WHERE po.id_reporte = ?
            ");
            $stmt->execute([$datos['id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_obtener_por_id: " . $e->getMessage());
            return null;
        }
    }

    private function Pago_crear($datos = []) {
        try {
            $sql = "INSERT INTO pago_online (
                        id_pedido, 
                        cedula_persona, 
                        referencia, 
                        banco_emisor, 
                        banco_receptor,
                        telefono_transferencia, 
                        monto_reportado, 
                        fecha_transferencia, 
                        nombre_pagador, 
                        cedula_pagador,
                        comprobante_adjunto,
                        estado_verificacion
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
            
            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute([
                $datos['id_pedido'],
                $datos['cedula_persona'] ?? null,
                $datos['referencia'],
                $datos['banco_emisor'],
                $datos['banco_receptor'] ?? null,
                $datos['telefono'],
                $datos['monto'],
                $datos['fecha_transferencia'] ?? date('Y-m-d'),
                $datos['nombre_pagador'] ?? null,
                $datos['cedula_pagador'] ?? null,
                $datos['comprobante'] ?? null
            ]);
            
            if ($resultado) {
                $id_reporte = $this->conn->lastInsertId();

                // El pago quedo REPORTADO y a la espera de la cajera: el pedido pasa a
                // 'revision'. Antes se quedaba en 'pendiente' y el detalle del pedido
                // mostraba "Pendiente de pago / Realiza el pago para continuar con tu
                // pedido", aunque el cliente ya hubiera pagado (pedia pagar otra vez).
                $this->conn->prepare("UPDATE pedidos SET estado = 'revision' WHERE id_pedido = ?")
                          ->execute([$datos['id_pedido']]);

                return [
                    'success' => true,
                    'mensaje' => 'Pago reportado correctamente',
                    'id_reporte' => $id_reporte
                ];
            }
            return ['error' => 'Error al guardar el pago'];
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_crear: " . $e->getMessage());
            return ['error' => 'Error al guardar: ' . $e->getMessage()];
        }
    }

    private function Pago_aprobar($datos = []) {
    try {
        $this->conn->beginTransaction();

        $user = $_SESSION["username"] ?? 'Sistema';
        $modulo = 'Administrar E-Commerce';

        $stmtVars = $this->conn->prepare("SET @usuario_actual = ?, @modulo = ?");
        $stmtVars->execute([$user, $modulo]);

        $stmtRepo = $this->conn->prepare("SELECT id_pedido FROM pago_online WHERE id_reporte = ?");
        $stmtRepo->execute([$datos['id_reporte']]);
        $pago = $stmtRepo->fetch(PDO::FETCH_ASSOC);

        if (!$pago) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => 'El reporte de pago no existe'];
        }

        $id_pedido = $pago['id_pedido'];

        $stmt = $this->conn->prepare("
            UPDATE pago_online 
            SET estado_verificacion = 'aprobado', 
                fecha_verificacion = NOW(), 
                verificado_por = ? 
            WHERE id_reporte = ?
        ");
        $resultadoPago = $stmt->execute([$datos['verificado_por'], $datos['id_reporte']]);

        if (!$resultadoPago) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => 'Error al aprobar el pago'];
        }

        $stmtPedido = $this->conn->prepare("
            UPDATE pedidos 
            SET estado = 'aprobado' 
            WHERE id_pedido = ?
        ");
        $resultadoPedido = $stmtPedido->execute([$id_pedido]);

        if (!$resultadoPedido) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => 'Error al actualizar el estado del pedido'];
        }

        $this->conn->commit();
        return ['success' => true, 'mensaje' => 'Pago aprobado y pedido actualizado correctamente'];
    } catch (\PDOException $e) {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
        error_log("PagoOnlineModel::_aprobar: " . $e->getMessage());
        return ['success' => false, 'error' => 'Error al aprobar: ' . $e->getMessage()];
    }
}

    private function Pago_rechazar($datos = []) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE pago_online 
                SET estado_verificacion = 'rechazado', 
                    fecha_verificacion = NOW(), 
                    verificado_por = ?,
                    motivo_rechazo = ? 
                WHERE id_reporte = ?
            ");
            $resultado = $stmt->execute([
                $datos['verificado_por'],
                $datos['motivo_rechazo'] ?? 'No especificado',
                $datos['id_reporte']
            ]);
            
            return $resultado 
                ? ['success' => true, 'mensaje' => 'Pago rechazado correctamente']
                : ['error' => 'Error al rechazar el pago'];
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_rechazar: " . $e->getMessage());
            return ['error' => 'Error al rechazar: ' . $e->getMessage()];
        }
    }

public function obtenerDatosPersona($cedula) {
    try {
        $stmt = $this->conn->prepare("SELECT nombre, apellido, correo, telefono FROM persona WHERE cedula_persona = ?");
        $stmt->execute([$cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new \Exception("Error SQL en obtenerDatosPersona: " . $e->getMessage());
    }
}


    public function PagobloquearPedido($id_pedido) {
        $stmt = $this->conn->prepare("SELECT id_pedido FROM pedidos WHERE id_pedido = ? FOR UPDATE");
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function Pago_existe_por_pedido($datos = []) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM pago_online WHERE id_pedido = ? AND estado_verificacion != 'rechazado'");
            $stmt->execute([$datos['id_pedido']]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado && $resultado['total'] > 0;
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_existe_por_pedido: " . $e->getMessage());
            return false;
        }
    }

    private function Pago_procesar_verificacion($datos = []) {
        try {
            $accion = $datos['accion'] ?? '';
            $id_reporte = $datos['id_reporte'] ?? 0;
            $verificado_por = $datos['verificado_por'] ?? 'cajera';
            
            if ($accion === 'aprobar') {
                return $this->Pago_aprobar([
                    'id_reporte' => $id_reporte,
                    'verificado_por' => $verificado_por
                ]);
            } elseif ($accion === 'rechazar') {
                return $this->Pago_rechazar([
                    'id_reporte' => $id_reporte,
                    'verificado_por' => $verificado_por,
                    'motivo_rechazo' => $datos['motivo_rechazo'] ?? null
                ]);
            }
            
            return ['error' => 'Acción no válida para verificación'];
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_procesar_verificacion: " . $e->getMessage());
            return ['error' => 'Error en verificación: ' . $e->getMessage()];
        }
    }

    private function dashboardModel()
    {
        if (!$this->dashboard_model) {
            $this->dashboard_model = new DashboardModel($this->conn);
        }
        return $this->dashboard_model;
    }

    public function DashboardtopClientes() { return $this->dashboardModel()->topClientes(); }
    public function DashboardtopProductos() { return $this->dashboardModel()->topProductos(); }
    public function DashboardventasDiarias() { return $this->dashboardModel()->ventasDiarias(); }
    public function DashboardventasMensuales() { return $this->dashboardModel()->ventasMensuales(); }
    public function DashboardventasNoConcretadas() { return $this->dashboardModel()->ventasNoConcretadas(); }
    public function DashboardmetodosPago() { return $this->dashboardModel()->metodosPago(); }
    public function DashboarddespachosMensuales() { return $this->dashboardModel()->despachosMensuales(); }
    public function DashboardcontarPagosPendientes() { return $this->dashboardModel()->contarPagosPendientes(); }
    public function DashboardcontarPagosAprobados() { return $this->dashboardModel()->contarPagosAprobados(); }
    public function DashboardcontarDespachosActivos() { return $this->dashboardModel()->contarDespachosActivos(); }
    public function DashboardcontarPedidos() { return $this->dashboardModel()->contarPedidos(); }
    public function DashboardpagosPendientes() { return $this->dashboardModel()->pagosPendientes(); }
    public function DashboardpagosAprobados() { return $this->dashboardModel()->pagosAprobados(); }
    public function DashboarddespachosActivos() { return $this->dashboardModel()->despachosActivos(); }



    private $banco_model;

    private function bancoModel()
    {
        if (!$this->banco_model) {
            $this->banco_model = new BancoModel();
        }
        return $this->banco_model;
    }

    public function BancoobtenerActivos($forzarSincronizacion = false) {
        return $this->bancoModel()->obtenerActivos($forzarSincronizacion);
    }

    public function BancoobtenerActivosSoloBd() {
        return $this->bancoModel()->obtenerActivosSoloBd();
    }


    public function Receptorobtener($metodo) { return $this->bancoModel()->obtenerReceptor($metodo); }
    public function ReceptorobtenerNombre($metodo) { return $this->bancoModel()->obtenerNombreReceptor($metodo); }
    public function ReceptorobtenerDatosPago($metodo) { return $this->bancoModel()->obtenerDatosPagoReceptor($metodo); }
}
