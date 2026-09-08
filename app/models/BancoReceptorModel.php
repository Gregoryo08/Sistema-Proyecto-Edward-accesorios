<?php
// /src/app/models/BancoReceptorModel.php
// =============================================
// BANCO RECEPTOR: dónde la empresa recibe el dinero.
// Los datos viven fijos en código (constante) porque NO forman parte
// de la estructura de BD firmada (no existe una tabla para esto).
// =============================================

namespace App\Sistema\models;

class BancoReceptorModel {

    /**
     * Datos de las cuentas receptoras por método de pago.
     * Mismo formato que en su día devolvía la tabla `bancos_receptor`.
     */
    private const RECEPTORES = [
        'transferencia' => [
            'metodo'            => 'transferencia',
            'nombre_banco'      => 'Banco de Venezuela',
            'banco_id'          => 3,
            'numero_cuenta'     => '0102-0202-1545-7896-3784',
            'tipo_cuenta'       => 'Corriente',
            'titular'           => 'Edward Accesorios C.A.',
            'cedula_rif'        => 'J-44445698',
            'telefono'          => '',
            'cedula_pago_movil' => '',
            'estado'            => 'activo',
        ],
        'pago_movil' => [
            'metodo'            => 'pago_movil',
            'nombre_banco'      => 'Banco de Venezuela',
            'banco_id'          => 3,
            'numero_cuenta'     => '',
            'tipo_cuenta'       => '',
            'titular'           => 'Edward Accesorios C.A.',
            'cedula_rif'        => 'J-44445698',
            'telefono'          => '0414-1234567',
            'cedula_pago_movil' => 'V-12345678',
            'estado'            => 'activo',
        ],
    ];

    /**
     * Fila completa del receptor según el método de pago.
     * @return array|null
     */
    public function obtener($metodo) {
        $metodo = ($metodo === 'pago_movil') ? 'pago_movil' : 'transferencia';
        return self::RECEPTORES[$metodo] ?? null;
    }

    /**
     * Nombre del banco receptor (para el hidden banco_receptor del reporte).
     * @return string|null
     */
    public function obtenerNombre($metodo) {
        $fila = $this->obtener($metodo);
        return $fila ? $fila['nombre_banco'] : null;
    }

    /**
     * Datos listos para la vista datosPago (misma forma que ofrecía Checkout).
     * transferencia → nombre, numero_cuenta, tipo_cuenta, titular, cedula_rif
     * pago_movil    → banco, telefono, cedula, rif, titular
     * @return array
     */
    public function obtenerDatosPago($metodo) {
        $fila = $this->obtener($metodo);
        if (!$fila) {
            return [];
        }
        if ($metodo === 'pago_movil') {
            return [
                'banco'         => $fila['nombre_banco'],
                'telefono'      => $fila['telefono'],
                'cedula'        => $fila['cedula_pago_movil'],
                'rif'           => $fila['cedula_rif'],
                'titular'       => $fila['titular'],
            ];
        }
        return [
            'nombre'        => $fila['nombre_banco'],
            'numero_cuenta' => $fila['numero_cuenta'],
            'tipo_cuenta'   => $fila['tipo_cuenta'],
            'titular'       => $fila['titular'],
            'cedula_rif'    => $fila['cedula_rif'],
        ];
    }
}
