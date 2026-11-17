<?php if (empty($pagosAprobados)): ?>
    <div class="alert alert-info text-center">No hay pagos aprobados pendientes de despacho.</div>
<?php else: ?>
    <?php foreach ($pagosAprobados as $pago): ?>
        <?php
        $nombre = htmlspecialchars(($pago['nombre'] ?? '') . ' ' . ($pago['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
        $telefono = htmlspecialchars($pago['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $direccion = htmlspecialchars($pago['direccion_entrega'] ?? 'No especificada', ENT_QUOTES, 'UTF-8');
        $total = number_format($pago['total'] ?? 0, 2);
        $cedula = htmlspecialchars($pago['cedula_persona'] ?? '', ENT_QUOTES, 'UTF-8');
        $idReporte = (int) $pago['id_reporte'];
        $idPedido = (int) $pago['id_pedido'];
        ?>
        <div class="card mb-2 card-aprobado" id="aprobado-<?= $idReporte ?>">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">Pedido #<?= $idPedido ?></h6>
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-user"></i> <?= $nombre ?><br>
                            <i class="fas fa-phone"></i> <?= $telefono ?>
                        </p>
                        <p class="mb-1 small"><strong>Dirección:</strong> <?= $direccion ?></p>
                        <p class="mb-1 small"><strong>Total:</strong> $<?= $total ?></p>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm"
                                onclick="mostrarFormularioDespacho(
                                    <?= $idReporte ?>,
                                    <?= $idPedido ?>,
                                    '<?= $cedula ?>',
                                    '<?= $nombre ?>',
                                    '<?= $telefono ?>',
                                    '<?= $direccion ?>'
                                )">
                            <i class="fas fa-truck"></i> Asignar Despacho
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
