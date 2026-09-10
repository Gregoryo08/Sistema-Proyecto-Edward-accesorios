<?php require_once("assets/comunes/menu.php"); ?>

<title>Dashboard | Edward Accesorios</title>

<main class="main m-4" id="main">
    <section id="hero" class="section mt-4 pt-4" style="height: auto;">
        <div class="mb-4 pt-4">
            <h2 class="fw-bold text-dark">DASHBOARD PRINCIPAL</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card card-custom p-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3" style="background: #eff6ff; color: #144272;">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold mb-0">INGRESOS HOY</p>
                                    <h3 class="fw-bold mb-0" id="ingresosHoy">$0.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-custom p-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3" style="background: #f0fdf4; color: #16a34a;">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div>
                                    <p class="text-muted small fw-bold mb-0">VENTAS COMPLETADAS</p>
                                    <h3 class="fw-bold mb-0" id="ventasCompletadas">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-custom p-4">
                            <h5 class="fw-bold mb-4">Actividad Reciente</h5>
                            <div class="placeholder-graph">
                                Gráfico de actividad en desarrollo...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-4">Acciones Rápidas</h5>
                    <div class="d-grid gap-3">
                        <a href="?pagina=ventas" class="btn btn-acciones">
                            <i class="bi bi-plus-circle-fill me-2"></i> Nueva Venta
                        </a>
                        <a href="?pagina=productos" class="btn nav-item-btn">
                            <i class="bi bi-box-seam me-2"></i> Inventario
                        </a>
                    </div>
                    <div class="mt-5 pt-4 border-top text-center">
                        <p class="small text-muted mb-0">Sesión iniciada como:</p>
                        <p class="fw-bold text-primary text-uppercase mt-1">
                            <?php
                            echo htmlspecialchars($_SESSION["username"] ?? 'Invitado', ENT_QUOTES, 'UTF-8');
                            if (!empty($_SESSION["nombre_completo"]) && $_SESSION["nombre_completo"] !== "USUARIO") {
                                echo " - " . htmlspecialchars($_SESSION["nombre_completo"], ENT_QUOTES, 'UTF-8');
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once("assets/comunes/footer.php"); ?>
<script src="assets/js/dashboard.js"></script>

<style>
    .card-custom {
        background: #ffffff !important;
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 10px 25px rgba(10, 38, 71, 0.05) !important;
        transition: transform 0.3s ease;
    }

    .card-custom:hover {
        transform: translateY(-5px);
    }

    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .placeholder-graph {
        height: 250px;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-weight: 500;
    }

    .btn-acciones {
        background: #144272 !important;
        color: #ffffff !important;
        padding: 1rem !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        text-align: left !important;
        border: none !important;
        transition: all 0.3s ease !important;
    }

    .btn-acciones:hover {
        background: #0A2647 !important;
        box-shadow: 0 6px 20px rgba(20, 66, 114, 0.3) !important;
    }

    .nav-item-btn {
        background: #ffffff;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 600;
        padding: 1rem;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .nav-item-btn:hover {
        background: #f8fafc;
        border-color: #144272;
        color: #144272;
        transform: translateY(-2px);
    }

    .text-primary {
        color: #144272 !important;
    }

    /* Corrección para el contenedor del gráfico */
    .card-actividad {
        background-color: var(--surface-color) !important;
        border: 1px solid var(--border-color) !important;
    }
</style>