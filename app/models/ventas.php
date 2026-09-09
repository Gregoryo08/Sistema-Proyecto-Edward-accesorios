<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use Exception;

class ventas extends Conexion
{
    private $cedula;
    private $id;
    private $nombre;
    private $apellido;
    private $correo;
    private $direccion;
    private $telefono;
    private $edad;
    private $sexo;
    private $cargos;
    private $estado;


    public function __construct(){
        parent::__construct();
    }

    public function procesarSolicitud($accion, $datos = null){
        switch($accion) {
            case 'listarProductos':
                return $this->listarProductos();
            
            case 'listarMetodosPago':
                return $this->listarMetodosPago();
                
            case 'listarClientes':
                return $this->listarClientes();
            case 'registrarCliente':
                return $this->registrarCliente($datos);
                
            case 'registrarVenta':
                return $this->registrarVenta($datos);
                
            default:
                return [
                    "success" => false, 
                    "mensaje" => "Acción no reconocida en el modelo de ventas."
                ];
        }
    }

    private function listarProductos(){
        try {
            $sql = "SELECT p.id_producto, p.nombre_producto, c.nombre_categoria as categoria, p.precio_detal, p.stock_actual
                    FROM productos p 
                    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.stock_actual > 0"; 
            
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ventas::listarProductos -> " . $e->getMessage());
            return [];
        }
    }

    private function listarMetodosPago(){
        try {
            $sql = "SELECT id_metodopago, nombre_metodopago, moneda FROM metodo_pago";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ventas::listarMetodosPago -> " . $e->getMessage());
            return [];
        }
    }

    private function listarClientes(){
        try {
            $sql = "SELECT p.cedula_persona, p.nombre, p.apellido, p.telefono
                    FROM persona p
                    INNER JOIN clientes c ON p.cedula_persona = c.cedula_persona
                    WHERE c.estado = 'activo'";
            $stmt = $this->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ventas::listarClientes -> " . $e->getMessage());
            return [];
        }
    }

    private function registrarVenta($datos) {
        if (empty($datos)) {
            return ["success" => false, "mensaje" => "Faltan los datos requeridos para procesar la venta."];
        }

        try {
            $this->beginTransaction();

            $sqlSesion = "SET @usuario_actual = :usuario, @modulo = :modulo";
            $stmtSesion = $this->prepare($sqlSesion);
            $stmtSesion->execute([
                ':usuario' => $datos['cedula_usuario'],
                ':modulo'  => 'Administrar Ventas'                  
            ]);

            $sqlVenta = "INSERT INTO ventas (fecha_venta, origen_venta, total_venta, estado, cedula_persona, cedula_empleado) 
                         VALUES (NOW(), :origen, :total_venta, :estado, :cedula_persona, :cedula_empleado)";
            
            $stmtVenta = $this->prepare($sqlVenta);
            $stmtVenta->execute([
                ':origen'         => 'Presencial',
                ':total_venta'    => number_format($datos['total_usd'], 2, '.', ''),
                ':estado'         => 'completada',
                ':cedula_persona' => $datos['cliente']['cedula'] ?? null,
                ':cedula_empleado' => $datos['cedula_usuario']
            ]);

            $idVenta = $this->lastInsertId();

            $sqlDetalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                           VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)";
            
            $sqlDescontarStock = "UPDATE productos 
                                  SET stock_actual = stock_actual - :cantidad 
                                  WHERE id_producto = :id_producto AND stock_actual >= :cantidad";

            $stmtDetalle = $this->prepare($sqlDetalle);
            $stmtStock   = $this->prepare($sqlDescontarStock);

            foreach ($datos['items'] as $item) {
                $precioUnitarioUSD = $item['precio'];
                $subtotalItemUSD   = $precioUnitarioUSD * $item['cantidad'];

                $stmtDetalle->execute([
                    ':id_venta'        => $idVenta,
                    ':id_producto'     => $item['id'],
                    ':cantidad'        => $item['cantidad'],
                    ':precio_unitario' => number_format($precioUnitarioUSD, 2, '.', ''),
                    ':subtotal'        => number_format($subtotalItemUSD, 2, '.', '')
                ]);

                $stmtStock->execute([
                    ':cantidad'    => $item['cantidad'],
                    ':id_producto' => $item['id']
                ]);

                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("Operación cancelada. El producto '" . $item['nombre'] . "' se ha quedado sin stock disponible.");
                }
            }

            $sqlPagos = "INSERT INTO pagos (id_venta, id_metodopago, monto_recibido, referencia, fecha_pago) 
                         VALUES (:id_venta, :id_metodopago, :monto_recibido, :referencia, NOW())";
            
            $stmtPagos = $this->prepare($sqlPagos);

            foreach ($datos['pagos'] as $pago) {
                $stmtPagos->execute([
                    ':id_venta'       => $idVenta,
                    ':id_metodopago'  => $pago['id_metodopago'],
                    ':monto_recibido' => number_format($pago['monto_dolar'], 2, '.', ''), 
                    ':referencia'     => !empty($pago['referencia']) ? $pago['referencia'] : null
                ]);
            }

            $this->commit();

            return [
                "success" => true,
                "mensaje" => "¡Excelente! La venta ha sido procesada con éxito."
            ];

        } catch (Exception $e) {
            if ($this->inTransaction()) {
                $this->rollBack();
            }
            error_log("Error en ventas::registrarVenta -> " . $e->getMessage());
            return [
                "success" => false,
                "mensaje" => "Error interno al guardar la transacción: " . $e->getMessage()
            ];
        }
    }

    private function registrarCliente (){
        $cedula = $this->getCedula();
        
        try {
            $this->beginTransaction();

            $sqlSesion = "SET @usuario_actual = :usuario, @modulo = :modulo";
            $stmtSesion = $this->prepare($sqlSesion);
            $stmtSesion->execute([
                ':usuario' => $_SESSION['username'] ?? 'Sistema',
                ':modulo'  => 'Administrar Ventas'
            ]);
            
            $sqlPersona = "INSERT INTO persona (cedula_persona, nombre, apellido, telefono, correo, sexo, fecha_nacimiento, direccion) 
                           VALUES (:cedula, :nombre, :apellido, :telefono, :correo, :sexo, :fecha, :direccion)";
            $stmtPersona = $this->prepare($sqlPersona);
            $stmtPersona->execute([
                ":cedula" => $cedula, ":nombre" => $this->getNombre(), ":apellido" => $this->getApellido(),
                ":telefono" => $this->getCel(), ":correo" => $this->getCorreo(), 
                ":sexo" => $this->getSexo(), ":fecha" => $this->getEdad(), ":direccion" => $this->getDireccion()
            ]);

            $sqlCliente = "INSERT INTO clientes (cedula_persona,  estado) VALUES (:cedula, :estado)";
            $this->prepare($sqlCliente)->execute([":cedula" => $cedula, ":estado" => "activo"]);

            $this->commit();
            return true;
        } catch (Exception $e) {
            if ($this->inTransaction()) $this->rollBack();
            return ["error" => $e->getMessage()];
        }

    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getCedula() { return $this->cedula; }
    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function getApellido() { return $this->apellido; }
    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function getSexo() { return $this->sexo; }
    public function setSexo($sexo) { $this->sexo = $sexo; }
    public function getCel() { return $this->telefono; }
    public function setCel($cel) { $this->telefono = $cel; }
    public function getDireccion() { return $this->direccion; }
    public function setDireccion($dir) { $this->direccion = $dir; }
    public function getCorreo() { return $this->correo; }
    public function setCorreo($correo) { $this->correo = $correo; }
    public function getCargo() { return $this->cargos; }
    public function setCargo($cargo) { $this->cargos = $cargo; }
    public function getEdad() { return $this->edad; }
    public function setEdad($edad) { $this->edad = $edad; }
    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }
}