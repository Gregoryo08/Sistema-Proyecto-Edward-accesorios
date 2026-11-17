<?php

namespace App\Sistema\Helpers;

/**
 * Logica pura del login, sin acceso a base de datos ni a sesion.
 *
 * Se extrae aqui para poder probarla con pruebas unitarias y para que la
 * usen tanto el login de la tienda (TiendaController) como el login legacy
 * de la intranet (models\login), sin duplicar las reglas de formato.
 */
class LoginHelper
{
    /** Tipos de cedula aceptados en el alta/login de la tienda. */
    public const TIPOS_CEDULA = ['V', 'E', 'J', 'P'];

    /** Cantidad minima de digitos del cuerpo de la cedula. */
    public const ANCHO_MINIMO_CEDULA = 7;

    /** Cantidad maxima de digitos del cuerpo de la cedula. */
    public const LARGO_MAXIMO_CEDULA = 12;

    /** Edad minima para registrarse. */
    public const EDAD_MINIMA = 18;

    /**
     * Normaliza una cedula venezolana al formato "V-12345678".
     *
     * Acepta separadores libres entre la letra y el numero (puntos, guiones,
     * espacios). Devuelve null si el formato no es valido.
     *
     * @param  mixed $valor
     * @return string|null
     */
    public static function normalizarCedula($valor)
    {
        $valor = strtoupper(trim((string) $valor));

        if (!preg_match('/^([VEJP])\D*(\d+)$/', $valor, $coincidencia)) {
            return null;
        }

        $tipo   = $coincidencia[1];
        $numero = $coincidencia[2];

        if (!in_array($tipo, self::TIPOS_CEDULA, true)) {
            return null;
        }

        $largo = strlen($numero);

        if ($largo < self::ANCHO_MINIMO_CEDULA || $largo > self::LARGO_MAXIMO_CEDULA) {
            return null;
        }

        return $tipo . '-' . $numero;
    }

    /**
     * Normaliza el usuario del login legacy de la intranet.
     *
     * Reglas historicas:
     *  - Solo letras (nombre de usuario): se usa tal cual.
     *  - "V-1234567" / "E-1234567": se usa tal cual.
     *  - Solo digitos: se antepone "V-".
     *  - Cualquier otro formato: null (invalido).
     *
     * @param  mixed $usuario
     * @return string|null
     */
    public static function normalizarUsuarioIntranet($usuario)
    {
        $usuario = trim((string) $usuario);

        if ($usuario === '') {
            return null;
        }

        if (preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ]+$/u', $usuario)) {
            return $usuario;
        }

        if (preg_match('/^[VE]-\d{6,9}$/', $usuario)) {
            return $usuario;
        }

        if (ctype_digit($usuario)) {
            return 'V-' . $usuario;
        }

        return null;
    }

    /**
     * Indica si una persona es mayor de edad respecto a una fecha de
     * referencia (por defecto, hoy).
     *
     * @param  mixed                 $fechaNacimiento
     * @param  \DateTimeInterface|null $referencia
     * @return bool
     */
    public static function esMayorDeEdad($fechaNacimiento, $referencia = null)
    {
        $fecha = trim((string) $fechaNacimiento);

        if ($fecha === '') {
            return false;
        }

        try {
            $nacimiento = new \DateTime($fecha);
            $hoy = $referencia instanceof \DateTimeInterface ? $referencia : new \DateTime();
        } catch (\Exception $e) {
            return false;
        }

        return $hoy->diff($nacimiento)->y >= self::EDAD_MINIMA;
    }
}
