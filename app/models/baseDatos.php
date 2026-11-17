<?php
namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;
use \mysqli;

class basedatos extends Conexion
{
    private $db_config;
    private $nombreArchivoGenerado;

    public function __construct()
    {
        parent::__construct();
        $this->db_config = require __DIR__ . '/../config/data.php';
    }

    public function realizarBackup()
    {
        try {
            $fecha = date('Y-m-d_H-i-s');
            $carpeta = __DIR__ . '/../../databases/Respaldos/';
            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0755, true);
            }

            $dbname = $this->db_config['dbname1'];
            $this->nombreArchivoGenerado = $dbname . '_' . $fecha . '.sql';
            $rutaCompleta = $carpeta . $this->nombreArchivoGenerado;

            $pdo = new PDO("mysql:host={$this->db_config['host']};dbname={$dbname};charset=utf8mb4", $this->db_config['username'], $this->db_config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SET FOREIGN_KEY_CHECKS=0;\n\n";
            $tablas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tablas as $tabla) {
                $sql .= "DROP TABLE IF EXISTS `$tabla`;\n";
                $create = $pdo->query("SHOW CREATE TABLE `$tabla`")->fetch(PDO::FETCH_ASSOC);
                $sql .= $create['Create Table'] . ";\n\n";

                $datos = $pdo->query("SELECT * FROM `$tabla`")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($datos as $fila) {
                    $valores = array_map(function($v) use ($pdo) {
                        return $v === null ? 'NULL' : $pdo->quote($v);
                    }, $fila);
                    $sql .= "INSERT INTO `$tabla` VALUES (" . implode(", ", $valores) . ");\n";
                }
                $sql .= "\n\n";
            }
            $sql .= "SET FOREIGN_KEY_CHECKS=1;";

            if (file_put_contents($rutaCompleta, $sql)) {
                return [
                    'resultado' => 'exito',
                    'mensaje' => 'Backup generado correctamente.',
                    'archivo' => $this->nombreArchivoGenerado
                ];
            }
            return ['resultado' => 'error', 'mensaje' => 'Error al escribir el archivo en el disco.'];
        } catch (\Exception $e) {
            return ['resultado' => 'error', 'mensaje' => $e->getMessage()];
        }
    }

    public function getNombreArchivoGenerado()
    {
        return $this->nombreArchivoGenerado;
    }

    public function restaurarBaseDatos($ruta_sql)
    {
        if (!file_exists($ruta_sql)) {
            return ["error" => "Archivo no encontrado."];
        }
        
        $contenido = file_get_contents($ruta_sql);
        
        if (strpos(trim($contenido), '<!DOCTYPE') !== false || strpos(trim($contenido), '<html') !== false) {
            return ["error" => "El archivo seleccionado no es un respaldo SQL válido."];
        }

        $mysqli = new mysqli($this->db_config['host'], $this->db_config['username'], $this->db_config['password'], $this->db_config['dbname1']);
        
        if ($mysqli->connect_error) {
            return ["error" => "Error de conexión: " . $mysqli->connect_error];
        }

        $mysqli->query("SET FOREIGN_KEY_CHECKS=0;");
        
        if ($mysqli->multi_query($contenido)) {
            do { 
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
        } else {
            $error = $mysqli->error;
            $mysqli->query("SET FOREIGN_KEY_CHECKS=1;");
            $mysqli->close();
            return ["error" => "Error en la consulta SQL: " . $error];
        }

        $mysqli->query("SET FOREIGN_KEY_CHECKS=1;");
        $mysqli->close();
        return ["success" => "Restauración completada."];
    }
}