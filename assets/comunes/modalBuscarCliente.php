<div class="modal fade" id="modalBuscarCliente" tabindex="-1" aria-labelledby="modalBuscarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="modalBuscarClienteLabel">
                    <i class="fa-solid fa-users text-primary me-2"></i> Seleccionar Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-4 align-items-center">
                    <div class="col-md-7 col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" id="searchClientInput" placeholder="Buscar por nombre o cédula..." onkeyup="filtrarClientes()">
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 d-flex justify-content-md-end justify-content-center">
                        <button class="btn btn-primary fw-semibold shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2"
                            data-bs-toggle="modal"
                            data-bs-target="#modalRegistrarCliente">
                            <i class="fa-solid fa-user-plus"></i> Registrar Cliente
                        </button>
                    </div>
                </div>

                <div class="table-responsive rounded-3 border" style="max-height: 350px;">
                    <table class="table table-hover align-middle mb-0" id="tablaClientesModal">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="py-3 ps-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Cédula</th>
                                <th class="py-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Cliente</th>
                                <th class="py-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Teléfono</th>
                                <th class="py-3 text-center pe-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-2 px-3" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalRegistrarCliente" tabindex="-1" aria-labelledby="modalRegistrarClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="modalRegistrarClienteLabel"><i class="fa-solid fa-user-plus text-primary me-2"></i>Registro de Cliente</h5>
                <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-target="#modalBuscarCliente" aria-label="Volver"></button>
            </div>
            <form id="formRegistrarCliente">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Completa los campos obligatorios para registrar al cliente en el sistema.</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cédula</label>
                            <div class="input-group">
                                <select name="prefijo" class="form-select bg-light border-light-subtle" id="prefijo" style="max-width: 80px;">
                                    <option value="V-">V-</option>
                                    <option value="E-">E-</option>
                                </select>
                                <input type="text" class="form-control bg-light border-light-subtle" id="cedula" placeholder="Ej: 12345678">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="nombre" placeholder="Nombre completo">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Apellido</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="apellido" placeholder="Apellido completo">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <div class="input-group">
                                <select name="operadora" class="form-select bg-light border-light-subtle" id="operadora" style="max-width: 90px;">
                                    <option value="0412">0412</option>
                                    <option value="0414">0414</option>
                                    <option value="0416">0416</option>
                                    <option value="0422">0422</option>
                                    <option value="0424">0424</option>
                                    <option value="0426">0426</option>
                                </select>
                                <input type="tel" class="form-control bg-light border-light-subtle" id="telefono" placeholder="1234567 (Opcional)">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light justify-content-between py-3">
                    <button type="button" class="btn btn-outline-secondary fw-medium px-3 rounded-2" data-bs-toggle="modal" data-bs-target="#modalBuscarCliente">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver
                    </button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm rounded-3">
                        Guardar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script src="assets/js/validaciones/ventas/ventas2.js"></script>