<?php

class CarritoHelper
{
    /**
     * Calcula el subtotal sumando los precios de los productos
     */
    public function calcularSubtotal(array $precios): float
    {
        $subtotal = 0;
        foreach ($precios as $precio) {
            $subtotal += (float)$precio;
        }
        return $subtotal;
    }

    /**
     * Calcula el IVA (16% por defecto)
     */
    public function calcularIva(float $subtotal, float $porcentajeIva = 16): float
    {
        return ($subtotal * $porcentajeIva) / 100;
    }

    /**
     * Calcula el total (subtotal + IVA)
     */
    public function calcularTotal(float $subtotal, float $iva): float
    {
        return $subtotal + $iva;
    }

    /**
     * Verifica si el carrito tiene productos
     */
    public function tieneProductos(array $carrito): bool
    {
        return count($carrito) > 0;
    }

    /**
     * Aplica descuento si supera cierto monto
     */
    public function aplicarDescuento(float $total, float $montoMinimo = 1000): float
    {
        if ($total >= $montoMinimo) {
            return $total * 0.90; // 10% de descuento
        }
        return $total;
    }
}