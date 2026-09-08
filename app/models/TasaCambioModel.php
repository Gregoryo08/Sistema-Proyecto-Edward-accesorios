<?php

namespace App\Sistema\models;

class TasaCambioModel
{
    const DEFAULT_TASA = 60.00;
    const MINUTOS_EXPIRACION = 30;

    /**
     * Estrategia de rendimiento (restaurada a la versión rápida/docker):
     * la tasa se lee y mantiene SOLO en $_SESSION (memoria), sin consultas a BD.
     * Esto elimina el costo de las lecturas repetidas de `tasa_cambio`
     * (antes: 3-5 SELECT por página de pago / por AJAX de tasa).
     *
     * El botón "BCV" del panel de administración sigue disponible y, cuando el
     * admin lo pulsa, llama a actualizarDesdeBCV() → scrapeBCV() (llamada
     * EXTERNA y MANUAL, nunca automática) y guarda el resultado en sesión.
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Pre-cargar UNA sola vez (al inicio de la sesión) el valor persistido en BD,
        // para no arrancar con DEFAULT_TASA. Las lecturas siguientes son 100% de sesión.
        if (!isset($_SESSION['tasa_dolar']) || $_SESSION['tasa_dolar'] === null) {
            $this->_cargarDesdeBdUnaVez();
        }
        if (!isset($_SESSION['tasa_dolar'])) {
            $_SESSION['tasa_dolar'] = self::DEFAULT_TASA;
            $_SESSION['tasa_fecha'] = date('Y-m-d H:i:s');
            $_SESSION['tasa_fuente'] = 'default';
        }
    }

    /**
     * Lectura puntual (una vez por sesión) del valor persistido en BD,
     * como respaldo de arranque. No afecta el rendimiento de las peticiones.
     */
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
            // si la tabla no existe o falla, se usa DEFAULT_TASA
        }
    }

    /**
     * Obtener la tasa guardada (solo sesión, sin BD).
     */
    public function obtener(): float
    {
        return (float) ($_SESSION['tasa_dolar'] ?? self::DEFAULT_TASA);
    }

    /**
     * Actualiza la tasa consultando el BCV / APIs.
     * Solo se invoca desde el panel de administración (acción explícita del
     * admin), NUNCA de forma automática.
     */
    public function actualizarDesdeBCV(): ?float
    {
        $tasaNueva = $this->scrapeBCV();
        if ($tasaNueva !== null && $tasaNueva > 0) {
            $this->guardar($tasaNueva, 'bcv_scrape');
            return $tasaNueva;
        }
        return null;
    }

    /**
     * Guardar tasa: en sesión (lectura rápida) y en BD (persistencia para que
     * los próximos inicios de sesión arranquen con este valor).
     */
    public function guardar(float $tasa, ?string $fuente = null): void
    {
        $fuente = $fuente ?? 'manual';
        $_SESSION['tasa_dolar'] = $tasa;
        $_SESSION['tasa_fecha'] = date('Y-m-d H:i:s');
        $_SESSION['tasa_fuente'] = $fuente;

        // Persistencia (opcional): una escritura por actualización, sin costo en lecturas.
        try {
            $conn = \App\Sistema\config\Conexion::getShared('sistema_edward')->getConexion();
            $stmt = $conn->prepare("
                INSERT INTO tasa_cambio (id, tasa, fecha_actualizacion, fuente)
                VALUES (1, ?, ?, ?)
                ON DUPLICATE KEY UPDATE tasa = VALUES(tasa), fecha_actualizacion = VALUES(fecha_actualizacion), fuente = VALUES(fuente)
            ");
            $stmt->execute([$tasa, date('Y-m-d H:i:s'), $fuente]);
        } catch (\Exception $e) {
            // si la tabla no existe, solo queda en sesión; sin romper el flujo
        }
    }

    /**
     * Verificar vigencia y mostrar info
     */
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

    /**
     * Convertir USD a Bs
     */
    public function convertir(float $montoUSD): float
    {
        return round($montoUSD * $this->obtener(), 2);
    }

    /**
     * Obtener fecha formateada
     */
    public function obtenerFechaActualizacion(): string
    {
        $fecha = $_SESSION['tasa_fecha'] ?? '';
        return $fecha ? date('d/m/Y h:i A', strtotime($fecha)) : 'Nunca';
    }

    /**
     * Obtener fuente de la última actualización
     */
    public function obtenerFuente(): string
    {
        return $_SESSION['tasa_fuente'] ?? 'N/A';
    }

    /**
     * Llamar al scraper (manual, desde el panel del admin).
     */
    public function scrapeBCV(): ?float
    {
        if (class_exists('App\\Sistema\\models\\scrape_dolar')) {
            return \App\Sistema\models\scrape_dolar::obtenerPrecioDolarBCV();
        }
        return null;
    }
}