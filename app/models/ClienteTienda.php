<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \Throwable;

class ClienteTienda
{
    private $cedula;

    public function __construct($cedula)
    {
        $this->cedula = trim((string) $cedula);
    }

    public function tieneRegistroEcommerce()
    {
        return $this->existe(
            "SELECT COUNT(*) FROM clientes WHERE cedula_persona = ?"
        );
    }

    public function tieneFinanciamientos()
    {
        return $this->existe(
            "SELECT COUNT(*) FROM financiamientos WHERE cedula_persona = ?"
        );
    }

    private function existe($sql)
    {
        if ($this->cedula === '') {
            return false;
        }

        try {
            $conex = Conexion::getShared('sistema')->getConexion();
            $stmt = $conex->prepare($sql);
            $stmt->execute([$this->cedula]);

            return ((int) $stmt->fetchColumn()) > 0;
        } catch (Throwable $th) {
            return false;
        }
    }
}
