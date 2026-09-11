<?php require_once('assets/comunes/menu.php'); ?>
<?php require_once('assets/comunes/modalBitacora.php'); ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<main class="main m-4" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <h2 class="text-center mb-4">Consultar Bitácora</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filtroRoles" class="form-label fw-bold">Filtrar por Rol:</label>
                        <select id="filtroRoles" class="form-select select2" data-placeholder="Seleccione un Rol">
                            <option value="">Todos los Roles</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="selectUsuario" class="form-label fw-bold">Filtrar por Usuario:</label>
                        <select id="selectUsuario" class="form-select select2" data-placeholder="Seleccione un Usuario">
                            <option value="">Todos los Usuarios</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="selectAccion" class="form-label fw-bold">Filtrar por Acción:</label>
                        <select id="selectAccion" class="form-select select2" data-placeholder="Seleccione una Acción">
                            <option value="">Todas las Acciones</option>
                            <option value="Iniciar Sesion">Iniciar Sesión</option>
                            <option value="Cerrar Sesion">Cerrar Sesión</option>
                            <option value="Registrar">Registrar</option>
                            <option value="Modificar">Modificar</option>
                            <option value="Eliminar">Eliminar</option>
                            <option value="Consultar">Consultar</option>
                            <option value="Acceder">Acceder</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button id="btnFiltrar" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <button id="btnLimpiar" class="btn btn-outline-secondary w-100">
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaBitacora" class="table table-striped table-bordered text-center w-100">
            </table>
        </div>
    </section>

    <div class="modal fade" id="modalDetallesBitacora" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Cambio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Dato Antiguo:</h6>
                    <p id="modalAntiguo" class="bg-light p-2 rounded"></p>
                    <hr>
                    <h6>Dato Nuevo:</h6>
                    <p id="modalNuevo" class="bg-light p-2 rounded"></p>
                </div>
            </div>
        </div>
    </div>
</main>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<?php require_once('assets/comunes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="assets/js/validaciones/bitacora/administrar_bitacora.js"></script>