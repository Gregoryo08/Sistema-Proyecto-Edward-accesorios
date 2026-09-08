<div class="modal fade" id="modalDatosPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $titulo ?? ($metodo === 'transferencia' ? 'Transferencia Bancaria' : 'Pago Móvil') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">Realiza el pago exacto de <strong>$<?= number_format($pedido['total'] ?? 0, 2) ?> USD</strong> (<strong>Bs. <?= number_format($total_bs ?? 0, 2) ?></strong>) usando los siguientes datos:</div>
                <?php if ($metodo === 'transferencia'): ?>
                    <table class="table table-bordered">
                        <tr><th>Banco</th><td><?= htmlspecialchars($datos_pago['nombre'] ?? '') ?></td></tr>
                        <tr><th>Número de cuenta</th><td><strong><?= htmlspecialchars($datos_pago['numero_cuenta'] ?? '') ?></strong></td></tr>
                        <tr><th>Titular</th><td><?= htmlspecialchars($datos_pago['titular'] ?? 'Edward Accesorios C.A.') ?></td></tr>
                        <tr><th>RIF</th><td><?= htmlspecialchars($datos_pago['cedula_rif'] ?? '') ?></td></tr>
                    </table>
                <?php else: ?>
                    <table class="table table-bordered">
                        <tr><th>Banco</th><td><?= htmlspecialchars($datos_pago['banco'] ?? '') ?></td></tr>
                        <tr><th>Teléfono</th><td><strong><?= htmlspecialchars($datos_pago['telefono'] ?? '') ?></strong></td></tr>
                        <tr><th>Cédula</th><td><?= htmlspecialchars($datos_pago['cedula'] ?? '') ?></td></tr>
                        <tr><th>RIF</th><td><?= htmlspecialchars($datos_pago['rif'] ?? '') ?></td></tr>
                    </table>
                <?php endif; ?>
                <a href="?pagina=reportarPago&pedido=<?= $id_pedido ?>&metodo=<?= $metodo ?>" class="btn btn-success btn-lg w-100 mt-3"><i class="fas fa-check-circle"></i> YA PAGUÉ</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
