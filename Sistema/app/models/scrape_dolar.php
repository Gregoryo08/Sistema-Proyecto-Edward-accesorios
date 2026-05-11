<?php

namespace App\Sistema\models;

use DOMDocument;
use DOMXPath;

class scrape_dolar
{
    public static function obtenerPrecioDolarBCV()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.bcv.org.ve/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // Añadimos User-Agent para evitar bloqueos
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('Error en cURL al obtener BCV: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        if (empty($html)) return null;

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $query = '//div[@id="dolar"]//strong';
        $nodes = $xpath->query($query);

        if ($nodes->length > 0) {
            $dolarValue = str_replace(',', '.', trim($nodes->item(0)->textContent));
            return (float) $dolarValue;
        }
        return null;
    }
}
