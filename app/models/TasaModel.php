<?php
namespace App\Sistema\Models;

use App\Sistema\config\Conexion;
use \PDO;
use \Throwable;

class TasaModel {
    private $monto;

    public function validarClaveAdministrador($clave) {
        try {
            $conexUser = new Conexion("usuario");
            $stmt = $conexUser->prepare("SELECT clave, id_rol, estatus FROM usuarios WHERE id_rol = 3");
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($usuarios as $usuario) {
                if (isset($usuario['estatus']) && $usuario['estatus'] === 'Inactivo') {
                    continue;
                }

                if (password_verify($clave, $usuario['clave']) || $clave === $usuario['clave']) {
                    return true;
                }
            }

            return false;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function registrarNotificacionTasaNoDisponible() {
        try {
            $notif = new \App\Sistema\models\notificacion();
            $notif->setMensaje("El precio del BCV no se encuentra disponible. Un administrador debe ingresar la tasa manualmente.");
            $notif->setTipo("alerta");
            $notif->setCedula_usuario($_SESSION['username'] ?? 'Sistema');
            
            return $notif->registrar();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function registrarNotificacionTasaAntigua() {
        try {
            $notif = new \App\Sistema\models\notificacion();
            $notif->setMensaje("La tasa actual del BCV no corresponde al precio de hoy. Un administrador debe actualizarla.");
            $notif->setTipo("alerta");
            $notif->setCedula_usuario($_SESSION['username'] ?? 'Sistema');
            
            return $notif->registrar();
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function cambiarTasaManual($monto, $cedulaAdmin, $claveAdmin, $fuente = 'BCV') {
        if (!is_numeric($monto) || $monto <= 0) {
            return ["invalido" => "El monto de la tasa debe ser un número válido mayor a cero."];
        }

        try {
            $conexSistema = new Conexion("sistema");
            $conexSistema->beginTransaction();

            $user = $_SESSION["username"] ?? $cedulaAdmin;
            $modulo = "Administrar Tasa";

            $conexSistema->exec("SET @usuario_actual = '{$user}'");
            $conexSistema->exec("SET @modulo = '{$modulo}'");

            $stmt = $conexSistema->prepare("INSERT INTO tasa_cambio (tasa, fuente) VALUES (:monto, :fuente)");
            $stmt->bindParam(":monto", $monto);
            $stmt->bindParam(":fuente", $fuente);

            if (!$stmt->execute()) {
                $conexSistema->rollBack();
                return ["error" => "Error al registrar la tasa."];
            }

            $conexSistema->commit();
            unset($conexSistema);
            return true;
        } catch (Throwable $th) {
            if (isset($conexSistema)) {
                $conexSistema->rollBack();
            }
            return ["error" => "Error en el servidor al actualizar la tasa."];
        }
    }

    public function obtenerTasaActual() {
        try {
            $conexSistema = new Conexion("sistema");
            $stmt = $conexSistema->prepare("SELECT id, tasa, fecha_actualizacion, fuente FROM tasa_cambio ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            unset($conexSistema);
            return $resultado;
        } catch (Throwable $th) {
            return false;
        }
    }

    public function getMonto() { return $this->monto; }
    public function setMonto($monto) { $this->monto = $monto; }
}