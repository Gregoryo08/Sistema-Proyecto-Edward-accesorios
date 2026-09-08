<div class="modal fade modal-comun" id="modalReponerProducto" tabindex="-1" role="dialog" aria-labelledby="modalReponerProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light position-relative">
                <h5 class="modal-title text-center w-100 fw-bold" id="modalReponerProductoLabel">
                    <i class="bi bi-boxes me-2 text-success"></i>Registrar Entrada de Productos
                </h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReponerProducto">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="proveedorSelect" class="form-label fw-bold">
                                    <i class="bi bi-person-workspace me-1 text-secondary"></i> Proveedor
                                </label>
                                <select name="proveedorSelect" class="form-select" id="proveedorSelect">
                                    <option value="" selected disabled>Seleccione un proveedor...</option>
                                </select>
                                <div id="error_proveedorSelect" class="msg-error">Seleccione un proveedor válido</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="productoSelect" class="form-label fw-bold">
                                    <i class="bi bi-basket me-1 text-primary"></i> Añadir Productos
                                </label>
                                <select name="productoSelect" class="form-select" id="productoSelect">
                                    <option value="" selected disabled>Seleccione un producto...</option>
                                </select>
                                <div id="error_productoSelect" class="msg-error">Seleccione un producto válido</div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover align-middle text-center" id="tablaEntradaProductos">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cod.</th>
                                    <th>Productos</th>
                                    <th style="width: 150px;">Cantidad</th>
                                    <th style="width: 150px;">Garantia</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="guardarEntrada" disabled>
                    <i class="bi bi-check-circle me-1"></i> Confirmar Entrada
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-comun" id="modalDetalleEntrada" tabindex="-1" role="dialog" aria-labelledby="modalDetalleEntradaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light position-relative">
                <h5 class="modal-title text-center w-100 fw-bold" id="modalDetalleEntradaLabel">
                    <i class="bi bi-box-seam me-1"></i>Detalles de la Entrada
                </h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center" id="detalleEntrada">
                        <thead class="table-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Ingresada</th>
                                <th>Garantía</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-comun" id="modalModificarEntrada" tabindex="-1" role="dialog" aria-labelledby="modalModificarEntradaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light position-relative">
                <h5 class="modal-title text-center w-100 fw-bold" id="modalModificarEntradaLabel">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>Modificar Producto de Entrada
                </h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModificarRenglon">
                    <input type="hidden" id="id_entrada">
                    <input type="hidden" id="producto_entrada">
                    <input type="hidden" id="cantidadOLD_entrada">
                    <input type="hidden" id="garantiaOLD_entrada">

                    <div class="form-group mb-3">
                        <label for="cantidadEntradaM" class="form-label fw-bold">Nueva Cantidad *</label>
                        <input type="number" class="form-control solo-numeros" id="cantidadEntradaM" placeholder="Ingresa la nueva cantidad" min="1" required>
                        <div id="error_cantidadEntradaM" class="msg-error">Ingrese una cantidad válida mayor a 0</div>
                    </div>

                    <div class="form-group mb-3" id="campoGarantiaModificar" style="display: none;">
                        <label for="garantiaEntradaM" class="form-label fw-bold">
                            <i class="bi bi-shield-check me-1 text-success"></i> Garantía (días)
                        </label>
                        <input type="number" class="form-control solo-numeros" id="garantiaEntradaM" placeholder="Días de garantía" min="1">
                        <div id="error_garantiaEntradaM" class="msg-error">Formato de días incorrecto</div>
                        <small class="text-muted d-block mt-1">Dejar vacío si no aplica garantía</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="modificarDatosE2" class="btn btn-warning text-white rounded-pill px-4 shadow-sm">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>


    <style>
        .form-select.is-valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 2.25rem center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }

        .is-valid {
            border-color: #198754 !important;
        }

        .msg-success {
            display: none;
            color: #198754;
            font-size: 0.7rem;
            font-weight: bold;
            margin-top: 5px;
        }


        .select2-container--valid .select2-selection--single {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 2.25rem center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }


        .msg-error {
            display: none;
            color: #dc3545 !important;
            font-size: 0.75rem;
            font-weight: 700;
            margin-top: 5px;
            height: 1rem;
            visibility: visible !important;
        }

        .is-invalid+.select2-container .select2-selection {
            border-color: #dc3545 !important;
        }

        .is-valid+.select2-container .select2-selection {
            border-color: #198754 !important;
        }
    </style>