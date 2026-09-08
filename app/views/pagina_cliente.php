<?php require_once("assets/comunes/menu_cliente.php"); ?>

<title>Dashboard | Edward Accesorios</title>

<main id="main">
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">DASHBOARD PRINCIPAL</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="">Inicio</a></li>
                </ol>
            </nav>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card card-custom p-4 bg-gradient-primary text-white" style="background: linear-gradient(135deg, #0A2647 0%, #144272 100%);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">Compromiso del Mes</span>
                            <h3 class="fw-bold mb-1" id="lbl-cuota-total">Cargando...</h3>
                            <p class="text-white-50 mb-0" id="lbl-info-cuotas">Espere un momento...</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">

                    
                    <div class="col-12">
                        <div class="card card-custom p-4 border-start border-4 border-primary shadow-sm">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 mb-1 fw-bold">Próximo Vencimiento</span>
                                    <h5 class="fw-bold text-dark mb-0" id="prox-nombre-equipo">Cargando equipo...</h5>
                                </div>
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill" id="prox-cuotas-num">Cuota - de -</span>
                            </div>

                            <div class="row align-items-center my-3 py-3 bg-light rounded-4 px-3">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <span class="text-muted small d-block">Monto a pagar:</span>
                                    <h3 class="fw-bold text-primary mb-0" id="prox-monto">$0.00</h3>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <span class="text-muted small d-block">Fecha límite:</span>
                                    <span class="fw-bold text-danger" id="prox-vencimiento"><i class="bi bi-clock-fill me-1"></i> Cargando...</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progreso del equipo</span>
                                    <span id="prox-progreso-texto">0% pagado</span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 5px;">
                                    <div id="prox-barra-progreso" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-12">
                        <div class="card card-custom p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Resumen de Cuotas por Mes</h5>
                                <div class="dropdown">
                                    <select id="select-mes" class="form-select bg-secondary text-white border-0 w-auto">

                                    </select>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" id="dropdown-meses">

                                    </ul>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 bg-light text-secondary d-flex align-items-center mb-4 p-3 rounded-4" role="alert">
                                <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                                <div id="texto-alerta-mes">
                                    Cargando información del mes actual...
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Equipo</th>
                                            <th>Cuota N°</th>
                                            <th>Vencimiento</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-cuotas-body">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">Cargando datos...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-4">Acciones Rápidas</h5>
                    <div class="d-grid gap-3">

                        <a href="?pagina=cliente_financiamiento" class="btn nav-item-btn">
                            <i class="bi bi-phone me-2"></i> Mis Equipos
                        </a>
                    </div>
                    <div class="mt-5 pt-4 border-top text-center">
                        <p class="small text-muted mb-0">Sesión iniciada como:</p>
                        <p class="fw-bold text-primary text-uppercase mt-1">
                            <?php
                            $cedulaMostrada = $_SESSION["cliente_cedula"] ?? $_SESSION["username"] ?? 'Invitado';
                            echo htmlspecialchars($cedulaMostrada, ENT_QUOTES, 'UTF-8');
                            
                            if (!empty($_SESSION["nombre_completo"]) && $_SESSION["nombre_completo"] !== "USUARIO") {
                                echo " - " . htmlspecialchars($_SESSION["nombre_completo"], ENT_QUOTES, 'UTF-8');
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once("assets/comunes/footer.php"); ?>
<script src="assets/js/dashboard2.js"></script>

<style>
    #main {
        margin-top: 45px !important;
        padding: 2rem;
        background: #f4f7f9;
        min-height: calc(100vh - 80px);
    }

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
</style>