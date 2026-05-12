<div class="modal fade" id="modalProcesarSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: #1a1a1a; color: white; border: 1px solid #333;">
            <div class="modal-header" style="border-bottom: 1px solid #333;">
                <h5 class="modal-title">Procesar Pedido #<span id="num_solicitud_modal"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 border-end border-secondary">
                        <h6 class="text-primary mb-3">Resumen de Productos</h6>
                        <div id="lista_productos_solicitud" class="mb-3"></div>
                        <div class="d-flex justify-content-between px-2">
                            <span class="fw-bold">TOTAL A COBRAR:</span>
                            <span class="text-success fw-bold" id="total_modal_ver"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Confirmación de Pago y Envío</h6>
                        <form id="formConfirmarVenta">
                            <input type="hidden" id="id_solicitud_input">
                            <div class="mb-3">
                                <label class="form-label text-white-50">Referencia de Pago / Banco</label>
                                <input type="text" id="pago_referencia" class="form-control bg-dark text-white border-secondary" placeholder="Ej: Ref: 123456 - Banco Prov" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white-50">Tipo de Entrega / Tracking</label>
                                <select id="envio_tipo" class="form-select bg-dark text-white border-secondary">
                                    <option value="Delivery">Delivery (Local)</option>
                                    <option value="Envio Nacional">Envío Nacional (Encomienda)</option>
                                    <option value="Retiro en Tienda">Retiro en Persona</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white-50">Nota Adicional de Envío</label>
                                <textarea id="envio_notas" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Ej: Motorizado enviado / Guía Zoom #..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #333;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button id="btnFinalizarPedido" class="btn btn-success px-4">CONFIRMAR Y FACTURAR</button>
            </div>
        </div>
    </div>
</div>