<?php

namespace App\Sistema\config;

use PDO;
use Exception;

class Conexion extends PDO
{
    private static $compartidas = [];

    private $repConexion = false;
    private $errorConexion = "";

    public static function getShared($db = "sistema")
    {
        $key = ($db === "usuario") ? "usuario" : "sistema";
        if (!isset(self::$compartidas[$key])) {
            self::$compartidas[$key] = new Conexion($db);
        }
        return self::$compartidas[$key];
    }

    public function __construct($db = "sistema")
    {

    
        if ($db == "usuario") {
            $this->conexionUsuario();
        } else {
            $this->conexionSistema();
        }
        
       
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function conexionUsuario()
    {
        try {
            $datos = require __DIR__ . '/data.php';

            parent::__construct("mysql:host={$datos["host"]}; dbname={$datos["dbname2"]}; charset=utf8mb4", $datos["username"], $datos["password"]);

            $this->repConexion = true;
        } catch (Exception $e) {
            // Posible entorno cambiado (docker<->xampp): invalidar caché de host y reintentar una vez
            $this->repConexion = $this->reintentarConHostRefrescado('usuario', $e);
            if (!$this->repConexion) {
                $this->errorConexion = "Error de conexión a DB 'usuario': " . $e->getMessage();
                throw new Exception($this->errorConexion);
            }
        }
    }

    public function conexionSistema()
    {
        try {
            $datos = require __DIR__ . '/data.php';

            parent::__construct("mysql:host={$datos["host"]}; dbname={$datos["dbname1"]}; charset=utf8mb4", $datos["username"], $datos["password"]);
            
            $this->repConexion = true;
        } catch (Exception $e) {
            // Posible entorno cambiado (docker<->xampp): invalidar caché de host y reintentar una vez
            $this->repConexion = $this->reintentarConHostRefrescado('sistema', $e);
            if (!$this->repConexion) {
                $this->errorConexion = "Error de conexión a DB 'sistema': " . $e->getMessage();
                throw new Exception($this->errorConexion);
            }
        }
    }

    /**
     * Si la conexión falla con el host cacheado, invalida el caché de detección
     * de Docker (data.php) y reintenta una sola vez con el host re-resuelto.
     * Devuelve true si el reintento fue exitoso.
     */
    private function reintentarConHostRefrescado($tipoDb, Exception $e)
    {
        $cacheFile = __DIR__ . '/db_host.cache';
        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }
        unset($GLOBALS['__DB_IS_DOCKER__']);
        try {
            $datos = require __DIR__ . '/data.php';
            $dbname = ($tipoDb === 'usuario') ? $datos["dbname2"] : $datos["dbname1"];
            parent::__construct("mysql:host={$datos["host"]}; dbname={$dbname}; charset=utf8mb4", $datos["username"], $datos["password"]);
            return true;
        } catch (Exception $e2) {
            error_log("Conexion::reintentarConHostRefrescado -> " . $e2->getMessage());
            return false;
        }
    }

    
    public function getConexion()
    {
        return $this;
    }

    public function getRepConexion()
    {
        return $this->repConexion;
    }

    public function getErrorConexion()
    {
        return $this->errorConexion;
    }
}
