<?php if (empty($pagosPendientes)): ?>
    <div class="alert alert-info text-center">No hay pagos pendientes por verificar.</div>
<?php else: ?>
    <?php foreach ($pagosPendientes as $pago): ?>
        <?php
        $nombre = htmlspecialchars(($pago['nombre'] ?? '') . ' ' . ($pago['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
        $telefono = htmlspecialchars($pago['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $totalPedido = number_format($pago['total'] ?? 0, 2);
        $monto = number_format($pago['monto_reportado'], 2);
        $banco = htmlspecialchars($pago['banco_emisor'] ?? '', ENT_QUOTES, 'UTF-8');
        $referencia = htmlspecialchars($pago['referencia'] ?? '', ENT_QUOTES, 'UTF-8');
        $idReporte = (int) $pago['id_reporte'];
        $idPedido = (int) $pago['id_pedido'];
        ?>
        <div class="card mb-2 card-pendiente" id="pendiente-<?= $idReporte ?>">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">Pedido #<?= $idPedido ?></h6>
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-user"></i> <?= $nombre ?><br>
                            <i class="fas fa-phone"></i> <?= $telefono ?>
                        </p>
                        <p class="mb-1 small"><strong>Total Pedido:</strong> $<?= $totalPedido ?></p>
                        <p class="mb-1 small text-muted"><strong>Monto Reportado:</strong> Bs. <?= $monto ?></p>
                        <p class="mb-1 small"><strong>Referencia:</strong> <?= $referencia ?></p>
                        <p class="mb-0 small"><strong>Banco:</strong> <?= $banco ?></p>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm mb-1" onclick="aprobarPago(<?= $idReporte ?>, <?= $idPedido ?>)">
                            <i class="fas fa-check"></i> Aprobar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rechazarPago(<?= $idReporte ?>, <?= $idPedido ?>)">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
