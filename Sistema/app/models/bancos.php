<?php


namespace App\Sistema\models; 

use App\Sistema\models\pagos;
use App\Sistema\config\Conexion;
use \PDO;

use \PDOException;

class bancos extends pagos
{
    private $nombre_banco;
    private $numero_cuenta;
    private $telefono;
    private $cedula_banco;
    private $id_banco;

   public function __construct()
    {
        parent::__construct();
    }

    public function registrar_banco()
    {
        $nombre = $this->getNombre_banco();
        $numero = $this->getNumero_cuenta();
        $cedula = $this->getCedula_cuenta();
        $telefono = $this->getTelefono();

        if (empty($nombre) || empty($numero) || empty($cedula) || empty($telefono)) {
            $input = "";

            if(empty($nombre)){
                $input += "nombre-";
            }
            if(empty($numero)){
                $input += "numero-";
            }
            if(empty($cedula)){
                $input += "cedula-";
            }
            if(empty($telefono)){
                $input += "telefono-";
            }

            return ["incompleto" => "Datos Incompletos", "input" => $input];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s-]+$/', $nombre))) {
            return ["invalido" => "Formato del Nombre No Valido!", "input" => "nombre"];
        }
        if (!(is_int($cedula) && preg_match('/^[0-9]*$/', $cedula))) {
            return ["invalido" => "Formato de la cedula / RIF: '$cedula' No Valido!", "input" => "cedula"];
        }
        if (!(preg_match('/^[0-9]*$/', $numero))) {
            return ["invalido" => "Formato del numero de cuenta: '$numero' No Valido!", "input" => "numero"];
        }
        if (!(preg_match('/^[0-9]*$/', $telefono))) {
            return ["invalido" => "Formato del numero de telefono: '$telefono' No Valido!", "input" => "telefono"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Bancos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("INSERT INTO bancos (nombre_banco, numero_cuenta, cedula_banco, telefono) VALUES (:n, :nc, :c, :t)");

            if (!($stmt->execute([
                ":n" => $nombre,
                ":nc" => $numero,
                ":c" => $cedula,
                ":t" => $telefono
            ]))) {

                $conex->rollBack();
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function listar()
    {
        $conex = new conexion("sistema");
        
        $stmt = $conex->query("SELECT * FROM bancos");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultados;
    }

    public function listar_activos()
    {
        $conex = new conexion("sistema");
        
        $stmt = $conex->query("SELECT * FROM bancos WHERE estatus = 'activo'");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultados;
    }

    public function consultarBanco()
    {
        $id = $this->getCedula_cuenta();

        if(empty($id)){
            return ["incompleto" => "El identificador del registro esta vacio!"];
        }

        if(!(is_int($id) && preg_match('/^[0-9]*$/',$id))){
            return ["invalido" => "El identificador del registro mo es valido!"];
        }

        $conex = new conexion("sistema");
        
        $stmt = $conex->prepare("SELECT * FROM bancos WHERE id_banco = :id");

        if(!($stmt->execute([
            ":id" => $id
        ]))){
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado;
    }

    public function validar()
    {
        $cedula = $this->getCedula_cuenta();
        $nombre = $this->getNombre_banco();

        $conex = new conexion("sistema");
        
        $stmt = $conex->prepare("SELECT COUNT(*) as conteo FROM bancos WHERE cedula_banco = :id AND nombre_banco LIKE :n");

        if(!($stmt->execute([
            ":id" => $cedula,
            ":n" => "%".$nombre."%"
        ]))){
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        unset($conex);
        return $resultado["conteo"];
    }

    public function consultarDisponibles()
    {
        $conex = new conexion("sistema");
        
        $stmt = $conex->query("SELECT * FROM servicio_adicional WHERE disponibilidad = 'Disponible'");
        $query = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($conex);
        return $query;
    }

    public function modificar()
    {
        $nombre = $this->getNombre_banco();
        $numero = $this->getNumero_cuenta();
        $cedula = $this->getCedula_cuenta();
        $telefono = $this->getTelefono();
        $id_banco = $this->getId_banco();

        if (empty($nombre) || empty($numero) || empty($cedula) || empty($telefono) || empty($id_banco)) {
            $input = "";

            if(empty($nombre)){
                $input += "nombre-";
            }
            if(empty($numero)){
                $input += "numero-";
            }
            if(empty($cedula)){
                $input += "cedula-";
            }
            if(empty($telefono)){
                $input += "telefono-";
            }
            if(empty($id_banco)){
                $input += "id_banco-";
            }

            return ["incompleto" => "Datos Incompletos", "input" => $input];
        }

        if (!(is_string($nombre) && preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s-]+$/', $nombre))) {
            return ["invalido" => "Formato del Nombre No Valido!", "input" => "nombre"];
        }
        if (!(is_int($cedula) && preg_match('/^[0-9]*$/', $cedula))) {
            return ["invalido" => "Formato de la cedula / RIF: '$cedula' No Valido!", "input" => "cedula"];
        }
        if (!(preg_match('/^[0-9]*$/', $numero))) {
            return ["invalido" => "Formato del numero de cuenta: '$numero' No Valido!", "input" => "numero"];
        }
        if (!(preg_match('/^[0-9]*$/', $telefono))) {
            return ["invalido" => "Formato del numero de telefono: '$telefono' No Valido!", "input" => "telefono"];
        }
        if (!(is_int($id_banco) && preg_match('/^[0-9]*$/', $id_banco))) {
            return ["invalido" => "El identificador del registro No es Valido!", "input" => "id_banco"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Bancos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->query("SET @modulo = '{$modulo}'");

            $stmt = $conex->prepare("UPDATE bancos SET nombre_banco = :n, numero_cuenta = :nc, cedula_banco = :c, telefono = :t WHERE id_banco = :id");

            if (!($stmt->execute([":n" => $nombre, ":nc" => $numero, ":c" => $cedula, ":t" => $telefono, ":id" => $id_banco]))) {
                $conex->rollBack();
                unset($this->conex);
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $conex->commit();
            unset($conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar($tipo)
    {
        $id_banco = $this->getId_banco();

        if (empty($id_banco) || !(is_int($id_banco))) {
            return ["error" => "Ah ocurrido un error con el servidor!"];
        }

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = "Administrar Bancos";
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = '{$modulo}'");

            if ($tipo == "deshabilitar") {
                $stmt = $conex->prepare("UPDATE bancos SET estatus = 'inactivo' WHERE id_banco = :id");
            } else {
                $stmt = $conex->prepare("UPDATE bancos SET estatus = 'activo' WHERE id_banco = :id");
            }

            if (!($stmt->execute([":id" => $id_banco]))) {
                $conex->rollBack();
                unset($conex);
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $conex->commit();
            unset($this->conex);
            return true;
        } catch (PDOException $e) {
            $conex->rollBack();
            unset($conex);
            return ["error" => $e->getMessage()];
        }
    }

    public function getNombre_banco()
    {
        return $this->nombre_banco;
    }
    public function setNombre_banco($nombre)
    {
        $this->nombre_banco = $nombre;
    }

    public function getNumero_cuenta()
    {
        return $this->numero_cuenta;
    }
    public function setNumero_cuenta($numero)
    {
        $this->numero_cuenta = $numero;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function getCedula_cuenta()
    {
        return $this->cedula_banco;
    }
    public function setCedula_cuenta($cedula_banco)
    {
        $this->cedula_banco = $cedula_banco;
    }

    public function getId_banco()
    {
        return $this->id_banco;
    }
    public function setId_banco($id)
    {
        $this->id_banco = $id;
    }
}

?>