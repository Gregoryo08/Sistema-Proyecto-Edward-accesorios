<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="min-width: 40%;">
        <div class="modal-content shadow-lg rounded-3">
            
            <div class="modal-header bg-light py-2 px-3">
                <h6 class="modal-title fw-bold text-dark" id="paymentModalLabel">Registrar Pago de Venta</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="paymentForm" class="d-flex flex-column" style="overflow: hidden;">
                
                <div class="modal-body pb-2">

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-4">
                            <div class="border border-secondary rounded-3 p-2 h-100">
                                <span class="text-uppercase fw-semibold text-muted d-block" style="font-size: 10px; letter-spacing: 0.05em;">Total Orden</span>
                                <span class="fs-5 fw-bold d-block mt-1 text-dark" id="totalAmountDollar">$0.00</span>
                                <span class="text-muted d-block" style="font-size: 10px;" id="totalAmount">0.00 bs</span>
                            </div>
                        </div>
                        
                        <div class="col-4">
                            <div class="border border-success rounded-3 p-2 h-100" style="box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.2);">
                                <span class="text-uppercase fw-semibold text-success d-block" style="font-size: 10px; letter-spacing: 0.05em;">Total Abonado</span>
                                <span class="fs-5 fw-bold d-block mt-1 text-success" id="mTotalAbonado">$0.00</span>
                                <span class="text-muted d-block" style="font-size: 10px;" id="mTotalAbonadoBs">0.00 bs</span>
                            </div>
                        </div>
                        
                        <div class="col-4">
                            <div class="border border-warning rounded-3 p-2 h-100" id="cardRestante">
                                <span class="text-uppercase fw-semibold text-warning d-block" style="font-size: 10px; letter-spacing: 0.05em;" id="lblRestante">Por Pagar</span>
                                <span class="fs-5 fw-bold d-block mt-1 text-warning" id="mTotalRestante">$0.00</span>
                                <span class="text-muted d-block" style="font-size: 10px;" id="mTotalRestanteBs">0.00 bs</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-2" style="border: 1px solid #334155;">
                        <h5 class="fw-bold text-uppercase mb-3 d-flex align-items-center gap-2" style="font-size: 12px; color: #4f46e5; letter-spacing: 0.05em;">
                            <i class="fa-solid fa-plus-circle" style="color: #4f46e5;"></i> Registrar Pago
                        </h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-1 fw-medium" style="font-size: 12px;">Método de Pago</label>
                                <select name="paymentMethod[]" class="form-select" style="border-color: #475569; font-size: 14px; padding: 10px 12px;">
                                </select>
                            </div>

                            <div class="col-md-6" id="inputReferenciaCol">
                                <label class="form-label mb-1 fw-medium" style="font-size: 12px;">Nro. de Referencia</label>
                                <input type="text" id="refPago" placeholder="Opcional / Nro de comprobante" class="form-control" style="border-color: #475569; font-size: 14px; padding: 10px 12px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0 fw-medium text-dark" style="font-size: 12px;">Monto a Registrar</label>
                            </div>
                            
                            <div class="input-group">
                                <span class="input-group-text fw-bold" id="simboloMonedaInput" style="border-color: #475569;">$</span>
                                <input type="text" id="montoPagoInput" step="0.01" class="form-control text-end fw-bold" placeholder="0.00" style="border-color: #475569; font-size: 20px; padding: 10px 15px;">
                                <span class="input-group-text fw-semibold text-muted" id="siglaMoneda" style="border-color: #475569; font-size: 12px;">USD</span>
                            </div>
                            
                            <p id="conversionAyuda" class="small text-info mt-1 mb-0 d-none fst-italic"></p>
                        </div>

                        <button type="button" onclick="agregarPago()" class="btn w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm text-white" style="background-color: #4f46e5; border: none; transition: background-color 0.2s;">
                            <i class="fa-solid fa-plus"></i> Registrar Pago
                        </button>
                    </div>

                    <div id="contenedorListaPagos"></div>

                </div>

                <div class="modal-footer border-top bg-light py-2 px-3 justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-light border px-3 flex-grow-1" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-sm btn-success px-3 flex-grow-1 fw-bold">
                        Confirmar Venta
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>