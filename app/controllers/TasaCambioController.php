<?php

namespace App\Sistema\Controllers;

use App\Sistema\models\TasaCambioModel;
use App\Sistema\models\bitacora;
use App\Sistema\config\Conexion;
use PDO;

class TasaCambioController
{
    private $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new TasaCambioModel();
    }

    public function index()
    {
        $tasa = $this->model->obtener();
        $vigencia = $this->model->vigencia();
        $fecha = $this->model->obtenerFechaActualizacion();
        $fuente = $this->model->obtenerFuente();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'tasa' => $tasa,
                'vigencia' => $vigencia,
                'fecha' => $fecha,
                'fuente' => $fuente
            ]);
            exit;
        }

        require_once __DIR__ . '/../views/admin/tasaCambio.php';
    }

    public function obtener()
    {
        header('Content-Type: application/json');
        echo json_encode([
            'tasa' => $this->model->obtener(),
            'vigencia' => $this->model->vigencia(),
            'fecha' => $this->model->obtenerFechaActualizacion(),
            'fuente' => $this->model->obtenerFuente()
        ]);
        exit;
    }

    public function verificarClave()
    {
        $clave = $_POST['clave'] ?? '';
        $username = $_SESSION['username'] ?? '';

        if (empty($clave) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }

        try {
            $conexUser = new Conexion('usuario');
            $stmt = $conexUser->prepare("SELECT clave FROM usuarios WHERE cedula_usuario = :u AND estatus = 'Activo'");
            $stmt->bindParam(':u', $username);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado || !password_verify($clave, $resultado['clave'])) {
                echo json_encode(['success' => false, 'message' => 'Clave incorrecta']);
                exit;
            }

            $_SESSION['tasa_auth'] = time();
            echo json_encode(['success' => true, 'message' => 'Clave verificada']);
            exit;
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error del servidor']);
            exit;
        }
    }

    public function actualizar()
    {
        $auth_time = $_SESSION['tasa_auth'] ?? 0;
        if (time() - $auth_time > 300) {
            echo json_encode(['success' => false, 'message' => 'Debe verificar su clave primero', 'reauth' => true]);
            exit;
        }

        $tasa = floatval($_POST['tasa'] ?? 0);
        if ($tasa <= 0) {
            echo json_encode(['success' => false, 'message' => 'Tasa inválida']);
            exit;
        }

        $tasaAnterior = $this->model->obtener();
        $this->model->guardar($tasa);

        $this->_registrarBitacora($tasaAnterior, $tasa);

        unset($_SESSION['tasa_auth']);
        echo json_encode([
            'success' => true,
            'message' => 'Tasa actualizada a Bs. ' . number_format($tasa, 2) . ' por USD',
            'tasa' => $tasa,
            'fecha' => $this->model->obtenerFechaActualizacion(),
            'fuente' => 'manual'
        ]);
        exit;
    }

    public function scrapear()
    {
        $auth_time = $_SESSION['tasa_auth'] ?? 0;
        if (time() - $auth_time > 300) {
            echo json_encode(['success' => false, 'message' => 'Debe verificar su clave primero', 'reauth' => true]);
            exit;
        }

        $tasaAnterior = $this->model->obtener();
        $precio = $this->model->actualizarDesdeBCV();
        if ($precio === null || $precio <= 0) {
            echo json_encode(['success' => false, 'message' => 'No se pudo obtener la tasa del BCV']);
            exit;
        }
        $this->_registrarBitacora($tasaAnterior, $precio, 'bcv_scrape');
        unset($_SESSION['tasa_auth']);
        echo json_encode([
            'success' => true,
            'message' => 'Tasa BCV obtenida: Bs. ' . number_format($precio, 2) . ' por USD',
            'tasa' => $precio,
            'fecha' => $this->model->obtenerFechaActualizacion(),
            'fuente' => 'bcv_scrape'
        ]);
        exit;
    }

    public function verificar()
    {
        header('Content-Type: application/json');
        echo json_encode($this->model->vigencia());
        exit;
    }

    private function _registrarBitacora(float $tasaAnterior, float $tasaNueva, ?string $fuente = null)
    {
        try {
            $usuario = $_SESSION['username'] ?? 'administrador';
            $b = new bitacora();
            $b->registrar(
                'tasa_cambio',
                'ACTUALIZAR',
                'Tasa de Cambio',
                $usuario,
                1,
                [[
                    'campo'  => 'tasa',
                    'antiguo' => (string) $tasaAnterior,
                    'nuevo'  => (string) $tasaNueva . ($fuente ? " ($fuente)" : ''),
                ]]
            );
        } catch (\Exception $e) {
            // no romper el flujo si la bitácora falla
        }
    }
}
