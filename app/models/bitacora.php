<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use PDO;
use Exception;

class Bitacora extends Conexion
{
    private $AccionUsuario;
    private $Usuario;

    public function __construct()
    {
        parent::__construct();
    }

    public function registrar($tabla, $accion, $modulo, $usuario_id, $id_modulo, array $detalles = [])
    {
        try {
            $pdo = $this->getConexion();
            $pdo->beginTransaction();

            $sql = "INSERT INTO bitacora (tabla_afectada, accion, modulo, usuario_id, id_modulo, fecha_registro) 
                    VALUES (:tabla, :accion, :modulo, :usuario_id, :id_modulo, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":tabla" => $tabla,
                ":accion" => $accion,
                ":modulo" => $modulo,
                ":usuario_id" => $usuario_id,
                ":id_modulo" => $id_modulo
            ]);

            $id_bitacora = $pdo->lastInsertId();

            if (!empty($detalles)) {
                $sqlDetalle = "INSERT INTO bitacora_detalles (id_bitacora, campo_afectado, valor_antiguo, valor_nuevo) 
                               VALUES (:id_bitacora, :campo, :antiguo, :nuevo)";
                $stmtDetalle = $pdo->prepare($sqlDetalle);

                foreach ($detalles as $detalle) {
                    $stmtDetalle->execute([
                        ":id_bitacora" => $id_bitacora,
                        ":campo" => $detalle['campo'],
                        ":antiguo" => $detalle['antiguo'],
                        ":nuevo" => $detalle['nuevo']
                    ]);
                }
            }

            $pdo->commit();
        } catch (Exception $e) {
            if (isset($pdo)) $pdo->rollBack();
            error_log("Error al registrar bitácora: " . $e->getMessage());
        }
    }

public function Consultar_Movimientos($usuario = null, $accion = null)
    {
        $pdo = $this->getConexion();
        $pdo->exec("SET time_zone = '-04:00'");

        $sql = "SELECT 
                    b.id,
                    COALESCE(r.descripcion_rol, 'N/A') AS rol, 
                    COALESCE(
                        CONCAT(u.cedula_usuario, ' - ', p.nombre, ' ', p.apellido), 
                        IF(u.cedula_usuario = 'administrador', 'Administrador', 'Superusuario')
                    ) AS usuario_info,
                    b.accion, 
                    b.modulo,
                    DATE_FORMAT(b.fecha_registro, '%d/%m/%Y %h:%i:%p') AS fecha_registro,
                    GROUP_CONCAT(CONCAT(bd.campo_afectado, ': ', bd.valor_antiguo) SEPARATOR '\n') AS valor_antiguo,
                    GROUP_CONCAT(CONCAT(bd.campo_afectado, ': ', bd.valor_nuevo) SEPARATOR '\n') AS valor_nuevo
                FROM sistema_edward_usuario.bitacora b 
                LEFT JOIN sistema_edward_usuario.bitacora_detalles bd ON b.id = bd.id_bitacora
                LEFT JOIN sistema_edward_usuario.usuarios u ON b.usuario_id COLLATE utf8mb4_unicode_ci = u.cedula_usuario COLLATE utf8mb4_unicode_ci
                LEFT JOIN sistema_edward.persona p ON u.cedula_usuario COLLATE utf8mb4_unicode_ci = p.cedula_persona COLLATE utf8mb4_unicode_ci
                LEFT JOIN sistema_edward_usuario.roles r ON u.id_rol = r.idRol
                WHERE 1=1";

        $params = [];

        if (!empty($usuario)) {
            $sql .= " AND b.usuario_id COLLATE utf8mb4_unicode_ci = :usuario COLLATE utf8mb4_unicode_ci";
            $params[':usuario'] = $usuario;
        }

        if (!empty($accion)) {
            $sql .= " AND b.accion COLLATE utf8mb4_unicode_ci = :accion COLLATE utf8mb4_unicode_ci";
            $params[':accion'] = $accion;
        }

        $sql .= " GROUP BY b.id, r.descripcion_rol, u.cedula_usuario, p.nombre, p.apellido, b.accion, b.modulo, b.fecha_registro";
        $sql .= " ORDER BY b.fecha_registro DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerUsuarios()
    {
        try {
            return $this->getConexion()->query("SELECT cedula_usuario FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAccion_Usuario() { return $this->AccionUsuario; }
    public function setAccion_Usuario($AccionUsuario) { $this->AccionUsuario = $AccionUsuario; }
    public function getUsuario() { return $this->Usuario; }
    public function setUsuario($Usuario) { $this->Usuario = $Usuario; }
}