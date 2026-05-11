<?php
namespace App\Sistema\models; 

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class pagos extends conexion
{
    private $id_pago;
    private $id_metodopago;
    private $cedula_cliente;
    private $monto;
    private $tipo;
    private $fecha;
    private $hora;
    private $referencia;
    private $cantidad;

    public function __construct()
    {
        parent::__construct();
    }

    public function consultarTazas()
    {
        $conex = new conexion("sistema");

        $stmt = $conex->prepare("SELECT * FROM taza_moneda WHERE monitor = 'usd'");
        $stmt->execute();

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conex);
        return $resultado;
    }

    public function registrar($sql, $array_parametros, $conex)
    {
        try {

            $stmt = $conex->prepare($sql);

            if (!($stmt->execute($array_parametros))) {
                return false;
            }

            $id_pago = $conex->lastInsertId();

            return [true, $id_pago];
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function consultar($sql, $parametros, $conex)
    {
        $stmt = $conex->prepare($sql);

        if(!($stmt->execute($parametros))){
            return false;
        }

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function validarPago()
   
    {
        $conex = new conexion("sistema");
        $fecha = $this->getFecha();
        $cedula = $this->getCedula_cliente();

        $stmt = $conex->prepare("SELECT COUNT(*) as cont FROM pagos WHERE fecha_pago = :f and cedula_cliente = :c");
        $stmt->bindParam(":f", $fecha);
        $stmt->bindParam(":c", $cedula);

        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado["cont"];
    }

    public function getId_Pago()
    {
        return $this->id_pago;
    }
    public function setId_Pago($id)
    {
        $this->id_pago = $id;
    }

    public function getIdMetodoPago()
    {
        return $this->id_metodopago;
    }
    public function setIdMetodoPago($id_metodopago)
    {
        $this->id_metodopago = $id_metodopago;
    }

    public function getCedula_cliente()
    {
        return $this->cedula_cliente;
    }
    public function setCedula_cliente($cedula)
    {
        $this->cedula_cliente = $cedula;
    }

    public function getMonto()
    {
        return $this->monto;
    }
    public function setMonto($monto)
    {
        $this->monto = $monto;
    }

    public function getTipo()
    {
        return $this->tipo;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    public function getFecha()
    {
        return $this->fecha;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getHora()
    {
        return $this->hora;
    }
    public function setHora($hora)
    {
        $this->hora = $hora;
    }

    public function getReferencia()
    {
        return $this->referencia;
    }
    public function setReferencia($referencia)
    {
        $this->referencia = $referencia;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
}

?>