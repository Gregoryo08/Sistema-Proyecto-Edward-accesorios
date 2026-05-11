<?php require_once("assets/comunes/menu.php"); ?>


<title>Dashboard | Edward Accesorios</title>

<main id="main">
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="fw-bold" style="color: var(--text-main);">Visión General</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Inicio</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card card-custom p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; background: #eff6ff; color: var(--accent-color);">
                                    <i class="bi bi-currency-dollar fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted small fw-bold mb-0 uppercase">INGRESOS HOY</p>
                                    <h3 class="fw-bold mb-0" id="ingresosHoy" style="color: var(--text-main);">$0.00</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-custom p-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; background: #f0fdf4; color: #16a34a;">
                                    <i class="bi bi-cart-check fs-4"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted small fw-bold mb-0">VENTAS COMPLETADAS</p>
                                    <h3 class="fw-bold mb-0" id="ocupacionActual" style="color: var(--text-main);">0%</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card card-custom p-4">
                            <h5 class="fw-bold mb-4" style="color: var(--text-main);">Actividad Reciente</h5>
                            <div style="height: 250px; background: #f8fafc; border: 2px dashed var(--border-color);"
                                class="rounded d-flex align-items-center justify-content-center text-muted">
                                Gráfico de actividad en desarrollo...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-4" style="color: var(--text-main);">Acciones Rápidas</h5>

                    <div class="d-grid gap-3">
                        <a href="?pagina=alquiler" class="btn btn-acciones w-100 text-start d-flex align-items-center p-3"
                            style="background: #eff6ff; color: var(--accent-color); border: none; border-radius: 8px; font-weight: 600; text-decoration: none;">
                            <i class="bi bi-plus-circle-fill me-3"></i> Nueva Venta
                        </a>

                        <a href="?pagina=aseo" class="btn w-100 text-start d-flex align-items-center p-3"
                            style="background: #f8fafc; color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 600; text-decoration: none;">
                            <i class="bi bi-box-seam me-3"></i> Inventario
                        </a>


                    </div>

                    <div class="mt-5 pt-4 border-top text-center">
                        <p class="small text-muted mb-0">Sesión iniciada como:</p>
                        <p class="fw-bold" style="color: var(--accent-color); text-transform: uppercase;">
                            <?php
                            echo $_SESSION["username"];
                            echo isset($_SESSION["nombre_completo"]) ? " - " . $_SESSION["nombre_completo"] : "";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    #main {
        margin-top: var(--header-height);
        transition: var(--transition);
        padding: 2rem;
        min-height: calc(100vh - var(--header-height));
    }


    .navmenu:hover~#main,
    .navmenu:hover+#main {
        margin-left: calc(var(--nav-width-open) - var(--nav-width-closed));
    }


    .card-custom {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .card-custom:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }



    .sidebar-active #main,
    .toggle-sidebar #main {
        margin-left: 260px;
    }


    @media (max-width: 1199px) {
        #main {
            margin-left: 0 !important;
            padding: 20px 15px;
        }
    }


    .card-metric {
        border: none;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.05);
        height: 100%;
    }

    .icon-box {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .bg-soft-success {
        background: #e0f3e9;
        color: #198754;
    }

    .bg-soft-info {
        background: #e0f1ff;
        color: #0dcaf0;
    }

    .btn-quick-action {
        border-radius: 8px;
        padding: 12px;
        font-weight: 600;
        border: none;
        transition: 0.2s;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>