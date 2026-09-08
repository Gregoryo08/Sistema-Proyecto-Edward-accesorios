<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class productos extends Conexion
{
    private $id_producto;
    private $nombre_producto;
    private $descripcion;
    private $id_categoria;
    private $id_marca;
    private $stock_minimo;
    private $stock_maximo;
    private $stock_actual;
    private $precio_detal;
    
    private $imagen_principal;
    private $imei;
    private $ram;
    private $almacenamiento;

    public function __construct()
    {
        parent::__construct();
    }

    public function existeNombre($nombre, $id = null)
    {
        $conex = new Conexion("sistema");
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
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Productos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            if (!empty($this->imei)) {
                $this->stock_actual = 1;
                $this->stock_minimo = 0; 
                $this->stock_maximo = 1;
            }

            $sql = "INSERT INTO productos(nombre_producto, descripcion, imagen_principal, id_categoria, id_marca, stock_minimo, stock_maximo, stock_actual, precio_detal) 
                    VALUES (:n, :d, :img, :c, :m, :smin, :smax, :sact, :p)";
            $stmt = $conex->prepare($sql);

            $stmt->execute([
                ":n"    => $this->nombre_producto,
                ":d"    => $this->descripcion,
                ":img"  => $this->imagen_principal,
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
            $conex = new Conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Productos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            if (!empty($this->imei)) {
                $this->stock_actual = 1;
            }

            $sql = "UPDATE productos SET 
                    nombre_producto = :n, 
                    descripcion = :d,
                    imagen_principal = :img,
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
                ":d"    => $this->descripcion,
                ":img"  => $this->imagen_principal,
                ":c"    => $this->id_categoria,
                ":m"    => $this->id_marca,
                ":smin" => $this->stock_minimo,
                ":smax" => $this->stock_maximo,
                ":sact" => $this->stock_actual,
                ":p"    => $this->precio_detal,
                ":id"   => $this->id_producto
            ]);

            if (!empty($this->imei)) {
                $sqlCheck = "SELECT COUNT(*) FROM unidades_telefonos WHERE id_producto = :id";
                $stmtCheck = $conex->prepare($sqlCheck);
                $stmtCheck->execute([":id" => $this->id_producto]);
                
                if ($stmtCheck->fetchColumn() > 0) {
                    $sqlTel = "UPDATE unidades_telefonos SET imei = :imei, memoria_ram = :ram, almacenamiento = :alm 
                               WHERE id_producto = :id";
                } else {
                    $sqlTel = "INSERT INTO unidades_telefonos (imei, memoria_ram, almacenamiento, id_producto) 
                               VALUES (:imei, :ram, :alm, :id)";
                }
                
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
            $conex = new Conexion("sistema");
            $conex->beginTransaction();
            
            $user = $_SESSION["username"];
            $modulo = "Administrar Productos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");
            
            $stmt = $conex->prepare("DELETE FROM productos WHERE id_producto = :id");
            $stmt->execute([":id" => $id]);
            
            $conex->commit();
            return true;
        } catch (PDOException $e) {
            if (isset($conex)) $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function listar()
    {
        $conex = new Conexion("sistema");
        $sql = "SELECT p.*, c.nombre_categoria, m.nombre_marca, u.imei, u.memoria_ram, u.almacenamiento
                FROM productos p 
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                INNER JOIN marcas m ON p.id_marca = m.id_marca
                LEFT JOIN unidades_telefonos u ON p.id_producto = u.id_producto
                ORDER BY p.id_producto DESC";
        $stmt = $conex->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCategorias()
    {
        $conex = new Conexion("sistema");
        $stmt = $conex->prepare("SELECT id_categoria, nombre_categoria FROM categorias");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMarcas()
    {
        $conex = new Conexion("sistema");
        $stmt = $conex->prepare("SELECT id_marca, nombre_marca FROM marcas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getId_producto() { return $this->id_producto; }
    public function setId_producto($id) { $this->id_producto = $id; }
    public function getNombre_producto() { return $this->nombre_producto; }
    public function setNombre_producto($n) { $this->nombre_producto = $n; }
    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($d) { $this->descripcion = $d; }
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
    public function getImagen_principal() { return $this->imagen_principal; }
    public function setImagen_principal($img) { $this->imagen_principal = $img; }

    public function actualizarImagen($id, $imagen)
    {
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("UPDATE productos SET imagen_principal = :img WHERE id_producto = :id");
            $stmt->execute([":img" => $imagen, ":id" => $id]);
            return true;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function obtenerImagen($id)
    {
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("SELECT id_producto, nombre_producto, imagen_principal FROM productos WHERE id_producto = :id");
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function renombrarImagen($id, $nuevoNombre)
    {
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("SELECT imagen_principal, nombre_producto FROM productos WHERE id_producto = :id");
            $stmt->execute([":id" => $id]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$prod || empty($prod['imagen_principal'])) {
                return ["error" => "El producto no tiene imagen asociada"];
            }

            $directorio = __DIR__ . '/../../assets/img/productos/';
            $actual = $prod['imagen_principal'];
            $rutaActual = $directorio . $actual;

            if (!file_exists($rutaActual)) {
                return ["error" => "No se encontró el archivo de imagen en el servidor"];
            }

            $nuevoNombreLimpio = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $nuevoNombre);
            if (empty($nuevoNombreLimpio)) {
                return ["error" => "Nombre de archivo no válido"];
            }

            if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $nuevoNombreLimpio)) {
                $ext = strtolower(pathinfo($actual, PATHINFO_EXTENSION));
                $nuevoNombreLimpio .= '.' . $ext;
            }

            $rutaNueva = $directorio . $nuevoNombreLimpio;
            if (file_exists($rutaNueva) && $rutaNueva !== $rutaActual) {
                return ["error" => "Ya existe un archivo con ese nombre: " . $nuevoNombreLimpio];
            }

            if (rename($rutaActual, $rutaNueva)) {
                $stmt2 = $conex->prepare("UPDATE productos SET imagen_principal = :img WHERE id_producto = :id");
                $stmt2->execute([":img" => $nuevoNombreLimpio, ":id" => $id]);
                return ["success" => true, "nuevo_nombre" => $nuevoNombreLimpio];
            }

            return ["error" => "No se pudo renombrar el archivo"];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminarImagen($id)
    {
        try {
            $conex = new Conexion("sistema");
            $stmt = $conex->prepare("SELECT imagen_principal FROM productos WHERE id_producto = :id");
            $stmt->execute([":id" => $id]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($prod && !empty($prod['imagen_principal']) && $prod['imagen_principal'] !== 'default.jpg') {
                $directorio = __DIR__ . '/../../assets/img/productos/';
                $ruta = $directorio . $prod['imagen_principal'];
                if (file_exists($ruta)) {
                    @unlink($ruta);
                }
            }

            $stmt2 = $conex->prepare("UPDATE productos SET imagen_principal = 'default.jpg' WHERE id_producto = :id");
            $stmt2->execute([":id" => $id]);
            return true;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function autoAsociarImagenes()
    {
        $directorio = __DIR__ . '/../../assets/img/productos/';
        $resultados = ['asociadas' => 0, 'renombradas' => 0, 'sin_match' => [], 'ya_tenian' => 0];

        $conex = new Conexion("sistema");
        $stmt = $conex->query("SELECT id_producto, nombre_producto, imagen_principal FROM productos ORDER BY id_producto");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $archivos = glob($directorio . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        foreach ($productos as $prod) {
            $nombre_norm = strtolower(trim($prod['nombre_producto']));
            $nombre_norm = preg_replace('/\s+/', '_', $nombre_norm);
            $nombre_norm = preg_replace('/[^a-z0-9_\-]/', '', $nombre_norm);

            if (!empty($prod['imagen_principal']) && $prod['imagen_principal'] !== 'default.jpg' && file_exists($directorio . $prod['imagen_principal'])) {
                if ($prod['imagen_principal'] !== $nombre_norm . '.' . strtolower(pathinfo($prod['imagen_principal'], PATHINFO_EXTENSION))) {
                    $ext = strtolower(pathinfo($prod['imagen_principal'], PATHINFO_EXTENSION));
                    $destino = $nombre_norm . '.' . $ext;
                    $rutaActual = $directorio . $prod['imagen_principal'];
                    $rutaNueva = $directorio . $destino;
                    if (!file_exists($rutaNueva)) {
                        rename($rutaActual, $rutaNueva);
                        $stmt2 = $conex->prepare("UPDATE productos SET imagen_principal = :img WHERE id_producto = :id");
                        $stmt2->execute([":img" => $destino, ":id" => $prod['id_producto']]);
                        $resultados['renombradas']++;
                    }
                }
                $resultados['ya_tenian']++;
                continue;
            }

            $encontrada = false;
            foreach ($archivos as $archivo) {
                $nombre_archivo = strtolower(pathinfo($archivo, PATHINFO_FILENAME));
                $ext_archivo = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                $nombre_destino = $nombre_norm . '.' . $ext_archivo;

                if ($nombre_archivo === $nombre_norm || str_contains($nombre_archivo, $nombre_norm) || str_contains($nombre_norm, $nombre_archivo)) {
                    $rutaDestino = $directorio . $nombre_destino;
                    if (basename($archivo) !== $nombre_destino && !file_exists($rutaDestino)) {
                        rename($archivo, $rutaDestino);
                        $this->actualizarImagen($prod['id_producto'], $nombre_destino);
                    } else {
                        $this->actualizarImagen($prod['id_producto'], basename($archivo));
                    }
                    $resultados['asociadas']++;
                    $encontrada = true;
                    break;
                }
            }

            if (!$encontrada) {
                $resultados['sin_match'][] = $prod['nombre_producto'];
            }
        }

        return $resultados;
    }
}