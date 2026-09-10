<?php
namespace App\Sistema\models;

use PDO;
use App\Sistema\config\Conexion;

class BancoModel {
    private $conn;

    // Fuente de respaldo: listado público de bancos de Venezuela (SUDEBAN/BCV).
    // Se usa SOLO cuando la tabla `bancos` está vacía (Opción B) o con ?forzar=1
    public const API_BANCOS_URL = 'https://raw.githubusercontent.com/hackhit/bancos/main/bancos.json';

    public function __construct() {
        $this->conn = Conexion::getShared('sistema_edward')->getConexion();
    }

    /**
     * Devuelve los bancos activos.
     * Si la BD está vacía (o se fuerza), sincroniza desde la API y reintenta.
     */
    public function obtenerActivos($forzarSincronizacion = false) {
        $bancos = $this->_obtenerActivos();

        if ($forzarSincronizacion || count($bancos) === 0) {
            $sincronizado = $this->sincronizarDesdeApi();
            if ($sincronizado['success']) {
                $bancos = $this->_obtenerActivos();
            }
        }

        return $bancos;
    }

    /**
     * SOLO lectura de BD, NUNCA toca la API.
     * Pensado para peticiones de páginas públicas/pago donde un fallback síncrono
     * a la API (timeout 8-16s) congelaría la página. La sincronización del
     * catálogo queda reservada a la acción explícita del admin (sincronizarBancos.php).
     */
    public function obtenerActivosSoloBd() {
        return $this->_obtenerActivos();
    }

    private function _obtenerActivos() {
        $stmt = $this->conn->query("SELECT id_banco, nombre_banco FROM bancos WHERE estado = 'activo' ORDER BY nombre_banco ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trae el listado de bancos desde la API (JSON GitHub).
     * @return array ['success' => bool, 'insertados' => int, 'error' => ?string]
     */
    public function sincronizarDesdeApi() {
        $json = $this->_obtenerJsonApi();
        if ($json === null) {
            return ['success' => false, 'insertados' => 0, 'error' => 'No se pudo consultar la API de bancos'];
        }

        $bancos = $this->_normalizarListado($json);
        if (count($bancos) === 0) {
            return ['success' => false, 'insertados' => 0, 'error' => 'La API no devolvió bancos válidos'];
        }

        $insertados = 0;
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO bancos (nombre_banco, codigo_banco)
                SELECT :nombre, :codigo
                WHERE NOT EXISTS (
                    SELECT 1 FROM bancos WHERE LOWER(TRIM(nombre_banco)) = LOWER(:nombre2)
                )
            ");
            foreach ($bancos as $b) {
                $stmt->execute([
                    ':nombre'  => $b['nombre'],
                    ':codigo'  => $b['codigo'],
                    ':nombre2' => $b['nombre'],
                ]);
                if ($stmt->rowCount() > 0) {
                    $insertados++;
                }
            }
        } catch (\PDOException $e) {
            error_log("Error al sincronizar bancos desde API: " . $e->getMessage());
            return ['success' => false, 'insertados' => 0, 'error' => $e->getMessage()];
        }

        return ['success' => true, 'insertados' => $insertados, 'error' => null];
    }

    /**
     * Devuelve el listado de bancos tal como lo entrega la API.
     * @return array [ ['nombre' => ..., 'codigo' => ...], ... ]
     */
    public function obtenerDeApi() {
        $json = $this->_obtenerJsonApi();
        if ($json === null) {
            return [];
        }
        return $this->_normalizarListado($json);
    }

    private function _obtenerJsonApi() {
        $opts = [
            'http' => [
                'method'  => 'GET',
                'timeout' => 8,
                'header'  => "Accept: application/json\r\nUser-Agent: sistema-edward/1.0\r\n",
            ],
        ];
        $ctx = stream_context_create($opts);

        $contenido = @file_get_contents(self::API_BANCOS_URL, false, $ctx);
        if ($contenido === false && function_exists('curl_init')) {
            $curl = curl_init(self::API_BANCOS_URL);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_USERAGENT      => 'sistema-edward/1.0',
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $contenido = curl_exec($curl);
            curl_close($curl);
        }

        if ($contenido === false) {
            error_log("BancoModel: no se pudo obtener la API de bancos (" . self::API_BANCOS_URL . ")");
            return null;
        }

        $json = json_decode($contenido, true);
        return is_array($json) ? $json : null;
    }

    private function _normalizarListado($json) {
        $lista = [];
        $nac = $json['bancos'] ?? $json['data'] ?? $json ?? [];

        if (is_array($nac)) {
            foreach ($nac as $item) {
                if (!is_array($item)) continue;
                $nombre = $item['nombre'] ?? $item['banco'] ?? $item['nombre_banco'] ?? $item['name'] ?? null;
                $codigo = $item['codigo'] ?? $item['codigo_banco'] ?? $item['code'] ?? null;
                if (!$nombre) continue;
                $nombre = $this->_limpiarNombreBanco($nombre);
                $codigo = $codigo !== null ? trim((string)$codigo) : null;

                // Eliminar centrales que no son bancos comerciales (no aplican al flujo de pagos)
                if ($codigo === '0001') continue;
                if ($nombre === '') continue;

                $lista[] = ['nombre' => $nombre, 'codigo' => $codigo];
            }
        }
        return $lista;
    }

    /**
     * Convierte nombres legales largos a nombres cortos y legibles.
     * "Banco de Venezuela, S.A. Banco Universal"      → "Banco de Venezuela"
     * "Banesco Banco Universal, C.A."                 → "Banesco"
     * "100% Banco, Banco Comercial, C.A"              → "100% Banco"
     */
    private function _limpiarNombreBanco($nombre) {
        $nombre = trim(preg_replace('/\s+/', ' ', (string)$nombre));
        // La fuente trae artefactos tipo "C.A ." (espacio antes del punto)
        $nombre = preg_replace('/\s+\./', '.', $nombre);

        // Sufijos legales que no aportan al nombre comercial
        $patrones = [
            '/\s*,\s*S\.A\.?\s+Banco\s+Universal\s*$/i',
            '/\s*(,\s*)?S\.A\.I\.C\.A\.?\s*$/i',
            '/\s*(,\s*)?C\.A\.?\s*(Banco\s+)?(Universal|Comercial|Microfinanciero|de\s+Desarrollo|Hipotecario)\s*,?\s*(C\.A\.?|S\.A\.?)?\s*$/i',
            '/\s+(Banco\s+)?(Universal|Comercial|Microfinanciero)\s*,?\s*(C\.A\.?|S\.A\.?|B\.U\.?)?\s*$/i',
            '/\s*(,\s*)?(B\.U\.?|C\.A\.?|S\.A\.?|S\.A\.I\.C\.A\.?)\s*$/i',
        ];

        // Iterar hasta estabilizar (ej: "Crédito C.A., Banco Universal" → "Crédito")
        for ($i = 0; $i < 6; $i++) {
            $anterior = $nombre;
            foreach ($patrones as $patron) {
                $nuevo = preg_replace($patron, '', $nombre);
                if ($nuevo !== null && trim($nuevo) !== '' && strtolower(trim($nuevo)) !== 'banco') {
                    $nombre = trim($nuevo, " \t\n\r\0\x0B,");
                }
            }
            if (strcasecmp($anterior, $nombre) === 0) break;
        }

        return trim($nombre);
    }
}