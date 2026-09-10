<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use PDOException;

class PedidoModel {
    private $conn;
    private $id_pedido;
    private $cedula_persona;
    private $total;
    private $estado;
    private $metodo_pago;

    public function setIdPedido($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('ID de pedido inválido');
        }
        $this->id_pedido = $valor;
        return $this;
    }

    public function getIdPedido() {
        return $this->id_pedido;
    }

    public function setCedulaPersona($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('Cédula inválida');
        }
        $this->cedula_persona = $valor;
        return $this;
    }

    public function getCedulaPersona() {
        return $this->cedula_persona;
    }

    public function setTotal($valor) {
        if (!is_numeric($valor) || $valor < 0) {
            throw new \InvalidArgumentException('Total inválido');
        }
        $this->total = $valor;
        return $this;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setEstado($valor) {
        $estados_validos = ['pendiente', 'revision', 'aprobado', 'enviado', 'entregado', 'rechazado', 'cancelado'];
        if (!in_array($valor, $estados_validos)) {
            throw new \InvalidArgumentException('Estado inválido: ' . $valor);
        }
        $this->estado = $valor;
        return $this;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setMetodoPago($valor) {
        $valor = trim($valor);
        if (empty($valor)) {
            throw new \InvalidArgumentException('Método de pago inválido');
        }
        $this->metodo_pago = $valor;
        return $this;
    }

    public function getMetodoPago() {
        return $this->metodo_pago;
    }

    public function __construct() {
        $this->conn = Conexion::getShared('sistema_edward')->getConexion();
    }

    public function ejecutar($accion, $datos = []) {
        $accionesValidas = [
            'iniciarTransaccion',
            'confirmarTransaccion',
            'revertirTransaccion',
            'getEstadoData',
            'obtenerPorId',
            'obtenerPorCliente',
            'obtenerDetalle',
            'obtenerPorIdYCliente',
            'crear',
            'crearConArray',
            'agregarDetalle',
            'actualizarEstado',
            'actualizarDireccionEntrega',
            'actualizarDatosPago',
            'setIdPedido',
            'getIdPedido',
            'setCedulaPersona',
            'getCedulaPersona',
            'setTotal',
            'getTotal',
            'setEstado',
            'getEstado',
            'setMetodoPago',
            'getMetodoPago'
        ];
        
        if (!in_array($accion, $accionesValidas)) {
            return ['error' => 'Acción no válida: ' . $accion];
        }
        
        $metodo = '_' . $accion;
        if (method_exists($this, $metodo)) {
            return $this->$metodo($datos);
        }
        
        if (method_exists($this, $accion)) {
            return $this->$accion($datos['valor'] ?? null);
        }
        
        return ['error' => 'Método no implementado: ' . $accion];
    }

    private function _iniciarTransaccion($datos = []) {
        $this->iniciarTransaccion();
        return true;
    }

    private function _confirmarTransaccion($datos = []) {
        $this->confirmarTransaccion();
        return true;
    }

    private function _revertirTransaccion($datos = []) {
        $this->revertirTransaccion();
        return true;
    }

    private function _getEstadoData($datos = []) {
        return $this->getEstadoData($datos['estado'] ?? '', $datos['motivo'] ?? null);
    }

    private function _obtenerPorId($datos = []) {
        return $this->obtenerPorId($datos['id_pedido'] ?? 0);
    }

    private function _obtenerPorCliente($datos = []) {
        return $this->obtenerPorCliente($datos['cedula_persona'] ?? 0);
    }

    private function _obtenerDetalle($datos = []) {
        return $this->obtenerDetalle($datos['id_pedido'] ?? 0);
    }

    private function _obtenerPorIdYCliente($datos = []) {
        return $this->obtenerPorIdYCliente($datos['id_pedido'] ?? 0, $datos['cedula_persona'] ?? 0);
    }

    private function _crear($datos = []) {
        return $this->crear(
            $datos['cedula'] ?? 0,
            $datos['nombre'] ?? '',
            $datos['telefono'] ?? '',
            $datos['email'] ?? '',
            $datos['subtotal'] ?? 0,
            $datos['costo_envio'] ?? 0,
            $datos['total'] ?? 0,
            $datos['metodo_pago'] ?? ''
        );
    }

    private function _crearConArray($datos = []) {
        return $this->crearConArray($datos['datos'] ?? $datos);
    }

    private function _agregarDetalle($datos = []) {
        return $this->agregarDetalle(
            $datos['id_pedido'] ?? 0,
            $datos['id_producto'] ?? 0,
            $datos['cantidad'] ?? 0,
            $datos['precio_unitario'] ?? 0,
            $datos['subtotal'] ?? 0
        );
    }

    private function _actualizarEstado($datos = []) {
        return $this->actualizarEstado(
            $datos['id_pedido'] ?? 0,
            $datos['estado'] ?? '',
            $datos['motivo'] ?? null
        );
    }

    private function _actualizarDireccionEntrega($datos = []) {
        return $this->actualizarDireccionEntrega(
            $datos['id_pedido'] ?? 0,
            $datos['direccion'] ?? ''
        );
    }

    private function _actualizarDatosPago($datos = []) {
        return $this->actualizarDatosPago(
            $datos['id_pedido'] ?? 0,
            $datos['datos_json'] ?? ''
        );
    }

    private function _setIdPedido($valor) {
        return $this->setIdPedido($valor);
    }

    private function _getIdPedido($datos = []) {
        return $this->getIdPedido();
    }

    private function _setCedulaPersona($valor) {
        return $this->setCedulaPersona($valor);
    }

    private function _getCedulaPersona($datos = []) {
        return $this->getCedulaPersona();
    }

    private function _setTotal($valor) {
        return $this->setTotal($valor);
    }

    private function _getTotal($datos = []) {
        return $this->getTotal();
    }

    private function _setEstado($valor) {
        return $this->setEstado($valor);
    }

    private function _getEstado($datos = []) {
        return $this->getEstado();
    }

    private function _setMetodoPago($valor) {
        return $this->setMetodoPago($valor);
    }

    private function _getMetodoPago($datos = []) {
        return $this->getMetodoPago();
    }

    public function iniciarTransaccion() {
        $this->conn->beginTransaction();
    }

    public function confirmarTransaccion() {
        $this->conn->commit();
    }

    public function revertirTransaccion() {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
    }

    public function getEstadoData($estado, $motivo = null) {
        $this->_validarEstado($estado);
        
        return [
            'estado' => $estado,
            'color' => $this->_getColor($estado),
            'icono' => $this->_getIcono($estado),
            'mensaje' => $this->_getMensaje($estado, $motivo),
            'mostrar_boton' => $this->_mostrarBoton($estado),
            'texto_boton' => $this->_getTextoBoton($estado),
            'clase_alerta' => $this->_getClaseAlerta($estado),
            'boton_estilo' => $this->_getBotonEstilo($estado),
            'boton_icono' => $this->_getBotonIcono($estado)
        ];
    }

    private function _validarEstado($estado) {
        $estados_validos = [
            'pendiente', 'revision', 'aprobado',
            'enviado', 'entregado', 'rechazado', 'cancelado'
        ];
        if (!in_array($estado, $estados_validos)) {
            error_log("Estado no válido en PedidoModel: '$estado'");
        }
    }

    private function _getColor($estado) {
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

    private function _getIcono($estado) {
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

    private function _getMensaje($estado, $motivo = null) {
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

    private function _mostrarBoton($estado) {
        return in_array($estado, ['monto_pendiente', 'rechazado']);
    }

    private function _getTextoBoton($estado) {
        $mensaje = $this->_getMensaje($estado);
        return $mensaje['boton'];
    }

    private function _getClaseAlerta($estado) {
        return $this->_getColor($estado);
    }

    private function _getBotonEstilo($estado) {
        if ($estado === 'pendiente') {
            return 'warning';
        } elseif ($estado === 'rechazado') {
            return 'danger';
        }
        return 'secondary';
    }

    private function _getBotonIcono($estado) {
        if ($estado === 'pendiente') {
            return 'fa-credit-card';
        } elseif ($estado === 'rechazado') {
            return 'fa-redo';
        }
        return 'fa-info-circle';
    }

    public function obtenerPorId($id_pedido) {
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
            return null;
        }
    }

    public function obtenerPorCliente($cedula_persona) {
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
            return [];
        }
    }

    public function obtenerDetalle($id_pedido) {
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
            return [];
        }
    }

    public function obtenerPorIdYCliente($id_pedido, $cedula_persona) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM pedidos 
                    WHERE id_pedido = ? AND cedula_persona = ?
            ");
            $stmt->execute([$id_pedido, $cedula_persona]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function crear($cedula, $nombre, $telefono, $email, $subtotal, $costo_envio, $total, $metodo_pago) {
        try {
            $sql = "INSERT INTO pedidos 
                    (cedula_persona, nombre_cliente, telefono_cliente, email_invitado, 
                     subtotal, costo_envio, monto_total, metodo_pago, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute([$cedula, $nombre, $telefono, $email, $subtotal, $costo_envio, $total, $metodo_pago]);
            if ($resultado) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function crearConArray($datos) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO pedidos 
                (cedula_persona, subtotal, costo_envio, monto_total, estado, metodo_pago, direccion_entrega)
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
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function agregarDetalle($id_pedido, $id_producto, $cantidad, $precio_unitario, $subtotal) {
        try {
            $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id_pedido, $id_producto, $cantidad, $precio_unitario, $subtotal]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizarEstado($id_pedido, $estado, $motivo = null) {
        try {
            $this->_validarEstado($estado);
            
            $sql = "UPDATE pedidos SET estado = ?";
            $params = [$estado];
            
            if ($motivo !== null && ($estado === 'rechazado' || $estado === 'pendiente')) {
                $sql .= ", motivo_rechazo = ?";
                $params[] = $motivo;
            }
            
            $sql .= " WHERE id_pedido = ?";
            $params[] = $id_pedido;
            
            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute($params);
            return $resultado;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizarDireccionEntrega($id_pedido, $direccion) {
        try {
            $sql = "UPDATE pedidos SET direccion_entrega = ? WHERE id_pedido = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$direccion, $id_pedido]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizarDatosPago($id_pedido, $datos_json) {
        try {
            $sql = "UPDATE pedidos SET datos_pago = ? WHERE id_pedido = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$datos_json, $id_pedido]);
        } catch (PDOException $e) {
            return false;
        }
    }
}