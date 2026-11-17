<div class="modal fade" id="modal_pagar_cuota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago Móvil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Datos para Pago Móvil:</strong><br>
                    Banco: Banco de Venezuela<br>
                    RIF: J-00000000-0<br>
                    Teléfono: 0414-0000000
                </div>
                <form id="formularioPagoCuota">
                    <input type="hidden" id="id_cuota_pago" name="id_cuota">
                    <input type="hidden" name="id_metodo" value="3">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block text-muted">Monto a pagar</label>
                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <div class="text-primary">
                                <small class="d-block text-uppercase fw-bold">Dólares</small>
                                <input type="text" class="form-control form-control-lg text-center fw-bold" id="monto_pago" name="monto" readonly style="width: 150px; border: none; background: transparent; font-size: 1.5rem;">
                            </div>
                            <div class="border-start ps-3 text-success">
                                <small class="d-block text-uppercase fw-bold">Bolívares</small>
                                <span class="fs-4 fw-bold" id="monto_bs">0.00</span>
                                <span class="fs-6"> Bs</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banco Origen</label>
                        <select class="form-select" id="id_banco_pago" name="id_banco" required></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número de Referencia</label>
                        <input type="text" class="form-control" name="referencia" id="referencia" maxlength="20" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btn_confirmar_pago">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalHistorialCuotas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Cuotas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tablaHistorial">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Vencimiento</th>
                                <th>Estado</th>
                                <th>Monto Pagado</th>
                                <th>Fecha Pago</th>
                                <th>Banco/Método</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>