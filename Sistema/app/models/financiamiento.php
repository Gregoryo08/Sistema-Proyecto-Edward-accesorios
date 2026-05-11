<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class financiamiento extends Conexion
{
    private $id_financiamiento;
    private $cedula_cliente;
    private $id_telefono;
    private $monto_total;
    private $pago_inicial;
    private $cantidad_cuotas;
    private $monto_cuota;
    private $dia_pago;
    private $fecha_inicio;

    public function __construct() { parent::__construct(); }

   public function actualizarSaldosYMoras()
{
    try {
        $conex = $this; 

       
        $sqlMora = "UPDATE financiamientos 
                    SET estado_equipo = 'bloqueado' 
                    WHERE estatus_financiamiento = 'vigente' 
                    AND id_financiamiento IN (
                        SELECT id_financiamiento FROM cuotas 
                        WHERE estado_cuota = 'pendiente' 
                        AND fecha_vencimiento < CURDATE()
                    )";
        $conex->exec($sqlMora);

        $sql = "SELECT 
                    f.*, 
                    c.nombre, 
                    c.apellido, 
                    t.modelo, 
                    t.imei,
                    (SELECT COUNT(*) FROM cuotas 
                     WHERE id_financiamiento = f.id_financiamiento 
                     AND estado_cuota = 'pagado') as pagadas,
                    (f.monto_total - f.pago_inicial - (SELECT IFNULL(SUM(monto_pagado), 0) 
                     FROM cuotas WHERE id_financiamiento = f.id_financiamiento 
                     AND estado_cuota = 'pagado')) as saldo_pendiente,
                    (SELECT fecha_vencimiento FROM cuotas 
                     WHERE id_financiamiento = f.id_financiamiento 
                     AND estado_cuota = 'pendiente' 
                     ORDER BY fecha_vencimiento ASC LIMIT 1) as proximo_vencimiento,
                    DATEDIFF(
                        (SELECT fecha_vencimiento FROM cuotas 
                         WHERE id_financiamiento = f.id_financiamiento 
                         AND estado_cuota = 'pendiente' 
                         ORDER BY fecha_vencimiento ASC LIMIT 1), 
                        CURDATE()
                    ) as dias_restantes
                FROM financiamientos f
                INNER JOIN clientes c ON f.cedula_cliente = c.cedula_cliente
                INNER JOIN telefonos t ON f.id_telefono = t.id_telefono
                ORDER BY f.id_financiamiento DESC";
        
        return $conex->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) { 
        return ["error" => $e->getMessage()]; 
    }
}

    public function registrar()
    {
        $cedula = $this->getCedula_cliente();
        $telefono = $this->getId_telefono();
        $total = $this->getMonto_total();
        $inicial = $this->getPago_inicial();
        $cant_cuotas = $this->getCantidad_cuotas();
        $dia = $this->getDia_pago();
        $inicio = $this->getFecha_inicio();

        if (empty($cedula) || empty($telefono) || empty($total) || empty($cant_cuotas) || empty($dia) || empty($inicio)) {
            return ["incompleto" => "Datos Incompletos"];
        }

        $monto_cuota = ($total - $inicial) / $cant_cuotas;

        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();

            $user = $_SESSION["username"];
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = 'Administrar Financiamiento'");

            $stmt = $conex->prepare("INSERT INTO financiamientos(cedula_cliente, id_telefono, monto_total, pago_inicial, cantidad_cuotas, monto_cuota, dia_pago, fecha_inicio) VALUES (:ce, :te, :to, :pi, :cc, :mc, :dp, :fi)");

            if ($stmt->execute([":ce"=>$cedula,":te"=>$telefono,":to"=>$total,":pi"=>$inicial,":cc"=>$cant_cuotas,":mc"=>$monto_cuota,":dp"=>$dia,":fi"=>$inicio])) {
                $id_finan = $conex->lastInsertId();
                for ($i = 1; $i <= $cant_cuotas; $i++) {
                    $f_venc = date('Y-m-d', strtotime($inicio . " + $i month"));
                    $f_venc = date('Y-m-', strtotime($f_venc)) . str_pad($dia, 2, '0', STR_PAD_LEFT);
                    $conex->prepare("INSERT INTO cuotas(id_financiamiento, numero_cuota, fecha_vencimiento, estado_cuota) VALUES (?, ?, ?, 'pendiente')")->execute([$id_finan, $i, $f_venc]);
                }
                $conex->commit();
                return ["success" => true];
            }
            $conex->rollBack();
            return ["error" => "Error al registrar"];
        } catch (PDOException $e) { if(isset($conex))$conex->rollBack(); return ["error" => $e->getMessage()]; }
    }

    public function registrarPago($id_cuota, $monto, $id_metodo)
    {
        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();
            $user = $_SESSION["username"];
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = 'Administrar Financiamiento'");

            $stmt = $conex->prepare("UPDATE cuotas SET monto_pagado = ?, fecha_pago_realizado = NOW(), estado_cuota = 'pagado', id_metodopago = ? WHERE id_cuota = ?");
            if ($stmt->execute([$monto, $id_metodo, $id_cuota])) {
                $stmt_info = $conex->prepare("SELECT id_financiamiento FROM cuotas WHERE id_cuota = ?");
                $stmt_info->execute([$id_cuota]);
                $id_finan = $stmt_info->fetch(PDO::FETCH_ASSOC)['id_financiamiento'];

                $stmt_check = $conex->prepare("SELECT COUNT(*) as pendientes FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pendiente'");
                $stmt_check->execute([$id_finan]);
                if ($stmt_check->fetch(PDO::FETCH_ASSOC)['pendientes'] == 0) {
                    $conex->prepare("UPDATE financiamientos SET estatus_financiamiento = 'finalizado', estado_equipo = 'activo' WHERE id_financiamiento = ?")->execute([$id_finan]);
                }
                $conex->commit();
                return ["success" => true];
            }
            $conex->rollBack();
            return ["error" => "Error al procesar pago"];
        } catch (PDOException $e) { if(isset($conex))$conex->rollBack(); return ["error" => $e->getMessage()]; }
    }

    public function cambiarEstadoEquipo($nuevo_estado)
    {
        try {
            $conex = new conexion("sistema");
            $user = $_SESSION["username"];
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = 'Administrar Financiamiento'");

            $stmt = $conex->prepare("UPDATE financiamientos SET estado_equipo = ? WHERE id_financiamiento = ?");
            return $stmt->execute([$nuevo_estado, $this->getId_financiamiento()]);
        } catch (PDOException $e) { 
            return false; 
        }
    }
    
    public function anularFinanciamiento()
{
    try {
        $this->beginTransaction();
        
      
        $stmt = $this->prepare("SELECT COUNT(*) FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pagado'");
        $stmt->execute([$this->getId_financiamiento()]);
        if ($stmt->fetchColumn() > 0) {
            return ["error" => "No se puede anular un financiamiento que ya tiene pagos registrados."];
        }

        
        $stmt = $this->prepare("UPDATE financiamientos SET estatus_financiamiento = 'anulado', estado_equipo = 'activo' WHERE id_financiamiento = ?");
        $stmt->execute([$this->getId_financiamiento()]);

       
        $this->prepare("DELETE FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pendiente'")->execute([$this->getId_financiamiento()]);

        $this->commit();
        return ["success" => true];
    } catch (PDOException $e) {
        if($this->inTransaction()) $this->rollBack();
        return ["error" => $e->getMessage()];
    }
}

    public function finalizarContratoManualmente()
    {
        try {
            $conex = new conexion("sistema");
            $conex->beginTransaction();
            
            $user = $_SESSION["username"];
            $conex->exec("SET @usuario_actual = '{$user}'");
            $conex->exec("SET @modulo = 'Administrar Financiamiento'");

            $stmt = $conex->prepare("UPDATE financiamientos SET estatus_financiamiento = 'finalizado', estado_equipo = 'activo' WHERE id_financiamiento = ?");
            
            if ($stmt->execute([$this->getId_financiamiento()])) {
                $conex->commit();
                return true;
            }
            $conex->rollBack();
            return false;
        } catch (PDOException $e) { 
            if(isset($conex)) $conex->rollBack();
            return false; 
        }
    }

    public function listarMetodos() { 
        $res = $this->query("SELECT id_metodopago, nombre_metodopago FROM metodo_pago WHERE estatus = 1")->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : []; 
    }
    public function listarBancos() { return $this->query("SELECT id_banco, nombre_banco FROM bancos WHERE estatus = 'activo'")->fetchAll(PDO::FETCH_ASSOC); }
    public function listarClientes() { return $this->query("SELECT cedula_cliente, nombre, apellido FROM clientes WHERE estado = 'activo' ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC); }
public function listarTelefonosDisponibles() { 
   
    $sql = "SELECT id_telefono, modelo, imei 
            FROM telefonos 
            WHERE id_telefono NOT IN (
                SELECT id_telefono 
                FROM financiamientos 
                WHERE estatus_financiamiento IN ('vigente', 'finalizado')
            )";
    return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC); 
}


    public function consultarCuotas() { 
    $stmt = $this->prepare("SELECT 
            c.*, 
            m.nombre_metodopago, 
            b.nombre_banco 
        FROM cuotas c 
        LEFT JOIN metodo_pago m ON c.id_metodopago = m.id_metodopago 
        LEFT JOIN bancos b ON c.id_banco = b.id_banco 
        WHERE c.id_financiamiento = ? 
        ORDER BY c.numero_cuota ASC"); 
    $stmt->execute([$this->getId_financiamiento()]); 
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

    public function getId_financiamiento() { return $this->id_financiamiento; }
    public function setId_financiamiento($id) { $this->id_financiamiento = $id; }
    public function getCedula_cliente() { return $this->cedula_cliente; }
    public function setCedula_cliente($cedula) { $this->cedula_cliente = $cedula; }
    public function getId_telefono() { return $this->id_telefono; }
    public function setId_telefono($id) { $this->id_telefono = $id; }
    public function getMonto_total() { return $this->monto_total; }
    public function setMonto_total($monto) { $this->monto_total = $monto; }
    public function getPago_inicial() { return $this->pago_inicial; }
    public function setPago_inicial($pago) { $this->pago_inicial = $pago; }
    public function getCantidad_cuotas() { return $this->cantidad_cuotas; }
    public function setCantidad_cuotas($cant) { $this->cantidad_cuotas = $cant; }
    public function getDia_pago() { return $this->dia_pago; }
    public function setDia_pago($dia) { $this->dia_pago = $dia; }
    public function getFecha_inicio() { return $this->fecha_inicio; }
    public function setFecha_inicio($fecha) { $this->fecha_inicio = $fecha; }
}