<div class="modal fade" id="modalRegistroVenta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Venta Presencial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7 border-end">
                        <label class="form-label fw-bold">Buscar Producto</label>
                        <select id="select_producto_venta" class="form-control"></select>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Producto</th><th>Cant</th><th>Subtotal</th><th></th></tr>
                                </thead>
                                <tbody id="cuerpoVentaTienda"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center mb-3">
                            <h6 class="text-muted">Total a Pagar</h6>
                            <h2 class="text-success fw-bold" id="total_mostrador">$0.00</h2>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select id="metodo_pago_tienda" class="form-select"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Referencia</label>
                            <input type="text" id="referencia_tienda" class="form-control">
                        </div>
                        <button id="btn_finalizar_tienda" class="btn btn-primary w-100 py-2">Completar Venta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalleVenta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content  text-white ">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-receipt text-info"></i> Detalle de la Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-dark table-hover text-center mb-0" style="width: 100%;">
                        <thead class="text-info">
                            <tr>
                                <th class="text-start">Producto</th>
                                <th>Cant</th>
                                <th>Precio</th>
                                <th style="color: white;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoDetalleVenta">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-info fw-bold" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>