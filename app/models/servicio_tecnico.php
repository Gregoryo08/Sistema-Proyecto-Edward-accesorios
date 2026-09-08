<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;
use \Exception;

class servicio_tecnico extends Conexion
{
    private $id_servicio;
    private $cedula_persona;
    private $equipo_descripcion;
    private $falla_inicial;
    private $diagnostico;
    private $estado;
    private $monto_total;
    private $id_especialidad; 

    public function __construct()
    {
        parent::__construct();
    }

 public function procesarSolicitud($accion, $datos = [])
{
    if (isset($datos['id'])) {
        $this->setId_servicio($datos['id']);
    }

    switch ($accion) {
        case 'listar':
            return $this->listar();
        case 'consultar':
            return $this->consultar($this->id_servicio);
        case 'consultar_productos':
            return $this->consultarProductos($this->id_servicio);
        case 'registrar':
    $this->setCedula($datos['cedula'] ?? '');
    $this->setEquipo($datos['equipo'] ?? '');
    $this->setFalla_inicial($datos['falla'] ?? '');
    $this->setId_especialidad($datos['especialidad'] ?? null); 
    return $this->registrar();
        case 'modificar':
    $this->setEquipo($datos['equipo'] ?? '');
    $this->setFalla_inicial($datos['falla'] ?? '');
    $this->setDiagnostico($datos['diagnostico'] ?? '');
    $this->setEstado($datos['estado'] ?? '');
    $this->setMonto_total($datos['monto'] ?? 0);
    $productos = is_string($datos['productos']) ? json_decode($datos['productos'], true) : $datos['productos'];
    return $this->modificar($productos);
        case 'cobrar':
            return $this->registrarCobro(
                $this->id_servicio, 
                $datos['monto'] ?? 0, 
                $datos['diagnostico'] ?? '', 
                $datos['nota_tecnico'] ?? ''
            );
        case 'eliminar':
            return $this->eliminar();
        default:
            return ["error" => "Acción no válida"];
    }
}

public function listarProductos($filtro = '')
{
    $conex = new conexion("sistema");
   
    $sql = "SELECT id_producto, nombre_producto, stock_actual, precio_detal FROM productos WHERE estado = 1 AND stock_actual > 0 AND id_categoria = 27";
    
    if (!empty($filtro)) {
        $sql .= " AND nombre_producto LIKE ?";
        $stmt = $conex->prepare($sql);
        $stmt->execute(['%' . $filtro . '%']);
    } else {
        $stmt = $conex->prepare($sql);
        $stmt->execute();
    }
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    unset($conex);
    return $res;
}

    public function listarMetodosPago()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT id_metodopago, nombre_metodopago FROM metodo_pago WHERE estatus = 1");
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $res;
    }

    public function agregarProductoServicio($id_servicio, $id_producto, $cantidad)
    {
        $conex = new conexion("sistema");
        $conex->beginTransaction();
        $user = $_SESSION["username"];
        $modulo = "Administrar Servicio Tecnico";
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = '{$modulo}'");
        try {
            $stmt = $conex->prepare("SELECT stock_actual, precio_detal FROM productos WHERE id_producto = ? FOR UPDATE");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$producto || $producto['stock_actual'] < $cantidad) {
                throw new Exception("Stock insuficiente");
            }
            $precio = $producto['precio_detal'];
            $subtotal = $precio * $cantidad;
            $stmt = $conex->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id_producto = ?");
            $stmt->execute([$cantidad, $id_producto]);
            $stmt = $conex->prepare("INSERT INTO servicio_productos (id_servicio, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_servicio, $id_producto, $cantidad, $precio]);
            $stmt = $conex->prepare("UPDATE servicio_tecnico SET monto_total = monto_total + ? WHERE id_servicio = ?");
            $stmt->execute([$subtotal, $id_servicio]);
            $conex->commit();
            unset($conex);
            return ["success" => true];
        } catch (Exception $e) {
            $conex->rollBack();
            unset($conex);
            return ["success" => false, "mensaje" => $e->getMessage()];
        }
    }

    private function listar()
    {
        $conex = new conexion("sistema");
        try {
            $stmt = $conex->prepare("SELECT id_servicio, cedula_persona, equipo_descripcion, falla_inicial, estado, monto_total, fecha_registro FROM servicio_tecnico");
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            unset($conex);
            return $res;
        } catch (PDOException $e) {
            unset($conex);
            return [];
        }
    }

   private function consultar($id)
{
    if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT)) return ["error" => "ID inválido"];
    $conex = new conexion("sistema");
    try {
        $stmt = $conex->prepare("
            SELECT st.*, dst.diagnostico, dst.nota_tecnico, dst.garantia_dias 
            FROM servicio_tecnico st 
            LEFT JOIN detalles_servicio_tecnico dst ON st.id_servicio = dst.id_servicio 
            WHERE st.id_servicio = :id
        ");
        $stmt->execute([":id" => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($conex);
        return $res ?: ["error" => "No encontrado"];
    } catch (PDOException $e) {
        unset($conex);
        return ["error" => $e->getMessage()];
    }
}

private function consultarProductos($id_servicio)
{
    $conex = new conexion("sistema");
    $stmt = $conex->prepare("
        SELECT 
            sp.id_producto, 
            p.nombre_producto, 
            sp.cantidad, 
            sp.precio_unitario,
            dst.diagnostico,
            dst.nota_tecnico,
            e.nombre_especialidad
        FROM servicio_productos sp 
        JOIN productos p ON sp.id_producto = p.id_producto 
        JOIN detalles_servicio_tecnico dst ON sp.id_servicio = dst.id_servicio
        LEFT JOIN especialidades e ON dst.id_especialidad = e.id_especialidad
        WHERE sp.id_servicio = :id
    ");
    $stmt->execute([":id" => $id_servicio]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    unset($conex);
    return $res;
}
    private function registrar()
{
    $conex = new conexion("sistema");
    try {
        $conex->beginTransaction();
        
        $user = $_SESSION["username"];
        $modulo = "Administrar Servicio Tecnico";
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = '{$modulo}'");

        // 1. Insertar en tabla principal
        $stmt = $conex->prepare("INSERT INTO servicio_tecnico (cedula_persona, equipo_descripcion, falla_inicial, estado) VALUES (:cedula, :equipo, :falla, 'Pendiente')");
        $stmt->execute([":cedula" => $this->cedula_persona, ":equipo" => $this->equipo_descripcion, ":falla" => $this->falla_inicial]);
        
        $id_servicio = $conex->lastInsertId();

        // 2. Insertar en detalles con la especialidad
        $stmtDetalle = $conex->prepare("INSERT INTO detalles_servicio_tecnico (id_servicio, id_especialidad) VALUES (:id_servicio, :id_especial)");
        $stmtDetalle->execute([":id_servicio" => $id_servicio, ":id_especial" => $this->id_especialidad]);

        $conex->commit();
        unset($conex);
        return ["success" => true];
    } catch (PDOException $e) {
        $conex->rollBack();
        unset($conex);
        return ["error" => $e->getMessage()];
    }
}

private function modificar($productos)
{
    $conex = new conexion("sistema");
    try {
        $conex->beginTransaction();

        $stmt = $conex->prepare("UPDATE servicio_tecnico SET equipo_descripcion = :equipo, falla_inicial = :falla, estado = :estado, monto_total = :monto WHERE id_servicio = :id");
        $stmt->execute([
            ":equipo" => $this->equipo_descripcion,
            ":falla" => $this->falla_inicial,
            ":estado" => $this->estado,
            ":monto" => $this->monto_total,
            ":id" => $this->id_servicio
        ]);

        $stmtDiag = $conex->prepare("UPDATE detalles_servicio_tecnico SET diagnostico = :diag WHERE id_servicio = :id");
        $stmtDiag->execute([":id" => $this->id_servicio, ":diag" => $this->diagnostico]);

        $conex->prepare("DELETE FROM servicio_productos WHERE id_servicio = :id")->execute([":id" => $this->id_servicio]);

        if (!empty($productos) && is_array($productos)) {
            $stmtInsert = $conex->prepare("INSERT INTO servicio_productos (id_servicio, id_producto, cantidad, precio_unitario) SELECT :id, :id_prod, :cant, precio_detal FROM productos WHERE id_producto = :id_prod");
            
            foreach ($productos as $prod) {
                $stmtInsert->execute([
                    ":id" => $this->id_servicio,
                    ":id_prod" => $prod['id_producto'],
                    ":cant" => $prod['cantidad']
                ]);
            }
        }

        $conex->commit();
        unset($conex);
        return ["success" => true];
    } catch (PDOException $e) {
        $conex->rollBack();
        unset($conex);
        return ["error" => "Error DB: " . $e->getMessage()];
    }
}
private function registrarCobro($id_servicio, $monto_final, $diagnostico, $nota_tecnico)
{
    $conex = new conexion("sistema");
    $conex->beginTransaction();
    $user = $_SESSION["username"];
    $modulo = "Administrar Servicio Tecnico";
    $conex->exec("SET @usuario_actual = '{$user}'");
    $conex->exec("SET @modulo = '{$modulo}'");
    
    try {
        
        $stmt = $conex->prepare("UPDATE servicio_tecnico SET estado = 'Cobrado', monto_total = :monto WHERE id_servicio = :id");
        $stmt->execute([":monto" => $monto_final, ":id" => $id_servicio]);

        
        $stmtDetalle = $conex->prepare("UPDATE detalles_servicio_tecnico SET diagnostico = :diag, nota_tecnico = :nota WHERE id_servicio = :id");
        $stmtDetalle->execute([
            ":id"   => $id_servicio, 
            ":diag" => $diagnostico, 
            ":nota" => $nota_tecnico
        ]);

        $conex->commit();
        unset($conex);
        return ["success" => true];
        
    } catch (Exception $e) {
        $conex->rollBack();
        unset($conex);
        return ["success" => false, "mensaje" => $e->getMessage()];
    }
}

    private function eliminar()
    {
        $conex = new conexion("sistema");
        try {
            $user = $_SESSION["username"];
            $modulo = "Administrar Servicio Tecnico";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");
            $stmt = $conex->prepare("DELETE FROM servicio_tecnico WHERE id_servicio = :id");
            $stmt->execute([":id" => $this->id_servicio]);
            unset($conex);
            return ["success" => true];
        } catch (PDOException $e) {
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function listarClientes()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT p.*, c.estado FROM persona p INNER JOIN clientes c ON p.cedula_persona = c.cedula_persona");
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $res;
    }

    public function listarEspecialidad()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT * FROM especialidades");
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $res;
    }

    public function listarMarca()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT * FROM marcas");
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $res;
    }

    public function setId_servicio($id) { $this->id_servicio = $id; }
    public function setCedula($cedula) { $this->cedula_persona = $cedula; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setEquipo($equipo) { $this->equipo_descripcion = $equipo; }
    public function setFalla_inicial($falla) { $this->falla_inicial = $falla; }
    public function setDiagnostico($diag) { $this->diagnostico = $diag; }
    public function setMonto_total($monto) { $this->monto_total = $monto; }
    public function setId_especialidad($id) { $this->id_especialidad = $id; }
}