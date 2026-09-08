<div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finalizar compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resumen-carrito" class="alert alert-secondary"></div>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Datos del cliente</h4>
                        <input type="text" id="nombre" class="form-control mb-2" placeholder="Nombre completo" value="<?= htmlspecialchars($cliente_nombre ?? '') ?>">
                        <input type="email" id="email" class="form-control mb-2" placeholder="Correo electrónico" value="<?= htmlspecialchars($cliente_correo ?? '') ?>">
                        <input type="tel" id="telefono" class="form-control mb-2" placeholder="Teléfono" value="<?= htmlspecialchars($cliente_telefono ?? '') ?>">
                        <input type="hidden" name="cedula_persona" id="cedula_persona" value="<?= htmlspecialchars($cedula_persona_checkout ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <h4>Forma de entrega</h4>
                        <button id="btn-retiro" class="btn btn-outline-primary w-100 mb-2">📦 Retiro Personal (Gratis)</button>
                        <button id="btn-delivery" class="btn btn-outline-primary w-100">🚚 Delivery ($10)</button>
                        <div id="delivery-form" style="display:none;" class="mt-3">
                            <input type="text" id="direccion" class="form-control mb-2" placeholder="Dirección completa">
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>Método de pago</h4>
                        <select id="metodo_pago" class="form-select">
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="pago_movil">Pago Móvil</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h4>Banco desde donde pagas</h4>
                        <select id="banco_emisor" class="form-select">
                            <option value="">Seleccione su banco</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded">
                    <h4>Total: <span id="total-final" class="text-success">$0.00</span></h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarCheckout">Confirmar compra</button>
            </div>
        </div>
    </div>
</div>
