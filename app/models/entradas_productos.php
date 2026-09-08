<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class entradas_productos extends Conexion
{
    private $id_entrada;
    private $id_producto;
    private $rif_proveedor;
    private $cantidad;
    private $cantidadOLD;
    private $dias_garantia;

    public function __construct() 
    { 
        parent::__construct(); 
    }

    public function procesarSolicitud($accion, $datos = []) 
    {
        switch ($accion) {
            case 'registrarEntrada':
                return $this->registrarEntrada();
            case 'insertarDetalle':
                return $this->insertarDetalleEntrada();
            case 'modificarDetalle':
                return $this->modificarDetallesEntrada();
            case 'eliminarEntrada':
                return $this->eliminarEntrada();
            case 'consultarEntradas':
                return $this->obtenerEntradas();
            case 'consultarDetalles':
                return $this->obtenerDetallesEntradas();
            case 'consultarProductosSinUnidad':
                return $this->obtenerProductosSinUnidad();
            case 'verificarGarantiasProximas':
                return $this->verificarGarantiasPorVencer();
            default:
                return ["error" => "Acción no reconocida"];
        }
    }

    private function registrarEntrada()
    {
        if (empty($this->rif_proveedor)) {
            return ["error" => "El proveedor es obligatorio"];
        }

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? "system";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Entradas'");
            
            $check = $this->prepare("SELECT rif_proveedor FROM proveedores WHERE rif_proveedor = ?");
            $check->execute([$this->rif_proveedor]);
            
            if ($check->rowCount() === 0) {
                $this->rollBack();
                return ["error" => "El proveedor seleccionado no existe"];
            }

            $sql = "INSERT INTO entradas_productos (rif_proveedor_fk, fecha_entrada) VALUES (?, NOW())";
            $stmt = $this->prepare($sql);
            $stmt->execute([$this->rif_proveedor]);
            
            $id = $this->lastInsertId();
            $this->commit();
            return ["success" => true, "id_entrada" => $id];
        } catch (PDOException $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            return ["error" => "Error al registrar la entrada: " . $e->getMessage()];
        }
    }

    private function insertarDetalleEntrada()
    {
        if (empty($this->id_entrada) || $this->id_entrada <= 0) return ["error" => "ID entrada inválido"];
        if (empty($this->id_producto) || $this->id_producto <= 0) return ["error" => "Producto no válido"];
        if (!is_numeric($this->cantidad) || $this->cantidad <= 0) return ["error" => "Cantidad inválida"];
        if (!is_numeric($this->dias_garantia) || $this->dias_garantia < 0) return ["error" => "Garantía debe ser 0 o superior"];

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? "system";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Entradas'");

            $sql = "INSERT INTO detalles_entrada (id_entrada_fk, id_producto_fk, cantidad_entrada, dias_garantia) VALUES (?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->execute([$this->id_entrada, $this->id_producto, $this->cantidad, $this->dias_garantia]);

            $stmt2 = $this->prepare("UPDATE productos SET stock_actual = stock_actual + ? WHERE id_producto = ?");
            $stmt2->execute([$this->cantidad, $this->id_producto]);

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            return ["error" => "Error al registrar el detalle de la entrada: " . $e->getMessage()];
        }
    }

    private function modificarDetallesEntrada()
    {
        if (empty($this->id_entrada) || empty($this->id_producto)) return ["error" => "Campos de identificación incompletos"];
        if (!is_numeric($this->cantidad) || $this->cantidad <= 0) return ["error" => "Cantidad inválida"];
        if (!is_numeric($this->dias_garantia) || $this->dias_garantia < 0) return ["error" => "Garantía inválida"];

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? "system";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Entradas'");

            $stmtCheck = $this->prepare("SELECT cantidad_entrada FROM detalles_entrada WHERE id_entrada_fk = ? AND id_producto_fk = ?");
            $stmtCheck->execute([$this->id_entrada, $this->id_producto]);
            $registroActual = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$registroActual) {
                $this->rollBack();
                return ["error" => "El detalle que intenta modificar no existe"];
            }

            $cantidadBaseReal = (float)$registroActual['cantidad_entrada'];

            $sql = "UPDATE detalles_entrada SET cantidad_entrada = ?, dias_garantia = ? WHERE id_entrada_fk = ? AND id_producto_fk = ?";
            $stmt = $this->prepare($sql);
            $stmt->execute([$this->cantidad, $this->dias_garantia, $this->id_entrada, $this->id_producto]);

            $diff = $this->cantidad - $cantidadBaseReal;
            
            $stmt2 = $this->prepare("UPDATE productos SET stock_actual = stock_actual + ? WHERE id_producto = ?");
            $stmt2->execute([$diff, $this->id_producto]);

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            return ["error" => "Error al modificar la entrada: " . $e->getMessage()];
        }
    }

    private function eliminarEntrada()
    {
        if (empty($this->id_entrada)) return ["error" => "ID de entrada requerido"];

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? "system";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = 'Administrar Entradas'");

            $detalles = $this->prepare("SELECT id_producto_fk, cantidad_entrada FROM detalles_entrada WHERE id_entrada_fk = ?");
            $detalles->execute([$this->id_entrada]);
            $lista = $detalles->fetchAll(PDO::FETCH_ASSOC);

            foreach ($lista as $row) {
                $this->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?")
                     ->execute([$row['cantidad_entrada'], $row['id_producto_fk']]);
            }

            $this->prepare("DELETE FROM detalles_entrada WHERE id_entrada_fk = ?")->execute([$this->id_entrada]);
            $this->prepare("DELETE FROM entradas_productos WHERE id_entrada = ?")->execute([$this->id_entrada]);

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            return ["error" => "Error al eliminar la entrada: " . $e->getMessage()];
        }
    }

    private function obtenerEntradas()
    {
        $sql = "SELECT e.id_entrada, p.nombre_proveedor AS nom_proveedor, 
                       DATE_FORMAT(e.fecha_entrada,'%d-%m-%Y %H:%i:%s') AS fecha_formateada 
                FROM entradas_productos e 
                INNER JOIN proveedores p ON e.rif_proveedor_fk = p.rif_proveedor 
                ORDER BY e.fecha_entrada DESC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerDetallesEntradas()
    {
        $sql = "SELECT d.*, p.nombre_producto 
                FROM detalles_entrada d 
                LEFT JOIN productos p ON d.id_producto_fk = p.id_producto
                WHERE d.id_entrada_fk = ?";
        $stmt = $this->prepare($sql);
        $stmt->execute([$this->id_entrada]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obtenerProductosSinUnidad()
    {
        $sql = "SELECT p.id_producto, p.nombre_producto, p.id_categoria, c.nombre_categoria 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_producto NOT IN (SELECT DISTINCT id_producto FROM unidades_telefonos)
                ORDER BY p.nombre_producto ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function verificarGarantiasPorVencer()
    {
        $sql = "SELECT d.id_producto_fk, d.dias_garantia, p.nombre_producto, 
                DATE_ADD(e.fecha_entrada, INTERVAL d.dias_garantia DAY) as fecha_fin_garantia,
                DATEDIFF(DATE_ADD(e.fecha_entrada, INTERVAL d.dias_garantia DAY), CURDATE()) as dias_restantes
                FROM detalles_entrada d
                INNER JOIN entradas_productos e ON d.id_entrada_fk = e.id_entrada
                INNER JOIN productos p ON d.id_producto_fk = p.id_producto
                WHERE d.dias_garantia IS NOT NULL AND d.dias_garantia > 0
                AND DATE_ADD(e.fecha_entrada, INTERVAL d.dias_garantia DAY) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                ORDER BY dias_restantes ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getId_entrada() { return $this->id_entrada; }
    public function setId_entrada($id) {
        if (is_numeric($id) && $id > 0) { $this->id_entrada = (int)$id; return true; }
        return false;
    }

    public function getId_producto() { return $this->id_producto; }
    public function setId_producto($id) {
        if (is_numeric($id) && $id > 0) { $this->id_producto = (int)$id; return true; }
        return false;
    }

    public function getRif_proveedor() { return $this->rif_proveedor; }
    public function setRif_proveedor($rif) {
        $r = htmlspecialchars(strip_tags(trim($rif)));
        if (!empty($r)) { $this->rif_proveedor = $r; return true; }
        return false;
    }

    public function getCantidad() { return $this->cantidad; }
    public function setCantidad($c) {
        if (is_numeric($c) && $c >= 0) { $this->cantidad = (float)$c; return true; }
        return false;
    }

    public function getCantidadOLD() { return $this->cantidadOLD; }
    public function setCantidadOLD($c) {
        if (is_numeric($c) && $c >= 0) { $this->cantidadOLD = (float)$c; return true; }
        return false;
    }

    public function getDiasGarantia() { return $this->dias_garantia; }
    public function setDiasGarantia($g) {
        if (is_numeric($g) && $g >= 0) { $this->dias_garantia = (int)$g; return true; }
        return false;
    }
}