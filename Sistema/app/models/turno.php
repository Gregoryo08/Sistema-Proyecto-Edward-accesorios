<?php

namespace App\Sistema\models;

use App\Sistema\config\Conexion;
use \PDO;
use \DateTime;
use \PDOException;

class Turno extends Conexion
{
    private $id_turno;
    private $cedula_recepcionista;
    private $cedula_empleados;
    private $fecha_turno;
    private $hora_entrada;
    private $hora_salida;
    private $observacion;

    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerTurno($fecha_turno)
    {
        $stmt = $this->prepare("SELECT COUNT(*) as conteo FROM turnos WHERE fecha_turno = :f");
        $stmt->bindParam(":f", $fecha_turno);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function listarEmpleados()
    {
        $stmt = $this->query("SELECT e.cedula_empleado as id, CONCAT(e.nombre, ' ', e.apellido) as text, e.nombre, e.apellido, c.nombre_cargo as cargo FROM empleados as e INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE e.estado = 'activo'");
        $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function listar()
    {
        $stmt = $this->prepare("SELECT t.fecha_turno, t.hora_entrada, t.hora_salida, t.id_turno, COUNT(te.cedula_empleado) as conteo FROM turnos as t LEFT JOIN turno_empleado as te ON t.id_turno = te.id_turno GROUP BY t.id_turno ORDER BY t.fecha_turno ASC");
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

    public function consultar()
    {
        $id = $this->getId_turno();
        if (empty($id)) {
            return ["incompleto" => "Dato ingresado esta vacio!"];
        }
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "Identificador del registro no es valido!"];
        }
        try {
            $stmt = $this->prepare("SELECT * FROM turnos WHERE id_turno = :id");
            if (!($stmt->execute([":id" => $id]))) {
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }

            $datos_turno = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($datos_turno && isset($datos_turno['fecha_turno'])) {
                $fecha_objeto = DateTime::createFromFormat('Y-m-d', $datos_turno['fecha_turno']);

                if ($fecha_objeto) {
                    $datos_turno['fecha_turno'] = $fecha_objeto->format('d/m/Y');
                }
            }

            $stmt = $this->prepare("SELECT e.cedula_empleado, e.nombre, e.apellido, c.nombre_cargo FROM turno_empleado as te INNER JOIN empleados as e ON te.cedula_empleado = e.cedula_empleado INNER JOIN cargos as c ON e.id_cargo = c.id_cargo WHERE te.id_turno = :id");
            if (!($stmt->execute([":id" => $id]))) {
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }
            $lista_empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->prepare("SELECT descripcion FROM observaciones_turno WHERE id_turno = :id");
            if (!($stmt->execute([":id" => $id]))) {
                return ["error" => "Ah ocurrido un error con el servidor!"];
            }
            $lista_observaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = [
                "turno" => $datos_turno,
                "datos_empleados" => $lista_empleados,
                "observaciones" => $lista_observaciones
            ];
            return $resultado;
        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function registrar()
    {
        $cedulas_empleados_raw = $this->getCedula_empleado();
        $fecha_turno = $this->getFecha_turno();
        $hora_entrada = $this->getHora_entrada();
        $hora_salida = $this->getHora_salida();
        $observaciones_raw = $this->getObs();
        $input_errores = [];

        if (empty($fecha_turno)) {
            $input_errores[] = "fecha";
        }
        if (empty($hora_entrada)) {
            $input_errores[] = "hora_entrada";
        }
        if (empty($hora_salida)) {
            $input_errores[] = "hora_salida";
        }
        if (!empty($input_errores)) {
            return ["incompleto" => "Algunos de los datos introducidos estan vacios, corrigelos para continuar con el registro!", "input" => implode("-", $input_errores)];
        }

        $cedulas_segmentos = array_filter(explode("-", $cedulas_empleados_raw));
        $cedulas_validas = [];
        $temp_cedula = "";
        $has_valid_cedulas = false;

        foreach ($cedulas_segmentos as $segmento) {
            if ($segmento === 'V' || $segmento === 'E') {
                $temp_cedula = $segmento . "-";
            } elseif (is_numeric($segmento) && !empty($temp_cedula)) {
                $cedula_completa = $temp_cedula . $segmento;
                if (preg_match('/^[VE]-\d{6,10}$/', $cedula_completa)) {
                    $cedulas_validas[] = $cedula_completa;
                    $has_valid_cedulas = true;
                } else {
                    return ["invalido" => "Formato de la cedula '$cedula_completa' No es Valido!", "input" => "Listaempleado"];
                }
                $temp_cedula = "";
            } else if (!empty($segmento)) {
                return ["invalido" => "Formato de la cedula '$segmento' No es Valido!", "input" => "Listaempleado"];
            }
        }

        if (!$has_valid_cedulas) {
            return ["invalido" => "No se han asignado empleados válidos al turno.", "input" => "Listaempleado"];
        }

        $formato_fecha = DateTime::createFromFormat('Y-m-d', $fecha_turno);
        if (!($formato_fecha) || $formato_fecha->format('Y-m-d') !== $fecha_turno) {
            return ["invalido" => "Formato de Fecha '$fecha_turno' es Inválido!", "input" => "fecha"];
        }


        $observaciones_validas = [];
        if (is_array($observaciones_raw)) {
            foreach ($observaciones_raw as $obs_segmento) {
                if (is_string($obs_segmento) && strlen($obs_segmento) >= 5) {
                    $observaciones_validas[] = $obs_segmento;
                } else {
                    return ["invalido" => "Formato de Observación '$obs_segmento' No Válido! (mínimo 5 caracteres)", "input" => "observaciones"];
                }
            }
        }

        try {
            $this->beginTransaction();
            $user = $_SESSION["username"];
            $modulo = 'Administrar Turnos';
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");
            $stmt = $this->prepare("INSERT INTO turnos (fecha_turno, hora_entrada, hora_salida) VALUES (:f, :he, :hs)");
            if (!($stmt->execute([":f" => $fecha_turno, ":he" => $hora_entrada, ":hs" => $hora_salida]))) {
                $this->rollBack();
                return ["error" => "Ah ocurrido un error al insertar el turno principal!"];
            }

            $id_turno = $this->lastInsertId();

            if (!empty($cedulas_validas)) {
                $stmt_empleados = $this->prepare("INSERT INTO turno_empleado (id_turno, cedula_empleado) VALUES (:idT, :c)");
                foreach ($cedulas_validas as $cedula) {
                    if (!($stmt_empleados->execute([":idT" => $id_turno, ":c" => $cedula]))) {
                        $this->rollBack();
                        return ["error" => "Ah ocurrido un error al registrar un empleado al turno!"];
                    }
                }
            }

            if (!empty($observaciones_validas)) {
                $stmt_obs = $this->prepare("INSERT INTO observaciones_turno (descripcion, id_turno) VALUES (:o, :idT)");
                foreach ($observaciones_validas as $observacion_texto) {
                    if (!($stmt_obs->execute([":o" => $observacion_texto, ":idT" => $id_turno]))) {
                        $this->rollBack();
                        return ["error" => "Ah ocurrido un error al registrar una observación!"];
                    }
                }
            }

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->rollBack();
            return ["error" => $e->getMessage()];
        }
    }
    public function modificar()
    {
        $id_turno = $this->getId_turno();
        $cedulas_empleados_raw = $this->getCedula_empleado();
        $fecha_turno = $this->getFecha_turno();
        $hora_entrada = $this->getHora_entrada();
        $hora_salida = $this->getHora_salida();
        $observaciones_raw = $this->getObs();
        $input_errores = [];

        if (empty($id_turno)) {
            return ["incompleto" => "El ID del turno está vacío!"];
        }

        if (empty($fecha_turno)) {
            $input_errores[] = "fecha";
        }
        if (empty($hora_entrada)) {
            $input_errores[] = "hora_entrada";
        }
        if (empty($hora_salida)) {
            $input_errores[] = "hora_salida";
        }

        if (!empty($input_errores)) {
            return ["incompleto" => "Algunos de los datos introducidos estan vacios, corrigelos para continuar!", "input" => implode("-", $input_errores)];
        }

        if (!filter_var($id_turno, FILTER_VALIDATE_INT) || $id_turno <= 0) {
            return ["invalido" => "Identificador del registro no es valido!"];
        }

        $cedulas_validas = [];
        if (!empty($cedulas_empleados_raw)) {
            $cedulas_segmentos = array_filter(explode("-", $cedulas_empleados_raw));
            $temp_cedula = "";
            $has_valid_cedulas = false;

            foreach ($cedulas_segmentos as $segmento) {
                if ($segmento === 'V' || $segmento === 'E') {
                    $temp_cedula = $segmento . "-";
                } elseif (is_numeric($segmento) && !empty($temp_cedula)) {
                    $cedula_completa = $temp_cedula . $segmento;
                    if (preg_match('/^[VE]-\d{6,10}$/', $cedula_completa)) {
                        $cedulas_validas[] = $cedula_completa;
                        $has_valid_cedulas = true;
                    } else {
                        return ["invalido" => "Formato de la cedula '$cedula_completa' No es Valido!", "input" => "Listaempleado"];
                    }
                    $temp_cedula = "";
                } else if (!empty($segmento)) {
                    return ["invalido" => "Formato de la cedula '$segmento' No es Valido!", "input" => "Listaempleado"];
                }
            }

            if (!$has_valid_cedulas) {
                return ["invalido" => "No se han asignado empleados válidos al turno.", "input" => "Listaempleado"];
            }
        }


        $formato_fecha = DateTime::createFromFormat('Y-m-d', $fecha_turno);
        if (!($formato_fecha) || $formato_fecha->format('Y-m-d') !== $fecha_turno) {
            return ["invalido" => "Formato de Fecha '$fecha_turno' es Inválido!", "input" => "fecha"];
        }


        $observaciones_validas = [];
        if (is_array($observaciones_raw)) {
            foreach ($observaciones_raw as $obs_segmento) {
                if (is_string($obs_segmento) && strlen($obs_segmento) >= 5) {
                    $observaciones_validas[] = $obs_segmento;
                } else {
                    return ["invalido" => "Formato de Observación '$obs_segmento' No Válido! (mínimo 5 caracteres)", "input" => "observaciones"];
                }
            }
        }

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = 'Administrar Turnos';
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

            $stmt_turno = $this->prepare("UPDATE turnos SET fecha_turno = :f, hora_entrada = :he, hora_salida = :hs WHERE id_turno = :id");
            if (!($stmt_turno->execute([":f" => $fecha_turno, ":he" => $hora_entrada, ":hs" => $hora_salida, ":id" => $id_turno]))) {
                $this->rollBack();
                return ["error" => "Ah ocurrido un error al actualizar el turno!"];
            }

            $stmt_del_empleados = $this->prepare("DELETE FROM turno_empleado WHERE id_turno = :id");
            $stmt_del_empleados->execute([":id" => $id_turno]);

            if (!empty($cedulas_validas)) {
                $stmt_empleados = $this->prepare("INSERT INTO turno_empleado (id_turno, cedula_empleado) VALUES (:idT, :c)");
                foreach ($cedulas_validas as $cedula) {
                    if (!($stmt_empleados->execute([":idT" => $id_turno, ":c" => $cedula]))) {
                        $this->rollBack();
                        return ["error" => "Ah ocurrido un error al actualizar los empleados del turno!"];
                    }
                }
            }

            $stmt_del_obs = $this->prepare("DELETE FROM observaciones_turno WHERE id_turno = :id");
            $stmt_del_obs->execute([":id" => $id_turno]);

            if (!empty($observaciones_validas)) {
                $stmt_obs = $this->prepare("INSERT INTO observaciones_turno (descripcion, id_turno) VALUES (:o, :idT)");
                foreach ($observaciones_validas as $observacion_texto) {
                    if (!($stmt_obs->execute([":o" => $observacion_texto, ":idT" => $id_turno]))) {
                        $this->rollBack();
                        return ["error" => "Ah ocurrido un error al actualizar las observaciones!"];
                    }
                }
            }

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function eliminar()
    {
        $id = $this->getId_turno();
        if (empty($id)) {
            return ["incompleto" => "Dato ingresado esta vacio!"];
        }
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return ["invalido" => "Identificador del registro no es valido!"];
        }

        try {
            $this->beginTransaction();

            $user = $_SESSION["username"];
            $modulo = 'Administrar Turnos';
            $this->exec("SET @usuario_actual = '{$user}'");
            $this->exec("SET @modulo = '{$modulo}'");

            $stmt_del_empleados = $this->prepare("DELETE FROM turno_empleado WHERE id_turno = :id");
            if (!($stmt_del_empleados->execute([":id" => $id]))) {
                $this->rollBack();
                return ["error" => "Ah ocurrido un error al eliminar los empleados asociados al turno!"];
            }

            $stmt_del_obs = $this->prepare("DELETE FROM observaciones_turno WHERE id_turno = :id");
            if (!($stmt_del_obs->execute([":id" => $id]))) {
                $this->rollBack();
                return ["error" => "Ah ocurrido un error al eliminar las observaciones asociadas al turno!"];
            }

            $stmt_del_turno = $this->prepare("DELETE FROM turnos WHERE id_turno = :id");
            if (!($stmt_del_turno->execute([":id" => $id]))) {
                $this->rollBack();
                return ["error" => "Ah ocurrido un error al eliminar el turno principal!"];
            }

            $this->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $this->rollBack();
            return ["error" => $e->getMessage()];
        }
    }

    public function getId_turno()
    {
        return $this->id_turno;
    }
    public function setId_turno($turno)
    {
        $this->id_turno = $turno;
    }

    public function getCedula_recepcionista()
    {
        return $this->cedula_recepcionista;
    }
    public function setCedula_recepcionista($cedula)
    {
        $this->cedula_recepcionista = $cedula;
    }

    public function getCedula_empleado()
    {
        return $this->cedula_empleados;
    }
    public function setCedula_empleado($cedula)
    {
        $this->cedula_empleados = $cedula;
    }

    public function getFecha_turno()
    {
        return $this->fecha_turno;
    }
    public function setFecha_turno($turno)
    {
        $this->fecha_turno = $turno;
    }

    public function getHora_entrada()
    {
        return $this->hora_entrada;
    }
    public function setHora_entrada($hora)
    {
        $this->hora_entrada = $hora;
    }

    public function getHora_salida()
    {
        return $this->hora_salida;
    }
    public function setHora_salida($hora)
    {
        $this->hora_salida = $hora;
    }

    public function getObs()
    {
        return $this->observacion;
    }
    public function setObs($obs)
    {
        $this->observacion = $obs;
    }
}