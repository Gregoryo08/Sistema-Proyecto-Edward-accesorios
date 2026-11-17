<?php
use App\Sistema\config\Acceso;

Acceso::iniciarSesion();

$sesionTienda   = Acceso::haySesionTienda();
$sesionIntranet = Acceso::haySesionIntranet() && !Acceso::esCliente();

$verMisPedidos  = $sesionTienda && Acceso::esClienteEcommerce();
$verMisCuotas   = $sesionTienda && Acceso::esClienteFinanciamiento();

$nombre = Acceso::nombreTienda();
if ($nombre === '') {
    $nombre = trim((string) ($_SESSION['nombre_completo'] ?? ''));
}

$salir = $sesionIntranet ? '?pagina=salida' : '?pagina=cerrarSesionCliente';
?>
<div class="barra-dominio">
    <div class="barra-dominio__contenido">
        <a class="barra-dominio__marca" href="?pagina=web_Catalogo">Edward<span>Accesorios</span></a>

        <nav class="barra-dominio__enlaces">
            <a href="?pagina=web_Catalogo"><i class="fas fa-store"></i> Catálogo</a>

            <?php if ($verMisPedidos): ?>
                <a href="?pagina=misPedidos"><i class="fas fa-box"></i> Mis Pedidos</a>
            <?php endif; ?>

            <?php if ($verMisCuotas): ?>
                <a href="?pagina=principal_cliente"><i class="fas fa-wallet"></i> Mis Cuotas</a>
            <?php endif; ?>
        </nav>

        <div class="barra-dominio__sesion">
            <?php if ($sesionTienda || $sesionIntranet): ?>
                <span class="barra-dominio__usuario">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($nombre); ?>
                </span>
                <a class="barra-dominio__salir" href="<?= $salir ?>">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            <?php else: ?>
                <a class="barra-dominio__login" href="?pagina=login_principal">
                    <i class="fas fa-user"></i> Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<style>
.barra-dominio { background: #121212; border-bottom: 1px solid #27272a; color: #ffffff; }
body.light-mode .barra-dominio { background: #ffffff; border-bottom-color: #e2e8f0; color: #0f172a; }
.barra-dominio__contenido { max-width: 1140px; margin: 0 auto; padding: 10px 16px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.barra-dominio__marca { font-weight: 700; font-size: 17px; text-decoration: none; color: inherit; }
.barra-dominio__marca span { color: #3b82f6; }
.barra-dominio__enlaces { display: flex; align-items: center; gap: 16px; flex: 1; flex-wrap: wrap; }
.barra-dominio__enlaces a { color: inherit; text-decoration: none; font-size: 13px; font-weight: 600; opacity: .85; }
.barra-dominio__enlaces a:hover { opacity: 1; color: #3b82f6; }
.barra-dominio__sesion { display: flex; align-items: center; gap: 12px; }
.barra-dominio__usuario { font-size: 13px; font-weight: 600; }
.barra-dominio__login, .barra-dominio__salir {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px;
    border-radius: 50px; font-size: 13px; font-weight: 600; text-decoration: none;
    border: 1px solid #3b82f6; color: #3b82f6; background: transparent;
}
.barra-dominio__salir { border-color: #ef4444; color: #ef4444; }
.barra-dominio__login:hover { background: #3b82f6; color: #ffffff; }
.barra-dominio__salir:hover { background: #ef4444; color: #ffffff; }
</style>