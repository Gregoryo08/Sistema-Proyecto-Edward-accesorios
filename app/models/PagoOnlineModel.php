<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;

class PagoOnlineModel {
    private $conn;
    private $id_reporte;
    private $id_pedido;
    private $cedula_persona;
    private $referencia;
    private $monto;

    public function setIdReporte($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('ID de reporte inválido');
        }
        $this->id_reporte = $valor;
        return $this;
    }

    public function getIdReporte() {
        return $this->id_reporte;
    }

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

    public function setReferencia($valor) {
        $valor = trim($valor);
        if (empty($valor)) {
            throw new \InvalidArgumentException('Referencia inválida');
        }
        $this->referencia = $valor;
        return $this;
    }

    public function getReferencia() {
        return $this->referencia;
    }

    public function setMonto($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('Monto inválido');
        }
        $this->monto = $valor;
        return $this;
    }

    public function getMonto() {
        return $this->monto;
    }

    public function __construct() {
        $this->conn = Conexion::getShared('sistema_edward')->getConexion();
    }

    public function getConexion() {
        return $this->conn;
    }

    public function ejecutar($accion, $datos = []) {
        $accionesValidas = [
            'obtener_pendientes',
            'obtener_aprobados_sin_despacho',
            'obtener_todos',
            'obtener_por_id',
            'crear',
            'aprobar',
            'rechazar',
            'existe_por_pedido',
            'procesar_verificacion',
            'setIdReporte',
            'getIdReporte',
            'setIdPedido',
            'getIdPedido',
            'setCedulaPersona',
            'getCedulaPersona',
            'setReferencia',
            'getReferencia',
            'setMonto',
            'getMonto'
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

    private function _setIdReporte($valor) {
        return $this->setIdReporte($valor);
    }

    private function _getIdReporte($datos = []) {
        return $this->getIdReporte();
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

    private function _setReferencia($valor) {
        return $this->setReferencia($valor);
    }

    private function _getReferencia($datos = []) {
        return $this->getReferencia();
    }

    private function _setMonto($valor) {
        return $this->setMonto($valor);
    }

    private function _getMonto($datos = []) {
        return $this->getMonto();
    }

    private function _obtener_pendientes($datos = []) {
    try {
        $stmt = $this->conn->prepare("
            SELECT 
                po.*,
                p.monto_total AS total,
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

    private function _obtener_aprobados_sin_despacho($datos = []) {
    try {
        $sinDespacho = "";
        $check = $this->conn->query("SHOW TABLES LIKE 'despachos'");
        if ($check && $check->rowCount() > 0) {
            $sinDespacho = "AND p.id_pedido NOT IN (SELECT COALESCE(id_pedido, 0) FROM despachos)";
        }
        
        $stmt = $this->conn->prepare("
            SELECT 
                po.*,
                p.monto_total AS total,
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

    private function _obtener_todos($datos = []) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    po.*,
                    per.nombre,
                    per.apellido,
                    per.telefono
                FROM pago_online po
                JOIN pedidos p ON po.id_pedido = p.id_pedido
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                ORDER BY po.fecha_reporte DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_obtener_todos: " . $e->getMessage());
            return [];
        }
    }

    private function _obtener_por_id($datos = []) {
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

    private function _crear($datos = []) {
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
                return [
                    'success' => true,
                    'mensaje' => 'Pago reportado correctamente',
                    'id_reporte' => $this->conn->lastInsertId()
                ];
            }
            return ['error' => 'Error al guardar el pago'];
        } catch (\PDOException $e) {
            error_log("PagoOnlineModel::_crear: " . $e->getMessage());
            return ['error' => 'Error al guardar: ' . $e->getMessage()];
        }
    }

    private function _aprobar($datos = []) {
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

    private function _rechazar($datos = []) {
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

    private function _existe_por_pedido($datos = []) {
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

    private function _procesar_verificacion($datos = []) {
        try {
            $accion = $datos['accion'] ?? '';
            $id_reporte = $datos['id_reporte'] ?? 0;
            $verificado_por = $datos['verificado_por'] ?? 'cajera';
            
            if ($accion === 'aprobar') {
                return $this->_aprobar([
                    'id_reporte' => $id_reporte,
                    'verificado_por' => $verificado_por
                ]);
            } elseif ($accion === 'rechazar') {
                return $this->_rechazar([
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
}