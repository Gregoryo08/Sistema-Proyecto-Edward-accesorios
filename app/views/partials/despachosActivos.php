<?php if (empty($despachosActivos)): ?>
    <div class="alert alert-info text-center">No hay despachos activos en este momento.</div>
<?php else: ?>
    <?php foreach ($despachosActivos as $despacho): ?>
        <?php
        $nombre = htmlspecialchars(($despacho['nombre'] ?? '') . ' ' . ($despacho['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
        $telefono = htmlspecialchars($despacho['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $direccion = htmlspecialchars($despacho['direccion_entrega'] ?? 'No especificada', ENT_QUOTES, 'UTF-8');
        $total = number_format($despacho['total'] ?? 0, 2);
        $motorizado = htmlspecialchars(
            ($despacho['despachador_nombre'] ?? 'No asignado')
            . (!empty($despacho['despachador_telefono']) ? ' - ' . $despacho['despachador_telefono'] : ''),
            ENT_QUOTES,
            'UTF-8'
        );
        $entregaEstimada = htmlspecialchars($despacho['fecha_entrega_estimada'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $estado = strtoupper($despacho['estado_despacho'] ?? 'pendiente');
        $claseEstado = ['PENDIENTE' => 'secondary', 'ASIGNADO' => 'info', 'EN_RUTA' => 'warning'][$estado] ?? 'secondary';
        $idPedido = (int) $despacho['id_pedido'];
        ?>
        <div class="card mb-2 card-despacho" id="despacho-row-<?= $idPedido ?>">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">
                            Pedido #<?= $idPedido ?>
                            <span class="badge bg-<?= $claseEstado ?> text-white ms-1"><?= $estado ?></span>
                        </h6>
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-user"></i> <?= $nombre ?><br>
                            <i class="fas fa-phone"></i> <?= $telefono ?>
                        </p>
                        <p class="mb-1 small"><strong>Dirección:</strong> <?= $direccion ?></p>
                        <p class="mb-1 small"><strong>Total:</strong> $<?= $total ?></p>
                        <p class="mb-1 small"><strong>Motorizado:</strong> <?= $motorizado ?></p>
                        <p class="mb-0 small text-muted"><strong>Entrega estimada:</strong> <?= $entregaEstimada ?></p>
                    </div>
                    <div>
                        <button class="btn btn-success btn-sm mb-1" onclick="actualizarEstadoDespacho(<?= $idPedido ?>, 'entregado')">
                            <i class="fas fa-check-double"></i> Entregado
                        </button>
                        <?php if ($estado !== 'EN_RUTA'): ?>
                            <button class="btn btn-info btn-sm mb-1" onclick="actualizarEstadoDespacho(<?= $idPedido ?>, 'en_ruta')">
                                <i class="fas fa-motorcycle"></i> Iniciar Ruta
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-danger btn-sm" onclick="actualizarEstadoDespacho(<?= $idPedido ?>, 'cancelado')">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
