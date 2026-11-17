# Pruebas de Aceptación con Codeception

Esta suite simula el flujo de un usuario real contra la aplicación.

## Requisitos (ya instalados en este proyecto)

- PHP 8.2
- Composer
- Dependencias de desarrollo (ya están en `vendor/`):
  - `codeception/codeception` 5.3
  - `codeception/module-phpbrowser`
  - `codeception/module-asserts`
  - `codeception/module-webdriver` (opcional, solo para navegador real)

> **No hay que instalar nada más para la demo.** PhpBrowser no necesita Chrome ni chromedriver.

## Cómo ejecutar la demo

1. Abre `ejecutar_pruebas_aceptacion.bat` (doble clic).
   Ese script levanta el servidor PHP en `http://localhost:8080`,
   ejecuta las pruebas y apaga el servidor.

   O desde la terminal:

   ```bat
   php vendor/bin/codecept run Acceptance
   ```

   (Requiere que la app esté disponible en `http://localhost:8080/src/`).

2. Para ver cada paso con detalle:

   ```bat
   php vendor/bin/codecept run Acceptance --steps
   ```

   Para ver la salida tipo consola de las pruebas:

   ```bat
   php vendor/bin/codecept run Acceptance --html
   ```

## Archivos de la demo

| Archivo | Qué muestra |
|---|---|
| `codeception.yml` | Configuración global (namespace, rutas, extensión RunFailed) |
| `tests/Acceptance.suite.yml` | Suite: actor + módulos (`PhpBrowser`, `Asserts`) + URL base |
| `tests/Acceptance/CatalogoCest.php` | Prueba Cest: catálogo, buscador y carrito |
| `tests/Acceptance/LoginCest.php` | Prueba Cest: formulario de acceso y credenciales |
| `tests/Acceptance/catalogo.feature` | Gherkin: catálogo |
| `tests/Acceptance/login.feature` | Gherkin: inicio de sesión |
| `tests/Support/AcceptanceTester.php` | Definiciones de pasos Gherkin (`@Given`, `@When`, `@Then`) |
| `tests/Support/_generated/` | Acciones generadas por Codeception (no se edita a mano) |

## Si usas Apache en el puerto 80

Cambia la URL en `tests/Acceptance.suite.yml`:

```yaml
- PhpBrowser:
    url: http://localhost/src/
```

## Regla importante

Cada vez que cambies `Acceptance.suite.yml` o los módulos, regenera el Actor:

```bat
php vendor/bin/codecept build
```

## Opcional: navegador real (JavaScript)

Si quieres probar interacciones con JavaScript (por ejemplo, el filtro del
buscador o el login por AJAX), ejecuta con el entorno `chrome`:

```bat
php vendor/bin/codecept run Acceptance --env chrome
```

Requisitos:

1. Google Chrome instalado.
2. `chromedriver.exe` compatible con tu versión de Chrome, en el `PATH`.
   En este proyecto hay uno en `tests_automatizados/chromedriver.exe`.

## Resultado esperado

```text
OK (9 tests, 19 assertions)
```
