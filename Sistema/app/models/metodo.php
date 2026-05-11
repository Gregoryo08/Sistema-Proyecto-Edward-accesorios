<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;

use \PDOException;

class metodo extends Conexion
{
    private $id_metodopago;
    private $nombre_metodopago;
    private $cuenta;
    private $mensaje;

    public function __construct()
    {
        parent::__construct();
    }

    public function existeNombreMetodoPago($nombre, $id_excluir = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM metodo_pago WHERE nombre_metodopago = :nombre";
            if ($id_excluir !== null) {
                $sql .= " AND id_metodopago != :id_excluir";
            }
            $query = $this->prepare($sql);
            $query->bindParam(":nombre", $nombre);
            if ($id_excluir !== null) {
                $query->bindParam(":id_excluir", $id_excluir, PDO::PARAM_INT);
            }
            $query->execute();
            return $query->fetchColumn() > 0;
        } catch (PDOException $e) {
            $this->setMensaje("Error al verificar nombre: " . $e->getMessage());
            return true;
        }
    }

    public function listar()
    {
        try {
            $stmt = $this->prepare("SELECT * FROM metodo_pago ORDER BY nombre_metodopago ASC");
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->setMensaje("Error al obtener métodos de pago: " . $e->getMessage());
            return [];
        }
    }

    public function consultar()
    {
        $id = $this->getIdMetodoPago();

        if (empty($id)) {
            return ["incompleto" => "Dato ingresado esta vacio!"];
        }
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "Identificador del registro no es valido!"];
        }

        try {
            $stmt = $this->prepare("SELECT * FROM metodo_pago WHERE id_metodopago = :id");
            if (!$stmt->execute([":id" => $id])) {
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $datos = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$datos) {
                return ["invalido" => "Método de pago no encontrado."];
            }

            return $datos;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    public function registrar()
    {
        if ($this->getNombreMetodoPago() === null || $this->getMensaje() !== null) {
            if ($this->getNombreMetodoPago() === null && $this->getMensaje() === null) {
                $this->setMensaje("El nombre del método de pago no ha sido establecido.");
            }
            return ["incompleto" => $this->getMensaje()];
        }

        $nombre = $this->getNombreMetodoPago();
        $cuenta = $this->getCuenta();

        if ($this->existeNombreMetodoPago($nombre)) {
            $this->setMensaje("El nombre del método de pago ya existe.");
            return ["invalido" => $this->getMensaje()];
        }

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? 'sistema_desconocido';
            $modulo = "Administrar Metodos de Pago";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

            $query = $this->prepare("INSERT INTO metodo_pago(nombre_metodopago, cuenta) VALUES (:nombre_metodopago, :cuenta)");
            $query->bindParam(":nombre_metodopago", $nombre);
            $query->bindParam(":cuenta", $cuenta);

            if (!$query->execute()) {
                $this->rollBack();
                $errorInfo = $query->errorInfo();
                $this->setMensaje("Error con el servidor: " . $errorInfo[2]);
                return ["error" => $this->getMensaje()];
            }

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->rollBack();
            $this->setMensaje("Error interno del servidor: " . $e->getMessage());
            return ["error" => $this->getMensaje()];
        }
    }

    public function modificar()
    {
        if ($this->getIdMetodoPago() === null || $this->getNombreMetodoPago() === null || $this->getMensaje() !== null) {
            if ($this->getIdMetodoPago() === null && $this->getMensaje() === null) {
                $this->setMensaje("El ID del método de pago no ha sido establecido.");
            } elseif ($this->getNombreMetodoPago() === null && $this->getMensaje() === null) {
                $this->setMensaje("El nombre del método de pago no ha sido establecido.");
            }
            return ["incompleto" => $this->getMensaje()];
        }

        $id = $this->getIdMetodoPago();
        $nombre = $this->getNombreMetodoPago();
        $cuenta = $this->getCuenta();

        if ($this->existeNombreMetodoPago($nombre, $id)) {
            $this->setMensaje("El nombre del método de pago ya existe.");
            return ["invalido" => $this->getMensaje()];
        }

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? 'sistema_desconocido';
            $modulo = "Administrar Metodos de Pago";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

            $query = $this->prepare("UPDATE metodo_pago SET nombre_metodopago = :nombre_metodopago, cuenta = :cuenta WHERE id_metodopago = :id_metodopago");
            $query->bindParam(":nombre_metodopago", $nombre);
            $query->bindParam(":cuenta", $cuenta);
            $query->bindParam(":id_metodopago", $id, PDO::PARAM_INT);

            if (!$query->execute()) {
                $this->rollBack();
                $errorInfo = $query->errorInfo();
                $this->setMensaje("Error con el servidor: " . $errorInfo[2]);
                return ["error" => $this->getMensaje()];
            }

            if ($query->rowCount() == 0) {
                $stmt_check = $this->prepare("SELECT id_metodopago FROM metodo_pago WHERE id_metodopago = :id");
                $stmt_check->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt_check->execute();
                if ($stmt_check->rowCount() == 0) {
                    $this->setMensaje("No se encontró el método de pago con el ID proporcionado.");
                    return ["error" => $this->getMensaje()];
                }
            }

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->rollBack();
            $this->setMensaje("Error interno del servidor: " . $e->getMessage());
            return ["error" => $this->getMensaje()];
        }
    }

    public function eliminar()
    {
        $id = $this->getIdMetodoPago();

        if (empty($id)) {
            return ["incompleto" => "Dato ingresado esta vacio!"];
        }
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "Identificador del registro no es valido!"];
        }

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"] ?? 'sistema_desconocido';
            $modulo = "Administrar Metodos de Pago";
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

            $query = $this->prepare("DELETE FROM metodo_pago WHERE id_metodopago = :id_metodopago");
            $query->bindParam(":id_metodopago", $id, PDO::PARAM_INT);

            if (!$query->execute()) {
                $this->rollBack();
                $errorInfo = $query->errorInfo();
                $this->setMensaje("Error con el servidor: " . $errorInfo[2]);
                return ["error" => $this->getMensaje()];
            }

            if ($query->rowCount() == 0) {
                $this->rollBack();
                $this->setMensaje("No se encontró el método de pago con el ID: $id para eliminar.");
                return ["invalido" => $this->getMensaje()];
            }

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->rollBack();
            $this->setMensaje("Error interno del servidor: " . $e->getMessage());
            return ["error" => $this->getMensaje()];
        }
    }

    public function getIdMetodoPago()
    {
        return $this->id_metodopago;
    }

    public function setIdMetodoPago($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1))) === false) {
            $this->setMensaje("ID de método de pago inválido.");
            $this->id_metodopago = null;
            return false;
        }
        $this->id_metodopago = $id;
        $this->setMensaje(null);
        return true;
    }

    public function getNombreMetodoPago()
    {
        return $this->nombre_metodopago;
    }

    public function setNombreMetodoPago($nombre_metodopago)
    {
        $nombre_limpio = trim($nombre_metodopago);
        $nombre_limpio = htmlspecialchars($nombre_limpio, ENT_QUOTES, 'UTF-8');

        if (empty($nombre_limpio)) {
            $this->setMensaje("El nombre del método de pago no puede estar vacío.");
            $this->nombre_metodopago = null;
            return false;
        }
        if (strlen($nombre_limpio) > 50) {
            $this->setMensaje("El nombre del método de pago es demasiado largo.");
            $this->nombre_metodopago = null;
            return false;
        }
        if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s-]+$/u", $nombre_limpio)) {
            $this->setMensaje("El nombre del método de pago contiene caracteres no permitidos.");
            $this->nombre_metodopago = null;
            return false;
        }

        $this->nombre_metodopago = $nombre_limpio;
        $this->setMensaje(null);
        return true;
    }

    public function getCuenta()
    {
        return $this->cuenta;
    }
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }

    public function getMensaje()
    {
        return $this->mensaje;
    }

    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    }
}