<div class="modal fade" id="modalProcesarSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Procesar Orden de Servicio</h3>
                    <p class="text-muted small">Confirma el pago de la orden y registra la referencia</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="id_solicitud_input">
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="mb-3 small text-muted">
                                    <span class="me-3"><strong>Orden:</strong> <span id="num_solicitud_modal">-</span></span>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-borderless table-sm align-middle">
                                        <thead class="small text-uppercase text-muted">
                                            <tr>
                                                <th>Repuesto / Servicio</th>
                                                <th class="text-end">Cantidad</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lista_productos_solicitud">
                                            <tr><td colspan="3" class="text-center text-muted">Cargando productos...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-5">
                                    <div>Total a cobrar</div>
                                    <div id="total_modal_ver">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-primary mb-3">Confirmación de Pago</h6>
                                <div class="mb-3">
                                    <label class="form-label text-black-50">Método de Pago</label>
                                    <select id="metodo_pago" class="form-select bg-light border-secondary">
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Tarjeta">Tarjeta</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-black-50">Referencia de Pago / Banco</label>
                                    <input type="text" id="pago_referencia" class="form-control" placeholder="Ej: Ref: 123456 - Banco Prov" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnFinalizarPedido">CONFIRMAR Y FACTURAR</button>
            </div>
        </div>
    </div>
</div>
