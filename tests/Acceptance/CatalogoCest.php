<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Prueba de aceptacion con sintaxis Cest (PHP orientado a objetos).
 * Cada metodo publico es un escenario independiente.
 */
final class CatalogoCest
{
    // Se ejecuta antes de cada prueba
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('?pagina=web_Catalogo');
    }

    // Escenario 1: cualquier visitante puede ver el catalogo
    public function verCatalogoSinIniciarSesion(AcceptanceTester $I): void
    {
        $I->seeElement('.product-card');
        $I->seeElement('#buscadorProductos');
        $I->seeElement('#verCarrito');
    }

    // Escenario 2: el buscador esta listo para usarse
    // (en el catalogo el filtro se hace con JavaScript, por eso aqui
    //  verificamos el atributo placeholder y no escribimos en el campo)
    public function verBuscadorDeProductos(AcceptanceTester $I): void
    {
        $I->seeElement('#buscadorProductos');
        $I->assertSame(
            'Buscar productos...',
            $I->grabAttributeFrom('#buscadorProductos', 'placeholder')
        );
    }

    // Escenario 3: el catalogo muestra el contador del carrito
    public function verContadorDelCarrito(AcceptanceTester $I): void
    {
        $I->seeElement('#cartCount');
        $I->assertSame('0', trim($I->grabTextFrom('#cartCount')));
    }
}
