<?php

namespace App\Sistema\models;

class Respuesta {

    private $exito;

    public function setExito($valor) {
        $this->exito = $valor;
        return $this;
    }

    public function getExito() {
        return $this->exito;
    }

    public function json($respuesta, $mensajeExito) {
        header('Content-Type: application/json');

        if ($respuesta === true) {
            echo json_encode(['success' => $mensajeExito]);
        } elseif (is_array($respuesta)) {
            echo json_encode($respuesta);
        } else {
            echo json_encode(['error' => 'Error desconocido en el servidor.']);
        }

        exit();
    }

    public function jsonConMensaje($respuesta, $mensajeExito) {
        header('Content-Type: application/json');

        if ($respuesta === true || (is_array($respuesta) && isset($respuesta['success']))) {
            echo json_encode(['success' => $mensajeExito]);
        } else {
            echo json_encode(is_array($respuesta) ? $respuesta : ['error' => 'Error interno.']);
        }

        exit();
    }
}
