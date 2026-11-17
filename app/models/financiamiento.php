<?php
namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \PDOException;

class financiamiento extends Conexion
{
    private $id_financiamiento;
    private $cedula_persona;
    private $id_productos;
    private $id_unidad;
    private $monto_total;
    private $pago_inicial;
    private $cantidad_cuotas;
    private $monto_cuota;
    private $dia_pago;
    private $fecha_inicio;

    public function __construct()
     { parent::__construct(); }


  public function procesarSolicitud($accion, $datos = []) 
{
    switch ($accion) {
        case 'registrar':
            return $this->registrar($datos['datosPerfil'] ?? null);
        case 'modificarCompleto':
            return $this->modificarCompleto($datos);
        case 'aprobarPago':
            return $this->gestionarEstadoPago($datos['id_cuota'], 'pagado');
        case 'negarPago':
            return $this->gestionarEstadoPago($datos['id_cuota'], 'pendiente');
        case 'cambiarEstadoEquipo':
            return $this->cambiarEstadoEquipo($datos['estado']);
        case 'anularFinanciamiento':
            return $this->anularFinanciamiento();
        case 'actualizarSaldosYMoras':
            return $this->actualizarSaldosYMorasPrivado();
        case 'consultarUno':
            return $this->consultarUno();
        case 'esFechaValida':
            return $this->esFechaValida($datos['fecha']);
        case 'verificarVencimientosProximos':
            return $this->verificarVencimientosProximos();
        case 'finalizarContratoManualmente':
            return $this->finalizarContratoManualmente();
        default:
            return ["error" => "Acción no reconocida"];
    }
}

private function actualizarSaldosYMorasPrivado()
{
    try {
        $this->beginTransaction();

        $sqlMoraPendientes = "SELECT df.id_financiamiento 
                              FROM cuotas c
                              JOIN detalles_financiamiento df ON c.id_financiamiento = df.id_financiamiento
                              WHERE c.estado_cuota = 'pendiente' 
                              AND c.fecha_vencimiento < CURDATE() 
                              AND df.estado_equipo != 'bloqueado'
                              GROUP BY df.id_financiamiento";
        
        $stmtPendientes = $this->query($sqlMoraPendientes);
        $afectados = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($afectados)) {
            $sqlMora = "UPDATE detalles_financiamiento df
                        JOIN financiamientos f ON df.id_financiamiento = f.id_financiamiento
                        SET df.estado_equipo = 'bloqueado' 
                        WHERE f.estado_financiamiento = 'vigente' 
                        AND df.id_financiamiento IN (
                            SELECT id_financiamiento FROM cuotas 
                            WHERE estado_cuota = 'pendiente' 
                            AND fecha_vencimiento < CURDATE()
                        )";
            $this->exec($sqlMora);

            foreach ($afectados as $reg) {
                $sqlInfo = "SELECT p.nombre, p.apellido, pr.nombre_producto 
                            FROM financiamientos f
                            JOIN persona p ON f.cedula_persona = p.cedula_persona
                            JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                            JOIN productos pr ON df.id_productos = pr.id_producto
                            WHERE f.id_financiamiento = :id";
                
                $stmt = $this->prepare($sqlInfo);
                $stmt->execute([':id' => $reg['id_financiamiento']]);
                $info = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($info) {
                    $nombreCompleto = $info['nombre'] . ' ' . $info['apellido'];
                    $producto = $info['nombre_producto'];
                    $notif = new \App\Sistema\models\notificacion();
                    $notif->setMensaje("Financiamiento #{$reg['id_financiamiento']} (Cliente: {$nombreCompleto}, Producto: {$producto}) bloqueado por morosidad.");
                    $notif->setTipo("alerta");
                    $notif->setCedula_usuario($_SESSION['username']);
                    $notif->registrar();
                }
            }
        }

        $this->commit();

        $sql = "SELECT f.*, p.nombre, p.apellido, df.id_productos, pr.nombre_producto, df.estado_equipo,
                       IFNULL(ut.imei, 'N/A') as imei, IFNULL(ut.almacenamiento, 'N/A') as almacenamiento,
                       IFNULL(ut.memoria_ram, 'N/A') as memoria_ram,
                       (SELECT COUNT(*) FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pagado') as pagadas,
                       (f.monto_total - f.pago_inicial - (SELECT IFNULL(SUM(monto_pagado), 0) FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pagado')) as saldo_pendiente,
                       (SELECT fecha_vencimiento FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pendiente' ORDER BY fecha_vencimiento ASC LIMIT 1) as proximo_vencimiento,
                       DATEDIFF((SELECT fecha_vencimiento FROM cuotas WHERE id_financiamiento = f.id_financiamiento AND estado_cuota = 'pendiente' ORDER BY fecha_vencimiento ASC LIMIT 1), CURDATE()) as dias_restantes
                FROM financiamientos f
                INNER JOIN persona p ON f.cedula_persona = p.cedula_persona
                INNER JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                INNER JOIN productos pr ON df.id_productos = pr.id_producto
                LEFT JOIN unidades_telefonos ut ON df.id_unidad = ut.id_unidad
                GROUP BY f.id_financiamiento
                ORDER BY f.id_financiamiento DESC";
        
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) { 
        if ($this->inTransaction()) {
            $this->rollBack();
        }
        return ["error" => "Error al actualizar estados: " . $e->getMessage()]; 
    }
}

private function gestionarEstadoPago($id_cuota, $nuevo_estado)
{
    try {
        $conex = new conexion("sistema");
        $conex->beginTransaction(); 

         $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");

        
        $stmtId = $conex->prepare("SELECT id_financiamiento FROM cuotas WHERE id_cuota = ?");
        $stmtId->execute([$id_cuota]);
        $id_finan = $stmtId->fetchColumn();

        $sql = "UPDATE cuotas SET estado_cuota = :estado, fecha_pago_realizado = NOW() WHERE id_cuota = :id";
        $stmt = $conex->prepare($sql);
        $res = $stmt->execute([':estado' => $nuevo_estado, ':id' => $id_cuota]);

        if ($res && $nuevo_estado === 'pagado') {
            $this->verificarCierreFinanciamiento($id_finan, $conex);
        }
        
        $conex->commit();
        return $res ? ["success" => true] : ["error" => "Error al actualizar"];
    } catch (PDOException $e) {
        if (isset($conex)) $conex->rollBack();
        return ["error" => $e->getMessage()];
    }
}

private function verificarCierreFinanciamiento($id_financiamiento, $conex)
{
    $check = $conex->prepare("SELECT COUNT(*) FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pendiente'");
    $check->execute([$id_financiamiento]);
    
    if ($check->fetchColumn() == 0) {
        $conex->prepare("UPDATE financiamientos SET estado_financiamiento = 'finalizado' WHERE id_financiamiento = ?")->execute([$id_financiamiento]);
        $conex->prepare("UPDATE detalles_financiamiento SET estado_equipo = 'activo' WHERE id_financiamiento = ?")->execute([$id_financiamiento]);
    }
}




private function consultarUno()
{
    if (empty($this->id_financiamiento)) {
        return ["error" => "ID de financiamiento no definido"];
    }

    try {
        $conex = new conexion("sistema");
        
        $sql = "SELECT f.*, p.nombre, p.apellido, df.id_productos, df.id_unidad 
                FROM financiamientos f
                JOIN persona p ON f.cedula_persona = p.cedula_persona
                JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                WHERE f.id_financiamiento = ?";
        
        $stmt = $conex->prepare($sql);
        $stmt->execute([$this->id_financiamiento]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ?: ["error" => "Financiamiento no encontrado"];
    } catch (PDOException $e) {
        return ["error" => $e->getMessage()];
    }
}

private function esFechaValida($fecha) 
{
    $fechaIngresada = strtotime($fecha);
    $hoy = strtotime(date('Y-m-d'));
    $unAnioDespues = strtotime('+1 year');

    
    return ($fechaIngresada !== false && $fechaIngresada >= $hoy && $fechaIngresada <= $unAnioDespues);
}




private function verificarVencimientosProximos()
{
    try {
        $conex = new conexion("sistema");
        
        $sql = "SELECT c.id_financiamiento, f.cedula_persona, p.nombre, p.apellido, pr.nombre_producto 
                FROM cuotas c
                JOIN financiamientos f ON c.id_financiamiento = f.id_financiamiento
                JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
                JOIN persona p ON f.cedula_persona = p.cedula_persona
                JOIN productos pr ON df.id_productos = pr.id_producto
                WHERE c.estado_cuota = 'pendiente' 
                AND DATEDIFF(c.fecha_vencimiento, CURDATE()) = 2";
        
        $stmt = $conex->query($sql);
        
        if ($stmt === false) {
            return ["error" => "Error al ejecutar la consulta de vencimientos"];
        }

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ["success" => true, "data" => $resultado ?: []];
    } catch (PDOException $e) {
        return ["error" => $e->getMessage()];
    }
}

private function registrar($datosPerfil = null)
{
    if (empty($this->cedula_persona) || empty($this->id_productos) || empty($this->monto_total) || empty($this->cantidad_cuotas) || empty($this->dia_pago) || empty($this->fecha_inicio)) {
        return ["incompleto" => "Datos Incompletos"];
    }

    $fechaIngresada = strtotime($this->fecha_inicio);
    $hoy = strtotime(date('Y-m-d'));
    $unAnioDespues = strtotime('+1 year');

    if ($fechaIngresada === false || $fechaIngresada < $hoy || $fechaIngresada > $unAnioDespues) {
        return ["error" => "Fecha de inicio inválida"];
    }

    if ($this->monto_total <= $this->pago_inicial) {
        return ["error" => "El monto total debe ser mayor al pago inicial"];
    }

    $monto_cuota = ($this->monto_total - $this->pago_inicial) / $this->cantidad_cuotas;

    try {
        $conex = new conexion("sistema");
        $conex->beginTransaction();

        $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");

        if ($datosPerfil !== null) {
            $sqlPerfil = "UPDATE perfiles_financiamiento SET 
                            tipo_residencia = :t_res, 
                            carga_familiar = :c_fam, 
                            estado_civil = :e_civil, 
                            profesion = :prof, 
                            ocupacion = :ocup, 
                            ingresos_mensuales = :ingresos, 
                            score_credito = :score 
                          WHERE cedula_persona = :cedula";
            
            $stmtPerfil = $conex->prepare($sqlPerfil);
            $stmtPerfil->execute([
                ":t_res"    => $datosPerfil['tipo_residencia'] ?? 'Familiar',
                ":c_fam"    => $datosPerfil['carga_familiar'] ?? 0,
                ":e_civil"  => $datosPerfil['estado_civil'] ?? 'Soltero',
                ":prof"     => $datosPerfil['profesion'] ?? 'Empleado',
                ":ocup"     => $datosPerfil['ocupacion'] ?? null,
                ":ingresos" => $datosPerfil['ingresos_mensuales'] ?? 0.00,
                ":score"    => $datosPerfil['score_credito'] ?? 5,
                ":cedula"   => $this->cedula_persona
            ]);
        }

        $stmt = $conex->prepare("INSERT INTO financiamientos(cedula_persona, monto_total, pago_inicial, cantidad_cuotas, monto_cuota, dia_pago, fecha_inicio) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt->execute([$this->cedula_persona, $this->monto_total, $this->pago_inicial, $this->cantidad_cuotas, $monto_cuota, $this->dia_pago, $this->fecha_inicio])) {
            $id_finan = $conex->lastInsertId();
            
            $conex->prepare("INSERT INTO detalles_financiamiento(id_financiamiento, id_productos, id_unidad, estado_equipo) VALUES (?, ?, ?, 'activo')")
                  ->execute([$id_finan, $this->id_productos, $this->id_unidad]);
            
            $conex->prepare("UPDATE unidades_telefonos SET estado_venta = 'Financiado' WHERE id_unidad = ?")->execute([$this->id_unidad]);

            for ($i = 1; $i <= $this->cantidad_cuotas; $i++) {
                $f_venc = date('Y-m-', strtotime($this->fecha_inicio . " + $i month")) . str_pad($this->dia_pago, 2, '0', STR_PAD_LEFT);
                $conex->prepare("INSERT INTO cuotas(id_financiamiento, numero_cuota, fecha_vencimiento, estado_cuota) VALUES (?, ?, ?, 'pendiente')")->execute([$id_finan, $i, $f_venc]);
            }
            $conex->commit();
            return ["success" => true];
        }
        $conex->rollBack();
        return ["error" => "Error al registrar"];
    } catch (\PDOException $e) { 
        if(isset($conex)) $conex->rollBack(); 
        return ["error" => $e->getMessage()]; 
    }
}

private function modificarCompleto($datos)
{
    if (empty($datos['monto_total']) || empty($datos['cantidad_cuotas']) || empty($datos['fecha_inicio']) || empty($datos['id_productos'])) {
        return ["error" => "Datos Incompletos"];
    }

    $fechaIngresada = strtotime($datos['fecha_inicio']);
    $hoy = strtotime(date('Y-m-d'));
    $unAnioDespues = strtotime('+1 year');

    if ($fechaIngresada === false || $fechaIngresada < $hoy || $fechaIngresada > $unAnioDespues) {
        return ["error" => "Fecha de inicio inválida"];
    }

    if ($datos['monto_total'] <= $datos['pago_inicial']) {
        return ["error" => "El monto total debe ser mayor al pago inicial"];
    }

    try {
        $conex = new conexion("sistema");
        $conex->beginTransaction();
        
         $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");

        $nuevoMontoCuota = ($datos['monto_total'] - $datos['pago_inicial']) / $datos['cantidad_cuotas'];
        
        $sqlFin = "UPDATE financiamientos SET monto_total = ?, pago_inicial = ?, cantidad_cuotas = ?, monto_cuota = ?, dia_pago = ?, fecha_inicio = ? WHERE id_financiamiento = ?";
        $conex->prepare($sqlFin)->execute([
            $datos['monto_total'], $datos['pago_inicial'], $datos['cantidad_cuotas'], 
            $nuevoMontoCuota, $datos['dia_pago'], $datos['fecha_inicio'], $this->id_financiamiento
        ]);
        
        $sqlDet = "UPDATE detalles_financiamiento SET id_productos = ?, id_unidad = ?, estado_equipo = 'activo' WHERE id_financiamiento = ?";
        $conex->prepare($sqlDet)->execute([$datos['id_productos'], $datos['id_unidad'], $this->id_financiamiento]);
        
        $conex->prepare("DELETE FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pendiente'")
              ->execute([$this->id_financiamiento]);
        
        for ($i = 1; $i <= $datos['cantidad_cuotas']; $i++) {
            $f_venc = date('Y-m-', strtotime($datos['fecha_inicio'] . " + $i month")) . str_pad($datos['dia_pago'], 2, '0', STR_PAD_LEFT);
            $conex->prepare("INSERT INTO cuotas(id_financiamiento, numero_cuota, fecha_vencimiento, estado_cuota) VALUES (?, ?, ?, 'pendiente')")
                  ->execute([$this->id_financiamiento, $i, $f_venc]);
        }

        $conex->commit();
        return ["success" => true];
    } catch (\PDOException $e) {
        if (isset($conex)) $conex->rollBack();
        return ["error" => $e->getMessage()];
    }
}

private function registrarPago($id_cuota, $monto, $id_metodo)
{
    if (empty($id_cuota) || empty($monto) || empty($id_metodo)) {
        return ["error" => "Datos Incompletos"];
    }

    if (!is_numeric($monto) || $monto <= 0) {
        return ["error" => "El monto debe ser un valor numérico positivo"];
    }

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
            $result = $stmt_info->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                $conex->rollBack();
                return ["error" => "Cuota no encontrada"];
            }

            $id_finan = $result['id_financiamiento'];

            $stmt_check = $conex->prepare("SELECT COUNT(*) as pendientes FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pendiente'");
            $stmt_check->execute([$id_finan]);
            
            if ($stmt_check->fetch(PDO::FETCH_ASSOC)['pendientes'] == 0) {
                $conex->prepare("UPDATE financiamientos SET estado_financiamiento = 'finalizado' WHERE id_financiamiento = ?")->execute([$id_finan]);
                $conex->prepare("UPDATE detalles_financiamiento SET estado_equipo = 'activo' WHERE id_financiamiento = ?")->execute([$id_finan]);
            }
            
            $conex->commit();
            return ["success" => true];
        }
        
        $conex->rollBack();
        return ["error" => "Error al procesar pago"];
    } catch (PDOException $e) { 
        if(isset($conex)) $conex->rollBack(); 
        return ["error" => $e->getMessage()]; 
    }
}

public function obtenerCedulaPorCuota($id_cuota)
{
    $sql = "SELECT f.cedula_persona 
            FROM cuotas c 
            JOIN financiamientos f ON c.id_financiamiento = f.id_financiamiento 
            WHERE c.id_cuota = :id_cuota";
    
    $stmt = $this->prepare($sql);
    $stmt->execute([':id_cuota' => $id_cuota]);
    return $stmt->fetchColumn();
}

public function obtenerInfoParaNotificacion($id_cuota)
{
    
    $sql = "SELECT CAST(f.cedula_persona AS CHAR) as cedula_persona, pr.nombre_producto, c.numero_cuota
            FROM cuotas c
            JOIN financiamientos f ON c.id_financiamiento = f.id_financiamiento
            JOIN detalles_financiamiento df ON f.id_financiamiento = df.id_financiamiento
            JOIN productos pr ON df.id_productos = pr.id_producto
            WHERE c.id_cuota = :id_cuota";
    
    $stmt = $this->prepare($sql);
    $stmt->execute([':id_cuota' => $id_cuota]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

private function cambiarEstadoEquipo($nuevo_estado)
{
    $estadosPermitidos = ['activo', 'bloqueado', 'suspendido'];
    if (!in_array($nuevo_estado, $estadosPermitidos)) {
        return ["error" => "Estado no permitido"];
    }

    if (empty($this->getId_financiamiento())) {
        return ["error" => "ID de financiamiento no definido"];
    }

    try {
        $conex = new conexion("sistema");
        $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");

        $stmt = $conex->prepare("UPDATE detalles_financiamiento SET estado_equipo = ? WHERE id_financiamiento = ?");
        $resultado = $stmt->execute([$nuevo_estado, $this->getId_financiamiento()]);
        
        return $resultado ? ["success" => true] : ["error" => "No se pudo actualizar el estado"];
    } catch (PDOException $e) { 
        return ["error" => $e->getMessage()]; 
    }
}
    
private function anularFinanciamiento()
{
    if (empty($this->getId_financiamiento())) {
        return ["error" => "ID de financiamiento no definido"];
    }

    try {
        $conex = new conexion("sistema");
         
        $conex->beginTransaction();

         $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");

        $stmt = $conex->prepare("SELECT COUNT(*) FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pagado'");
        $stmt->execute([$this->getId_financiamiento()]);
        
        if ($stmt->fetchColumn() > 0) {
            $conex->rollBack();
            return ["error" => "No se puede anular un financiamiento que ya tiene pagos registrados."];
        }

        $stmt = $conex->prepare("UPDATE financiamientos SET estado_financiamiento = 'anulado' WHERE id_financiamiento = ?");
        $stmt->execute([$this->getId_financiamiento()]);

        $conex->prepare("UPDATE detalles_financiamiento SET estado_equipo = 'activo' WHERE id_financiamiento = ?")
              ->execute([$this->getId_financiamiento()]);

        $conex->prepare("DELETE FROM cuotas WHERE id_financiamiento = ? AND estado_cuota = 'pendiente'")
              ->execute([$this->getId_financiamiento()]);

        $conex->commit();
        return ["success" => true];
    } catch (PDOException $e) {
        if (isset($conex) && $conex->inTransaction()) {
            $conex->rollBack();
        }
        return ["error" => $e->getMessage()];
    }
}

private function finalizarContratoManualmente()
{
    if (empty($this->getId_financiamiento())) {
        return ["error" => "ID de financiamiento no definido"];
    }

    try {
        $conex = new conexion("sistema");
       
        
        $conex->beginTransaction();
         $user = $_SESSION["username"];
        $conex->exec("SET @usuario_actual = '{$user}'");
        $conex->exec("SET @modulo = 'Administrar Financiamiento'");
        

        $stmt = $conex->prepare("UPDATE financiamientos SET estado_financiamiento = 'finalizado' WHERE id_financiamiento = ?");
        $stmt->execute([$this->getId_financiamiento()]);

        $stmtDetalle = $conex->prepare("UPDATE detalles_financiamiento SET estado_equipo = 'activo' WHERE id_financiamiento = ?");
        $stmtDetalle->execute([$this->getId_financiamiento()]);

        $conex->commit();
        return ["success" => true];
    } catch (PDOException $e) { 
        if (isset($conex) && $conex->inTransaction()) {
            $conex->rollBack();
        }
        return ["error" => $e->getMessage()]; 
    }
}

    public function listarMetodos() { 
        $res = $this->query("SELECT id_metodopago, nombre_metodopago FROM metodo_pago WHERE estado = 1")->fetchAll(PDO::FETCH_ASSOC);
        return $res ? $res : []; 
    }



public function listarBancos() 
{ return $this->query("SELECT id_banco, nombre_banco FROM bancos WHERE estado = 'activo'")->fetchAll(PDO::FETCH_ASSOC); }

 public function listarClientes() 
{
    $sql = "SELECT p.cedula_persona, p.nombre, p.apellido 
            FROM persona p
            INNER JOIN clientes c ON p.cedula_persona = c.cedula_persona
            WHERE EXISTS (
                SELECT 1 FROM perfiles_financiamiento pf 
                WHERE pf.cedula_persona = p.cedula_persona
            )
            ORDER BY p.nombre ASC";
            
    return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
    
  public function listarTelefonosDisponibles() { 
        $sql = "SELECT p.id_producto, p.nombre_producto, p.precio_detal, ut.id_unidad, ut.imei, ut.almacenamiento, ut.memoria_ram 
                FROM unidades_telefonos ut
                INNER JOIN productos p ON ut.id_producto = p.id_producto
                WHERE p.id_categoria = 26 
                AND ut.estado_venta = 'Disponible'
                AND p.id_producto NOT IN (
                    SELECT df.id_productos 
                    FROM detalles_financiamiento df
                    INNER JOIN financiamientos f ON df.id_financiamiento = f.id_financiamiento
                    WHERE f.estado_financiamiento IN ('vigente', 'finalizado')
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

    public function setId_financiamiento($id) {
        if (is_numeric($id) && $id > 0) {
            $this->id_financiamiento = (int)$id;
            return true;
        }
        return false;
    }

    public function getCedula_persona() { return $this->cedula_persona; }

    public function setCedula_persona($cedula) {
        $c = htmlspecialchars(strip_tags(trim($cedula)));
        if (!empty($c)) {
            $this->cedula_persona = $c;
            return true;
        }
        return false;
    }

    public function getId_productos() { return $this->id_productos; }

    public function setId_productos($id) {
        if (is_numeric($id) && $id > 0) {
            $this->id_productos = (int)$id;
            return true;
        }
        return false;
    }

    public function getId_unidad() { return $this->id_unidad; }

    public function setId_unidad($id) {
        if (is_numeric($id) && $id > 0) {
            $this->id_unidad = (int)$id;
            return true;
        }
        return false;
    }

    public function getMonto_total() { return $this->monto_total; }

    public function setMonto_total($monto) {
        if (is_numeric($monto) && $monto >= 0) {
            $this->monto_total = (float)$monto;
            return true;
        }
        return false;
    }

    public function getPago_inicial() { return $this->pago_inicial; }

    public function setPago_inicial($pago) {
        if (is_numeric($pago) && $pago >= 0) {
            $this->pago_inicial = (float)$pago;
            return true;
        }
        return false;
    }

    public function getCantidad_cuotas() { return $this->cantidad_cuotas; }

    public function setCantidad_cuotas($cant) {
        if (is_numeric($cant) && $cant > 0) {
            $this->cantidad_cuotas = (int)$cant;
            return true;
        }
        return false;
    }

    public function getDia_pago() { return $this->dia_pago; }

    public function setDia_pago($dia) {
        $d = (int)$dia;
        if ($d >= 1 && $d <= 31) {
            $this->dia_pago = $d;
            return true;
        }
        return false;
    }

    public function getFecha_inicio() { return $this->fecha_inicio; }

    public function setFecha_inicio($fecha) {
        $timestamp = strtotime($fecha);
        if ($timestamp !== false) {
            $this->fecha_inicio = date('Y-m-d', $timestamp);
            return true;
        }
        return false;
    }
}