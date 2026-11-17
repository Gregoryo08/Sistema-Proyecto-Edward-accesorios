<div class="modal fade" id="modalVerOrden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Detalle de la Orden</h3>
                    <p class="text-muted small">Información completa de la orden de trabajo</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase">Orden</div>
                        <div class="fw-bold fs-5" id="orden_id">-</div>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase">Fecha</div>
                        <div class="fw-bold" id="orden_fecha">-</div>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase">Estado</div>
                        <div class="fw-bold" id="orden_estado">-</div>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted text-uppercase">Cliente</div>
                        <div class="fw-bold" id="orden_cliente">-</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted text-uppercase mb-1">Observaciones</div>
                    <div class="bg-light rounded-3 p-3" id="orden_observaciones">-</div>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead class="small text-uppercase text-muted">
                            <tr>
                                <th>Repuesto / Servicio</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="orden_items">
                            <tr><td colspan="4" class="text-center text-muted">No hay repuestos o servicios registrados.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="small text-muted text-uppercase">Total repuestos</div>
                        <div class="fw-bold fs-5" id="orden_repuestos_total">$ 0.00</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted text-uppercase">Mano de obra</div>
                        <div class="fw-bold fs-5" id="orden_reparacion_total">$ 0.00</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted text-uppercase">Total orden</div>
                        <div class="fw-bold fs-5" style="color: #198754;" id="orden_total">$ 0.00</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>