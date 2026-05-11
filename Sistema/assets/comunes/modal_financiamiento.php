<div class="modal fade" id="modalRegistroFinanciamiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Nuevo Financiamiento</h3>
                    <p class="text-muted small">Vincula un cliente a un dispositivo y configura el plan de pago</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioRegistroFinanciamiento">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cliente</label>
                            <select name="cedula_cliente" id="cedula_cliente" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Seleccionar Cliente...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Dispositivo (IMEI)</label>
                            <select name="id_telefono" id="id_telefono" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Seleccionar Teléfono...</option>
                            </select>
                        </div>

                        <hr class="my-3 opacity-25">

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Monto Total ($)</label>
                            <input type="number" step="0.01" name="monto_total" id="monto_total" class="form-control rounded-3 border-light-subtle bg-light" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pago Inicial ($)</label>
                            <input type="number" step="0.01" name="pago_inicial" id="pago_inicial" class="form-control rounded-3 border-light-subtle bg-light" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nro. de Cuotas</label>
                            <input type="number" name="cantidad_cuotas" id="cantidad_cuotas" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: 12" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Día de Pago Mensual</label>
                            <input type="number" name="dia_pago" id="dia_pago" min="1" max="31" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: 05" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control rounded-3 border-light-subtle bg-light" required>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                                <span class="small fw-bold text-primary text-uppercase d-block mb-1">Cálculo de Cuota Estimada</span>
                                <h4 class="mb-0 text-primary" id="cuota_estimada">$ 0.00</h4>
                            </div>
                        </div>

                        <div class="col-12 mt-3" id="contenedor_ia" style="display:none;">
                            <div class="p-3 rounded-4 shadow-sm" style="background-color: #000910; border-left: 4px solid #0dcaf0;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="spinner-grow spinner-grow-sm text-info me-2" role="status" id="ia_cargando" style="display:none;"></div>
                                    <h6 class="text-info mb-0 fw-bold"><i class="bi bi-cpu-fill"></i> Evaluador Inteligente de Confiabilidad (RCI-01)</h6>
                                </div>
                                <div class="row text-center text-white">
                                    <div class="col-4 border-end border-secondary border-opacity-50">
                                        <small class="d-block text-muted" style="font-size: 0.65rem;">CONFIANZA</small>
                                        <span class="fw-bold h5 mb-0" id="ia_puntaje">0%</span>
                                    </div>
                                    <div class="col-4 border-end border-secondary border-opacity-50">
                                        <small class="d-block text-muted" style="font-size: 0.65rem;">NIVEL</small>
                                        <div class="mt-1"><span class="badge" id="ia_nivel">---</span></div>
                                    </div>
                                    <div class="col-4">
                                        <small class="d-block text-muted" style="font-size: 0.65rem;">CUOTAS RECOM.</small>
                                        <span class="fw-bold h5 mb-0" id="ia_cuotas">0</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-center text-white">
                                    <small id="ia_mensaje" class="fst-italic" style="font-size: 0.85rem;"></small>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow" id="btn_registrar">Registrar Financiamiento</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSeguimientoPagos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Seguimiento de Cuotas</h3>
                    <p class="text-muted small" id="info_cliente_seguimiento">Cliente: - | Equipo: -</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tablaSeguimientoCuotas">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Nro</th>
                                <th>Vencimiento</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Método / Banco</th> <th>Fecha de Pago</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoSeguimiento">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


















<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Registrar Pago</h3>
                    <p class="text-muted small" id="detalle_cuota_pago">Cuota Nro: - | Monto: -</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioPagoCuota">
                    <input type="hidden" name="id_cuota" id="id_cuota_pago">
                    <input type="hidden" name="id_financiamiento" id="id_finan_pago">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Método de Pago</label>
                            <select name="id_metodopago" id="id_metodopago" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Seleccione método...</option>
                            </select>
                        </div>

                        <div class="col-12" id="contenedor_banco" style="display: none;">
                            <label class="form-label small fw-bold text-muted text-uppercase">Banco Destino</label>
                            <select name="id_banco" id="id_banco" class="form-select rounded-3 border-light-subtle bg-light">
                                <option value="" selected disabled>Seleccione banco...</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Monto a Pagar ($)</label>
                            <input type="number" step="0.01" name="monto_pagado" id="monto_pago_input" class="form-control rounded-3 border-light-subtle bg-light fs-4 fw-bold text-primary" required>
                        </div>

                        <div class="col-12 bg-light p-3 rounded-4 border-start border-4 border-success shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-5">
                                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Tasa BCV</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text border-0 bg-transparent ps-0">x</span>
                                        <input type="number" step="0.01" id="tasa_dia" class="form-control border-0 bg-transparent fw-bold p-0 shadow-none" style="font-size: 1.1rem;" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-7 text-end border-start">
                                    <label class="small fw-bold text-muted text-uppercase d-block mb-1">Total en Bolívares</label>
                                    <h4 class="mb-0 fw-black text-success" id="monto_bs_calculado">0.00 Bs</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Referencia / Nota</label>
                            <input type="text" name="referencia" class="form-control rounded-3 border-light-subtle bg-light" placeholder="Ej: Pago móvil Prov. 1234">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow" id="btn_confirmar_pago">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>