<?php

namespace App\Sistema\models;

use DOMDocument;
use DOMXPath;

class scrape_dolar
{
    private const CONNECT_TIMEOUT = 4;
    private const TIMEOUT = 8;
    private const URL_BCV = 'https://www.bcv.org.ve/';
    private const URL_API_RESPALDO = 'https://ve.dolarapi.com/v1/dolares/oficial';

    public static function obtenerPrecioDolarBCV()
    {
        $html = self::descargar(self::URL_BCV);

        if ($html === null) {
            return null;
        }

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//div[@id="dolar"]//strong');

        if ($nodes->length > 0) {
            $valor = (float) str_replace(',', '.', trim($nodes->item(0)->textContent));

            return $valor > 0 ? $valor : null;
        }

        return null;
    }

    public static function obtenerPrecioDolarApi()
    {
        $json = self::descargar(self::URL_API_RESPALDO);

        if ($json === null) {
            return null;
        }

        $data = json_decode($json, true);

        if (!is_array($data) || !isset($data['promedio'])) {
            return null;
        }

        $valor = (float) $data['promedio'];

        return $valor > 0 ? $valor : null;
    }

    private static function descargar(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        $contenido = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('Error en cURL al obtener tasa (' . $url . '): ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if (!is_string($contenido) || $contenido === '') {
            return null;
        }

        return $contenido;
    }
}
