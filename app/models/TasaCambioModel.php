<?php

namespace App\Sistema\models;

class TasaCambioModel
{
    const DEFAULT_TASA = 60.00;
    const MINUTOS_EXPIRACION = 30;

    private $ultimaFuente = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tasa_dolar']) || $_SESSION['tasa_dolar'] === null) {
            $this->_cargarDesdeBdUnaVez();
        }
        if (!isset($_SESSION['tasa_dolar'])) {
            $_SESSION['tasa_dolar'] = self::DEFAULT_TASA;
            $_SESSION['tasa_fecha'] = date('Y-m-d H:i:s');
            $_SESSION['tasa_fuente'] = 'default';
        }
    }

    private function _cargarDesdeBdUnaVez(): void
    {
        try {
            $conn = \App\Sistema\config\Conexion::getShared('sistema_edward')->getConexion();
            $stmt = $conn->query("SELECT tasa, fecha_actualizacion, fuente FROM tasa_cambio WHERE id = 1 LIMIT 1");
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row && isset($row['tasa'])) {
                $_SESSION['tasa_dolar'] = (float) $row['tasa'];
                $_SESSION['tasa_fecha'] = $row['fecha_actualizacion'] ?: date('Y-m-d H:i:s');
                $_SESSION['tasa_fuente'] = $row['fuente'] ?? 'manual';
            }
        } catch (\Exception $e) {

        }
    }

    public function obtener(): float
    {
        return (float) ($_SESSION['tasa_dolar'] ?? self::DEFAULT_TASA);
    }

    public function actualizarDesdeFuentes(): ?float
    {
        $tasa = $this->scrapeFuentes();

        if ($tasa === null) {
            return null;
        }

        $this->guardar($tasa, $this->ultimaFuente ?? 'scrape');

        return $tasa;
    }

    public function scrapeFuentes(): ?float
    {
        $tasa = $this->scrapeBCV();

        if ($tasa !== null && $tasa > 0) {
            $this->ultimaFuente = 'bcv_scrape';
            return $tasa;
        }

        $tasa = $this->scrapeApi();

        if ($tasa !== null && $tasa > 0) {
            $this->ultimaFuente = 'api_scrape';
            return $tasa;
        }

        $this->ultimaFuente = null;

        return null;
    }

    public function guardar(float $tasa, ?string $fuente = null): void
    {
        $fuente = $fuente ?? 'manual';
        $_SESSION['tasa_dolar'] = $tasa;
        $_SESSION['tasa_fecha'] = date('Y-m-d H:i:s');
        $_SESSION['tasa_fuente'] = $fuente;

        try {
            $conn = \App\Sistema\config\Conexion::getShared('sistema_edward')->getConexion();
            $stmt = $conn->prepare("
                INSERT INTO tasa_cambio (id, tasa, fecha_actualizacion, fuente)
                VALUES (1, ?, ?, ?)
                ON DUPLICATE KEY UPDATE tasa = VALUES(tasa), fecha_actualizacion = VALUES(fecha_actualizacion), fuente = VALUES(fuente)
            ");
            $stmt->execute([$tasa, date('Y-m-d H:i:s'), $fuente]);
        } catch (\Exception $e) {

        }
    }

    public function vigencia(): array
    {
        $fecha = $_SESSION['tasa_fecha'] ?? null;
        if (!$fecha) {
            return [
                'expirada' => true,
                'minutos' => 999,
                'horas' => 999,
                'mensaje' => 'Nunca actualizada',
            ];
        }
        $diff = time() - strtotime($fecha);
        $minutos = floor($diff / 60);
        $horas = floor($diff / 3600);
        $expirada = $minutos >= self::MINUTOS_EXPIRACION;

        return [
            'expirada' => $expirada,
            'minutos' => $minutos,
            'horas' => $horas,
            'mensaje' => $expirada
                ? "Tasa desactualizada ({$minutos}min)"
                : "Tasa actualizada hace {$minutos}min",
        ];
    }

    public function convertir(float $montoUSD): float
    {
        return round($montoUSD * $this->obtener(), 2);
    }

    public function obtenerFechaActualizacion(): string
    {
        $fecha = $_SESSION['tasa_fecha'] ?? '';
        return $fecha ? date('d/m/Y h:i A', strtotime($fecha)) : 'Nunca';
    }

    public function obtenerFuente(): string
    {
        return $_SESSION['tasa_fuente'] ?? 'N/A';
    }

    public function scrapeBCV(): ?float
    {
        if (class_exists('App\\Sistema\\models\\scrape_dolar')) {
            return \App\Sistema\models\scrape_dolar::obtenerPrecioDolarBCV();
        }
        return null;
    }

    public function scrapeApi(): ?float
    {
        if (class_exists('App\\Sistema\\models\\scrape_dolar')) {
            return \App\Sistema\models\scrape_dolar::obtenerPrecioDolarApi();
        }
        return null;
    }

    public function obtenerTasaConCache(): float
    {
        $cacheTtl = 3600;

        if (
            isset($_SESSION['tasa_bcv'], $_SESSION['tasa_bcv_time']) &&
            (time() - $_SESSION['tasa_bcv_time']) < $cacheTtl
        ) {
            return (float) $_SESSION['tasa_bcv'];
        }

        $tasa = $this->scrapeFuentes();

        if ($tasa !== null && $tasa > 0) {
            $_SESSION['tasa_bcv'] = $tasa;
            $_SESSION['tasa_bcv_time'] = time();
            return (float) $tasa;
        }

        return (float) ($_SESSION['tasa_bcv'] ?? 0.0);
    }
}
