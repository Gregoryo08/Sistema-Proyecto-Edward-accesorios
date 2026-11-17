<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * ============================================================
     * Paso 1: Definiciones de pasos Gherkin (BDD)
     * Cada anotacion @Given/@When/@Then conecta una frase del
     * archivo .feature con codigo PHP que usa las acciones del Actor.
     * ============================================================
     */

    /** @Given que abro la página del catálogo */
    public function abroElCatalogo(): void
    {
        $this->amOnPage('?pagina=web_Catalogo');
    }

    /** @Then debería ver la lista de productos */
    public function veoLaListaDeProductos(): void
    {
        $this->seeElement('.product-card');
    }

    /** @Then debería ver el buscador de productos */
    public function veoElBuscadorDeProductos(): void
    {
        $this->seeElement('#buscadorProductos');
    }

    /** @Then el buscador debería tener el texto de ayuda :texto */
    public function elBuscadorTieneElTextoDeAyuda(string $texto): void
    {
        $this->assertSame(
            $texto,
            $this->grabAttributeFrom('#buscadorProductos', 'placeholder')
        );
    }

    /** @Given que abro la página de acceso */
    public function abroElAcceso(): void
    {
        $this->amOnPage('?pagina=login_principal');
        $this->esperoPaginaLista();
    }

    /**
     * La vista de login limpia los campos 500 ms y 1500 ms despues
     * de cargar (autofill residual). Se espera a que termine antes
     * de escribir, igual que haria un usuario real.
     */
    public function esperoPaginaLista(): void
    {
        sleep(2);
    }

    /** @Then debería ver el formulario de login */
    public function veoElFormularioDeLogin(): void
    {
        $this->seeElement('#clienteLoginForm');
    }

    /** @Then debería ver el campo de cédula */
    public function veoElCampoDeCedula(): void
    {
        $this->seeElement('#cedula_cliente');
    }

    /** @When ingreso la cédula :cedula */
    public function ingresoLaCedula(string $cedula): void
    {
        $this->click('#cedula_cliente');
        $this->fillField('#cedula_cliente', $cedula);
    }

    /** @When ingreso la contraseña :clave */
    public function ingresoLaClave(string $clave): void
    {
        $this->fillField('#pass_cliente', $clave);
    }

    /** @Then el campo cédula debería contener :valor */
    public function elCampoCedulaContiene(string $valor): void
    {
        $this->seeInField('#cedula_cliente', $valor);
    }
}
