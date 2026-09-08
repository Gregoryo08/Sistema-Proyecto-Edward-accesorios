<!-- Modal: Registrar Orden Técnico -->
<div class="modal fade" id="modalOrdenTecnico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Registrar Orden de Servicio</h3>
                    <p class="text-muted small">El técnico completa los datos y el sistema guarda el registro.</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioOrdenTecnico">
                    <input type="hidden" id="orden_id_tecnico" name="orden_id">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cliente</label>
                            <input type="text" name="cliente" id="orden_cliente_tecnico" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Nombre del cliente" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Marca registrada</label>
                            <select name="id_marca" id="orden_marca_tecnico" class="form-select rounded-3 border-light-subtle bg-light">
                                <option value="">Seleccione una marca</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado de la orden</label>
                            <select name="estado" id="orden_estado_tecnico" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="0" selected>Pendiente</option>
                               
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Equipo / Modelo</label>
                            <input type="text" name="equipo" id="orden_equipo_tecnico" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: iPhone 12" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">IMEI / Serie</label>
                            <input type="text" name="imei" id="orden_imei_tecnico" class="form-control rounded-3 border-light-subtle bg-light" placeholder="00000000000000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tiempo estimado</label>
                            <input type="time" name="horas_estimadas" id="orden_horas_estimadas_tecnico" class="form-control rounded-3 border-light-subtle bg-light" value="00:00">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Observaciones</label>
                            <textarea name="observaciones" id="orden_observaciones_tecnico" class="form-control rounded-3 border-light-subtle bg-light" rows="3" placeholder="Descripción del problema, nota para el cliente..."></textarea>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">Repuestos</h6>
                                <p class="small text-muted mb-0">Agrega los repuestos usados y sus precios.</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarRepuesto">Agregar repuesto</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="small text-uppercase text-muted">
                                    <tr>
                                        <th>Repuesto</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">Precio unitario</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-center">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="orden_repuestos_rows">
                                    <tr class="orden-repuesto-row">
                                        <td><input type="text" class="form-control form-control-sm repuesto-nombre" placeholder="Ej: Pantalla"></td>
                                        <td><input type="number" min="1" value="1" class="form-control form-control-sm repuesto-cantidad"></td>
                                        <td><input type="number" min="0" step="0.01" value="0.00" class="form-control form-control-sm repuesto-precio"></td>
                                        <td class="text-end repuesto-subtotal">$ 0.00</td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-repuesto">x</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Total repuestos</label>
                            <input type="text" id="orden_repuestos_total_tecnico" class="form-control rounded-3 border-light-subtle bg-light fw-bold" value="$ 0.00" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Total orden</label>
                            <input type="text" id="orden_total_tecnico" class="form-control rounded-3 border-light-subtle bg-light fw-bold" value="$ 0.00" readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnGuardarOrdenTecnico">Guardar orden</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalConfirmarEliminarOrden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalConfirmarEliminarOrdenLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">¿Estás seguro de eliminar esta orden? Esta acción no se puede deshacer.</p>
                <input type="hidden" id="ordenEliminarId">
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnConfirmarEliminarOrden">Eliminar</button>
            </div>
        </div>
    </div>
</div>
