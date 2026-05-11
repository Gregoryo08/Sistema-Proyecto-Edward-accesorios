<?php

namespace App\Sistema\config;

use PDO;
use Exception;

class Conexion extends PDO
{
    private $repConexion = false;
    private $errorConexion = "";

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
            $this->errorConexion = "Error de conexión a DB 'usuario': " . $e->getMessage();
            throw new Exception($this->errorConexion); 
        }
    }

    public function conexionSistema()
    {
        try {
            $datos = require __DIR__ . '/data.php';

            parent::__construct("mysql:host={$datos["host"]}; dbname={$datos["dbname1"]}; charset=utf8mb4", $datos["username"], $datos["password"]);
            
            $this->repConexion = true;
        } catch (Exception $e) {
            $this->errorConexion = "Error de conexión a DB 'sistema': " . $e->getMessage();
            throw new Exception($this->errorConexion); 
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