<?php

namespace App\Sistema\models;

class Vista
{
    private $directorio;
    private $rutaError;

    public function __construct($directorio = '', $rutaError = '')
    {
        $this->directorio = rtrim($directorio, '/\\');
        $this->rutaError = $rutaError !== ''
            ? $rutaError
            : $this->directorio . '/404.php';
    }

    public function setDirectorio($valor)
    {
        $this->directorio = rtrim($valor, '/\\');
        return $this;
    }

    public function getDirectorio()
    {
        return $this->directorio;
    }

    public function setRutaError($valor)
    {
        $this->rutaError = $valor;
        return $this;
    }

    public function getRutaError()
    {
        return $this->rutaError;
    }

    public function existe($nombre)
    {
        return is_file($this->ruta($nombre));
    }

    public function ruta($nombre)
    {
        return $this->directorio . '/' . ltrim($nombre, '/\\');
    }

    public function render($nombre, array $variables = [])
    {
        list($ruta, $variables) = $this->preparar($nombre, $variables);

        extract($variables, EXTR_SKIP);

        ob_start();
        require $ruta;

        return ob_get_clean();
    }

    public function mostrar($nombre, array $variables = [])
    {
        list($ruta, $variables) = $this->preparar($nombre, $variables);

        extract($variables, EXTR_SKIP);

        require $ruta;
    }

    private function preparar($nombre, array $variables)
    {
        $ruta = $this->ruta($nombre);

        if ($this->existe($nombre)) {
            return array($ruta, $variables);
        }

        error_log("Vista no encontrada: " . $ruta);

        return array($this->rutaError, array('ruta_vista' => $ruta));
    }
}
