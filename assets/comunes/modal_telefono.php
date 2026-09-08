<div class="modal fade" id="modalRegistroTelefono" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Nuevo Dispositivo</h3>
                    <p class="text-muted small">Ingresa las especificaciones del equipo</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioRegistroTelefono">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Marca</label>
                            <select name="marca" id="marca" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Cargando marcas...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Modelo</label>
                            <input type="text" name="modelo" id="modelo" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: Galaxy S23" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Almacenamiento</label>
                            <input type="text" name="almacenamiento" id="almacenamiento" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: 128GB" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Memoria RAM</label>
                            <input type="text" name="ram" id="ram" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: 8GB" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Número de IMEI</label>
                            <input type="text" name="imei" id="imei" class="form-control rounded-3 border-light-subtle bg-light fs-5 fw-bold text-dark" placeholder="00000000000000" required>
                            <small id="mensajeModulo" style="color: red; display: none;"></small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary " id="btn_registrar">Registrar Equipo</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarTelefono" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Actualizar Datos</h3>
                    <p class="text-muted small">Editando especificaciones del dispositivo</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioModificarTelefono">
                    <input type="hidden" name="id" id="id_telefono_modificar">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Marca</label>
                            <select name="marca" id="marca_modificar" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Cargando marcas...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Modelo</label>
                            <input type="text" name="modelo" id="modelo_modificar" class="form-control rounded-3 border-light-subtle bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Almacenamiento</label>
                            <input type="text" name="almacenamiento" id="almacenamiento_modificar" class="form-control rounded-3 border-light-subtle bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Memoria RAM</label>
                            <input type="text" name="ram" id="ram_modificar" class="form-control rounded-3 border-light-subtle bg-light" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Número de IMEI</label>
                            <input type="text" name="imei" id="imei_modificar" class="form-control rounded-3 border-light-subtle bg-light fs-5 fw-bold " readonly>
                            <small id="mensajeModuloModificar" style="color: red; display: none;"></small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow" id="modificarDatos">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>