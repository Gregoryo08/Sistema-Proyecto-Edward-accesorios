<?php
// /src/app/models/EnvioModel.php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use PDOException;

class EnvioModel {
    private $conn;
    private $id_despacho;
    private $id_pedido;
    private $tipo_despacho;
    private $estado_despacho;

    public function setIdDespacho($valor) {
        if (!is_numeric($valor) || $valor <= 0) {
            throw new \InvalidArgumentException('ID de despacho inválido');
        }
        $this->id_despacho = $valor;
        return $this;
    }

    public function getIdDespacho() {
        return $this->id_despacho;
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

    public function setTipoDespacho($valor) {
        if (!in_array($valor, ['tienda', 'delivery'])) {
            throw new \InvalidArgumentException('Tipo de despacho inválido');
        }
        $this->tipo_despacho = $valor;
        return $this;
    }

    public function getTipoDespacho() {
        return $this->tipo_despacho;
    }

    public function setEstadoDespacho($valor) {
        $estados = ['pendiente', 'asignado', 'en_ruta', 'entregado', 'cancelado'];
        if (!in_array($valor, $estados)) {
            throw new \InvalidArgumentException('Estado de despacho inválido');
        }
        $this->estado_despacho = $valor;
        return $this;
    }

    public function getEstadoDespacho() {
        return $this->estado_despacho;
    }

    public function __construct() {
        $this->conn = Conexion::getShared('sistema_edward')->getConexion();
    }

    // =============================================
    // 🔒 MÉTODOS PRIVADOS (lógica interna)
    // =============================================

    /**
     * Validar datos del despacho antes de insertar
     * 🔒 PRIVADO: solo usado internamente
     */
    private function _validarDatosDespacho($datos) {
        if (empty($datos['id_pedido']) || $datos['id_pedido'] <= 0) {
            throw new PDOException("ID de pedido inválido");
        }
        if (!in_array($datos['tipo_despacho'] ?? 'delivery', ['tienda', 'delivery'])) {
            throw new PDOException("Tipo de despacho inválido");
        }
    }

    /**
     * Verificar si la tabla despachos existe
     * 🔒 PRIVADO: solo usado internamente
     */
    private function _tablaExiste() {
        $stmt = $this->conn->prepare("SHOW TABLES LIKE 'despachos'");
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtener datos del despacho con JOINs
     * 🔒 PRIVADO: solo usado internamente
     */
    private function _obtenerDespachoConJoin($where, $params = []) {
        $sql = "
            SELECT 
                d.*,
                p.id_pedido,
                p.total,
                p.direccion_entrega,
                per.nombre,
                per.apellido,
                per.telefono,
                per.correo
            FROM despachos d
            JOIN pedidos p ON d.id_pedido = p.id_pedido
            JOIN persona per ON p.cedula_persona = per.cedula_persona
        ";
        
        if ($where) {
            $sql .= " WHERE " . $where;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // =============================================
    // ✅ MÉTODO PÚBLICO DE ACCESO (Patrón de José)
    // =============================================

    public function ejecutar($accion, $datos = []) {
        switch ($accion) {
            case 'obtenerActivos': return $this->_obtenerActivos();
            case 'obtenerPorId': return $this->_obtenerPorId($datos['id_despacho']);
            case 'obtenerPorPedido': return $this->_obtenerPorPedido($datos['id_pedido']);
            case 'crear': return $this->_crear($datos);
            case 'actualizarEstado': return $this->_actualizarEstado($datos['id_despacho'], $datos['estado']);
            case 'marcarEntregado': return $this->_marcarEntregado($datos['id_despacho']);
            default: return ['error' => 'Acción no válida'];
        }
    }

    // =============================================
    // 🔒 MÉTODOS PRIVADOS (tu código existente)
    // =============================================

    private function _obtenerActivos() {
        try {
            if (!$this->_tablaExiste()) {
                return [];
            }
            
            $stmt = $this->_obtenerDespachoConJoin(
                "d.estado_despacho IN ('pendiente', 'asignado', 'en_ruta')",
                []
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_obtenerActivos: " . $e->getMessage());
            return [];
        }
    }

    private function _obtenerPorId($id_despacho) {
        try {
            $stmt = $this->_obtenerDespachoConJoin("d.id_despacho = ?", [$id_despacho]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_obtenerPorId: " . $e->getMessage());
            return null;
        }
    }

    private function _obtenerPorPedido($id_pedido) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM despachos 
                WHERE id_pedido = ?
                ORDER BY fecha_despacho DESC
            ");
            $stmt->execute([$id_pedido]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_obtenerPorPedido: " . $e->getMessage());
            return [];
        }
    }

    private function _crear($datos) {
        try {
            $this->_validarDatosDespacho($datos);
            
            $stmt = $this->conn->prepare("
                INSERT INTO despachos (
                    id_pedido, 
                    tipo_despacho, 
                    fecha_despacho, 
                    hora_despacho,
                    despachador_nombre, 
                    despachador_telefono,
                    instrucciones_entrega,
                    fecha_entrega_estimada,
                    estado_despacho
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')
            ");
            
            $resultado = $stmt->execute([
                $datos['id_pedido'],
                $datos['tipo_despacho'] ?? 'delivery',
                $datos['fecha_despacho'] ?? date('Y-m-d'),
                $datos['hora_despacho'] ?? null,
                $datos['despachador_nombre'] ?? null,
                $datos['despachador_telefono'] ?? null,
                $datos['instrucciones_entrega'] ?? null,
                $datos['fecha_entrega_estimada'] ?? null
            ]);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'id_despacho' => $this->conn->lastInsertId()
                ];
            }
            return ['error' => 'Error al crear el despacho'];
            
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_crear: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function _actualizarEstado($id_despacho, $estado) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE despachos 
                SET estado_despacho = ? 
                WHERE id_despacho = ?
            ");
            return $stmt->execute([$estado, $id_despacho]);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_actualizarEstado: " . $e->getMessage());
            return false;
        }
    }

    private function _marcarEntregado($id_despacho) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE despachos 
                SET estado_despacho = 'entregado',
                    fecha_entrega_real = NOW()
                WHERE id_despacho = ?
            ");
            return $stmt->execute([$id_despacho]);
        } catch (PDOException $e) {
            error_log("Error en EnvioModel::_marcarEntregado: " . $e->getMessage());
            return false;
        }
    }
}