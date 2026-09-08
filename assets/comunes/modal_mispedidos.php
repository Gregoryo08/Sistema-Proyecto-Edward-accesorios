<div class="modal fade" id="modalMisPedidos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mis Pedidos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($pedidos)): ?>
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <h4>No tienes pedidos aún</h4>
                        <p class="mb-3">Explora nuestro catálogo y encuentra lo que más te guste.</p>
                        <a href="?pagina=web_Catalogo" class="btn btn-primary"><i class="fas fa-store"></i> Ir al Catálogo</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <span><strong>Pedido #<?= $pedido['id_pedido'] ?></strong></span>
                                    <span>Fecha: <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></span>
                                    <?php $estadoData = $pedidoModel->getEstadoData($pedido['estado'], $pedido['motivo_rechazo'] ?? null); ?>
                                    <span class="badge bg-<?= $estadoData['color'] ?> p-2"><i class="fas <?= $estadoData['icono'] ?> me-1"></i><?= ucfirst($estadoData['estado']) ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['nombre'] . ' ' . $pedido['apellido']) ?></p>
                                <p><strong>Teléfono:</strong> <?= $pedido['telefono'] ?> | <strong>Correo:</strong> <?= $pedido['correo'] ?></p>
                                <p><strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?> | <strong>Método:</strong> <?= ucfirst($pedido['metodo_pago'] ?? 'No especificado') ?></p>
                                <?php if ($pedido['estado'] == 'rechazado' && !empty($pedido['motivo_rechazo'])): ?>
                                    <div class="alert alert-danger mt-2 py-2"><i class="fas fa-exclamation-circle"></i> <strong>Rechazado:</strong> <?= htmlspecialchars($pedido['motivo_rechazo']) ?></div>
                                <?php endif; ?>
                                <a href="?pagina=verPedido&id=<?= $pedido['id_pedido'] ?>" class="btn btn-outline-primary btn-sm mt-2"><i class="fas fa-eye"></i> Ver Detalle</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
