<?php

declare(strict_types=1);

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Pruebas de aceptacion del flujo de acceso de clientes.
 */
final class LoginCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('?pagina=login_principal');
        $I->esperoPaginaLista();
    }

    // Escenario: el formulario de acceso esta disponible
    public function verFormularioDeAcceso(AcceptanceTester $I): void
    {
        $I->seeElement('#clienteLoginForm');
        $I->seeElement('#cedula_cliente');
        $I->seeElement('#pass_cliente');
        $I->seeElement('#acceder');
    }

    // Escenario: el usuario completa sus credenciales
    public function completarCredenciales(AcceptanceTester $I): void
    {
        $I->click('#cedula_cliente');
        $I->fillField('#cedula_cliente', 'V-12345678');
        $I->fillField('#pass_cliente', 'claveDePrueba');

        $I->seeInField('#cedula_cliente', 'V-12345678');
        $I->seeInField('#pass_cliente', 'claveDePrueba');
    }
}
