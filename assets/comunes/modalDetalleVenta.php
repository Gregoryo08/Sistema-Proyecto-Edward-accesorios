<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-light" id="modalDetalleVentaLabel"><i class="fa-solid fa-receipt me-2"></i> Detalle de Venta <span id="detIdVenta" class="text-light"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4 p-2 bg-light rounded-3 small">
                    <div class="col-md-6">
                        <strong>Fecha / Hora:</strong> <span id="detFecha"></span><br>
                        <strong>Origen:</strong> <span id="detOrigen" class="text-capitalize"></span><br>
                        <strong>Estado:</strong> <span id="detEstado"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Cliente:</strong> <span id="detCliente"></span><br>
                        <strong>Operador:</strong> <span id="detOperador"></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered text-center mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetallesProductos">
                            </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Total General:</td>
                                <td id="detTotal" class="text-success text-end pe-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
