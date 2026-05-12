<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class productos extends Conexion
{
    private $id_producto;
    private $nombre_producto;
    private $id_categoria;
    private $id_marca;
    private $stock_minimo;
    private $stock_maximo;
    private $stock_actual;
    private $precio_detal;
    
   
    private $imei;
    private $ram;
    private $almacenamiento;

    public function __construct()
    {
        parent::__construct();
    }

    public function existeNombre($nombre, $id = null)
    {
        $conex = new conexion("sistema");
        $sql = "SELECT COUNT(*) FROM productos WHERE nombre_producto = :n";
        if ($id) {
            $sql .= " AND id_producto != :id";
        }
        
        $stmt = $conex->prepare($sql);
        $params = [":n" => $nombre];
        if ($id) $params[":id"] = $id;

        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function registrar()
    {
        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Productos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

           
            $sql = "INSERT INTO productos(nombre_producto, id_categoria, id_marca, stock_minimo, stock_maximo, stock_actual, precio_detal) 
                    VALUES (:n, :c, :m, :smin, :smax, :sact, :p)";
            $stmt = $conex->prepare($sql);

            $stmt->execute([
                ":n"    => $this->nombre_producto,
                ":c"    => $this->id_categoria,
                ":m"    => $this->id_marca,
                ":smin" => $this->stock_minimo,
                ":smax" => $this->stock_maximo,
                ":sact" => $this->stock_actual,
                ":p"    => $this->precio_detal
            ]);

            $id_producto_generado = $conex->lastInsertId();

          
            if (!empty($this->imei)) {
                $sqlTel = "INSERT INTO unidades_telefonos(id_producto, imei, memoria_ram, almacenamiento) 
                           VALUES (:id, :imei, :ram, :alm)";
                $stmtTel = $conex->prepare($sqlTel);
                $stmtTel->execute([
                    ":id"   => $id_producto_generado,
                    ":imei" => $this->imei,
                    ":ram"  => $this->ram,
                    ":alm"  => $this->almacenamiento
                ]);
            }

            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function modificar()
    {
        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Productos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $sql = "UPDATE productos SET 
                    nombre_producto = :n, 
                    id_categoria = :c, 
                    id_marca = :m,
                    stock_minimo = :smin,
                    stock_maximo = :smax,
                    stock_actual = :sact,
                    precio_detal = :p
                    WHERE id_producto = :id";
            
            $stmt = $conex->prepare($sql);
            $stmt->execute([
                ":n"    => $this->nombre_producto,
                ":c"    => $this->id_categoria,
                ":m"    => $this->id_marca,
                ":smin" => $this->stock_minimo,
                ":smax" => $this->stock_maximo,
                ":sact" => $this->stock_actual,
                ":p"    => $this->precio_detal,
                ":id"   => $this->id_producto
            ]);

           
            if (!empty($this->imei)) {
                $sqlTel = "UPDATE unidades_telefonos SET imei = :imei, memoria_ram = :ram, almacenamiento = :alm 
                           WHERE id_producto = :id";
                $stmtTel = $conex->prepare($sqlTel);
                $stmtTel->execute([
                    ":imei" => $this->imei,
                    ":ram"  => $this->ram,
                    ":alm"  => $this->almacenamiento,
                    ":id"   => $this->id_producto
                ]);
            }

            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar()
{
    $id = $this->getId_producto();
    try {
        $conex = new conexion("sistema");
        $conex->beginTransaction();
        
        $user = $_SESSION["username"];
        $modulo = "Administrar Productos";
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = '{$modulo}'");
        
        
        $stmt = $conex->prepare("DELETE FROM productos WHERE id_producto = :id");
        if (!($stmt->execute([":id" => $id]))) {
            $conex->rollBack();
            return ["error" => "No se pudo eliminar el producto"];
        }
        
        $conex->commit();
        return true;
    } catch (PDOException $e) {
        if (isset($conex)) $conex->rollBack();
        return ["error" => $e->getMessage()];
    }
}

    public function listar()
    {
        $conex = new conexion("sistema");
      
        $sql = "SELECT p.*, c.nombre_categoria, m.nombre_marca, u.imei, u.memoria_ram, u.almacenamiento
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                INNER JOIN marcas m ON p.id_marca = m.id_marca
                LEFT JOIN unidades_telefonos u ON p.id_producto = u.id_producto";
        $stmt = $conex->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCategorias()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT id_categoria, nombre_categoria FROM categorias");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMarcas()
    {
        $conex = new conexion("sistema");
        $stmt = $conex->prepare("SELECT id_marca, nombre_marca FROM marcas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getId_producto() { return $this->id_producto; }
    public function setId_producto($id) { $this->id_producto = $id; }

    public function getNombre_producto() { return $this->nombre_producto; }
    public function setNombre_producto($n) { $this->nombre_producto = $n; }

    public function getId_categoria() { return $this->id_categoria; }
    public function setId_categoria($id) { $this->id_categoria = $id; }

    public function getId_marca() { return $this->id_marca; }
    public function setId_marca($id) { $this->id_marca = $id; }

    public function getStock_minimo() { return $this->stock_minimo; }
    public function setStock_minimo($s) { $this->stock_minimo = $s; }

    public function getStock_maximo() { return $this->stock_maximo; }
    public function setStock_maximo($s) { $this->stock_maximo = $s; }

    public function getStock_actual() { return $this->stock_actual; }
    public function setStock_actual($s) { $this->stock_actual = $s; }

    public function getPrecio_detal() { return $this->precio_detal; }
    public function setPrecio_detal($p) { $this->precio_detal = $p; }

   
    public function setImei($i) { $this->imei = $i; }
    public function setRam($r) { $this->ram = $r; }
    public function setAlmacenamiento($a) { $this->almacenamiento = $a; }
}