<div class="modal fade" id="modalRegistroFinanciamiento" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" >Nuevo Financiamiento</h3>
                    <p >Vincula un cliente a un dispositivo y configura el plan de pago</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioRegistroFinanciamiento">
                    <input type="hidden" name="id_producto" id="input_id_producto">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cliente</label>
                            <select name="cedula_persona" id="cedula_persona" data-field="cliente" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Seleccionar Cliente...</option>
                            </select>
                            <div id="error_cedula_persona" class="msg-error">Seleccione un cliente válido</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Dispositivo (IMEI)</label>
                            <select name="id_unidad" id="id_telefono" data-field="dispositivo" class="form-select rounded-3 border-light-subtle bg-light" required>
                                <option value="" selected disabled>Seleccionar Teléfono...</option>
                            </select>
                            <div id="error_id_telefono" class="msg-error">Seleccione un dispositivo válido</div>
                        </div>
                        <hr class="my-3 opacity-25">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Monto Total ($)</label>
                            <input type="text" name="monto_total" id="monto_total" data-field="monto" class="form-control solo-numeros" placeholder="0.00" required readonly>
                            <div id="error_monto_total" class="msg-error">Formato incorrecto</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pago Inicial ($)</label>
                            <input type="text" name="pago_inicial" id="pago_inicial" data-field="pago" class="form-control solo-numeros" placeholder="0.00" required>
                            <div id="error_pago_inicial" class="msg-error">Formato incorrecto</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nro. de Cuotas</label>
                            <input type="text" inputmode="numeric" maxlength="2" name="cantidad_cuotas" id="cantidad_cuotas" data-field="cuotas" class="form-control solo-numeros" placeholder="Ej: 12" required>
                            <div id="error_cantidad_cuotas" class="msg-error">Formato incorrecto</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Día de Pago Mensual</label>
                            <input type="text" inputmode="numeric" maxlength="2" name="dia_pago" id="dia_pago" data-field="dia" class="form-control solo-numeros" placeholder="Ej: 05" required>
                            <div id="error_dia_pago" class="msg-error">Formato incorrecto</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" data-field="fecha" class="form-control"
                                min="<?php echo date('Y-m-d'); ?>"
                                max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>"
                                required>
                            <div id="error_fecha_inicio" class="msg-error">Seleccione una fecha</div>
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
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h3 class="fw-black mb-0 text-white">Seguimiento de Cuotas</h3>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tablaSeguimientoCuotas">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>Nro</th>
                                <th>Vencimiento</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Método / Banco</th>
                                <th>Fecha de Pago</th>
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

<<div class="modal fade" id="modalModificarFinanciamiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Modificar Financiamiento</h3>
                    <p class="text-muted small">Actualiza los datos del plan de pago y el equipo</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioModificarFinanciamiento">
                    <input type="hidden" name="id_financiamiento" id="mod_id_financiamiento">
                    <input type="hidden" name="id_unidad" id="mod_id_unidad">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Información del Cliente</label>
                            <input type="text" id="mod_info_cliente" class="form-control rounded-3 border-light-subtle bg-light" disabled>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Producto / Equipo</label>
                            <select name="id_producto" id="mod_id_producto" class="form-select rounded-3 border-light-subtle" required>
                            </select>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Monto Total ($)</label>
                            <input type="text" name="monto_total" id="mod_monto_total" class="form-control solo-numeros" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pago Inicial ($)</label>
                            <input type="text" name="pago_inicial" id="mod_pago_inicial" class="form-control solo-numeros" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nro. de Cuotas</label>
                            <input type="text" inputmode="numeric" maxlength="2" name="cantidad_cuotas" id="mod_cantidad_cuotas" class="form-control solo-numeros" placeholder="Ej: 12" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Día de Pago Mensual</label>
                            <input type="text" inputmode="numeric" maxlength="2" name="dia_pago" id="mod_dia_pago" class="form-control solo-numeros" placeholder="Ej: 05" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="mod_fecha_inicio" class="form-control" required>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                                <span class="small fw-bold text-warning text-uppercase d-block mb-1">Nueva Cuota Estimada</span>
                                <h4 class="mb-0 text-warning" id="mod_cuota_estimada">$ 0.00</h4>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold shadow text-white" id="btn_guardar_modificacion">Guardar Cambios</button>
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

        /* Estilos para el estado de éxito */
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



        /* Estilo para el contenedor de Select2 cuando es válido */
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
            /* Fuerza altura */
            visibility: visible !important;
        }

        /* Para componentes tipo Select2 */
        .is-invalid+.select2-container .select2-selection {
            border-color: #dc3545 !important;
        }

        .is-valid+.select2-container .select2-selection {
            border-color: #198754 !important;
        }
    </style>