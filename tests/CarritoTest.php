<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/Helpers/CarritoHelper.php';

class CarritoTest extends TestCase
{
    private CarritoHelper $carritoHelper;

    // Se ejecuta ANTES de CADA prueba
    protected function setUp(): void
    {
        $this->carritoHelper = new CarritoHelper();
    }

    // Se ejecuta DESPUÉS de CADA prueba
    protected function tearDown(): void
    {
        unset($this->carritoHelper);
    }

    // PRUEBA 1: Verificar cálculo del subtotal
    public function testCalcularSubtotal(): void
    {
        $precios = [1500, 800, 700];
        $resultado = $this->carritoHelper->calcularSubtotal($precios);
        // Esperamos que 1500+800+700 = 3000
        $this->assertEquals(3000, $resultado);
    }

    // PRUEBA 2: Verificar cálculo del IVA
    public function testCalcularIva(): void
    {
        $subtotal = 3000;
        $iva = $this->carritoHelper->calcularIva($subtotal);
        // 16% de 3000 = 480
        $this->assertEquals(480, $iva);
    }

    // PRUEBA 3: Verificar cálculo del total
    public function testCalcularTotal(): void
    {
        $subtotal = 3000;
        $iva = 480;
        $total = $this->carritoHelper->calcularTotal($subtotal, $iva);
        // 3000 + 480 = 3480
        $this->assertEquals(3480, $total);
    }

    // PRUEBA 4: Verificar que el carrito SI tiene productos
    public function testCarritoTieneProductos(): void
    {
        $carrito = ['producto1', 'producto2'];
        $this->assertTrue($this->carritoHelper->tieneProductos($carrito));
    }

    // PRUEBA 5: Verificar aplicación de descuento
    public function testAplicarDescuentoConMontoMayor(): void
    {
        $total = 1500;
        $totalConDescuento = $this->carritoHelper->aplicarDescuento($total);
        // Debe aplicarse 10% de descuento -> 1350
        $this->assertEquals(1350, $totalConDescuento);
    }

    // PRUEBA 6: Verificar que NO se aplica descuento con monto menor
    public function testNoAplicarDescuentoConMontoMenor(): void
    {
        $total = 500;
        $totalSinDescuento = $this->carritoHelper->aplicarDescuento($total);
        $this->assertEquals(500, $totalSinDescuento);
    }
}