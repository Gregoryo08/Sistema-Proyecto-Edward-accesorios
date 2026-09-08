<div class="modal fade" id="modalReportarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reportar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-reporte" autocomplete="off">
                    <input type="hidden" name="id_pedido" value="<?= htmlspecialchars($id_pedido) ?>">
                    <input type="hidden" name="metodo" value="<?= htmlspecialchars($metodo) ?>">
                    <input type="hidden" name="banco_receptor" value="<?= htmlspecialchars($banco_receptor ?? 'Banesco') ?>">
                    <input type="hidden" name="total_usd" id="total-usd" value="<?= htmlspecialchars($total_usd) ?>">
                    <div class="alert alert-info py-2 small"><i class="fas fa-info-circle"></i> Monto: <strong>$<?= number_format($total_usd, 2) ?> USD</strong> → <strong>Bs. <?= number_format($total_bs, 2) ?></strong><br>Tasa: <span class="tasa-valor"><?= number_format($tasa, 2) ?></span> Bs./USD</div>
                    <div id="paso1" class="paso">
                        <h5 class="text-primary">Paso 1 de 4</h5>
                        <label class="form-label fw-bold">📋 Referencia bancaria</label>
                        <input type="text" class="form-control form-control-lg" name="referencia" placeholder="Ej: 123456789012" maxlength="13">
                        <div class="mt-3"><button type="button" class="btn btn-primary btn-lg w-100" onclick="validarPaso1()">Confirmar Referencia <i class="fas fa-arrow-right"></i></button></div>
                    </div>
                    <div id="paso2" class="paso" style="display:none;">
                        <h5 class="text-primary">Paso 2 de 4</h5>
                        <label class="form-label fw-bold">🏦 Banco emisor</label>
                        <input type="text" class="form-control form-control-lg" name="banco_emisor" placeholder="Ej: Banco Provincial" minlength="3">
                        <div class="mt-3"><button type="button" class="btn btn-primary btn-lg w-100" onclick="validarPaso2()">Confirmar Banco <i class="fas fa-arrow-right"></i></button></div>
                    </div>
                    <div id="paso3" class="paso" style="display:none;">
                        <h5 class="text-primary">Paso 3 de 4</h5>
                        <label class="form-label fw-bold">📱 Teléfono</label>
                        <input type="tel" class="form-control form-control-lg" name="telefono" placeholder="Ej: 04121234567" maxlength="15">
                        <div class="mt-3"><button type="button" class="btn btn-primary btn-lg w-100" onclick="validarPaso3()">Confirmar Teléfono <i class="fas fa-arrow-right"></i></button></div>
                    </div>
                    <div id="paso4" class="paso" style="display:none;">
                        <h5 class="text-primary">Paso 4 de 4</h5>
                        <label class="form-label fw-bold">💵 Monto transferido</label>
                        <input type="text" class="form-control form-control-lg" name="monto" placeholder="Ej: 1340.00" oninput="this.value = this.value.replace(/[^0-9.,]/g, '')">
                        <div class="mt-3"><button type="button" class="btn btn-primary btn-lg w-100" onclick="mostrarResumen()">Revisar Todo <i class="fas fa-clipboard-check"></i></button></div>
                    </div>
                    <div id="resumen" style="display:none;">
                        <h5 class="text-success mb-3">✅ Confirma los datos</h5>
                        <div class="card bg-light mb-3"><div class="card-body"><ul class="list-unstyled mb-0"><li><strong>📋 Referencia:</strong> <span id="r_ref"></span></li><li><strong>🏦 Banco:</strong> <span id="r_banco"></span></li><li><strong>📱 Teléfono:</strong> <span id="r_telf"></span></li><li><strong>💵 Monto:</strong> <span id="r_monto"></span></li></ul></div></div>
                        <button type="button" class="btn btn-success btn-lg w-100" onclick="enviarReporte()"><i class="fas fa-check-circle"></i> CONFIRMAR TODO Y ENVIAR</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
