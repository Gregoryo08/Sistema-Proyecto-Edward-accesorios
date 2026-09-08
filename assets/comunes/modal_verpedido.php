<div class="modal fade" id="modalVerPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Pedido #<?= $pedido['id_pedido'] ?? '' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-primary text-white"><h5>Datos del Pedido</h5></div>
                    <div class="card-body">
                        <p><strong>Cliente:</strong> <?= htmlspecialchars(($pedido['nombre'] ?? '') . ' ' . ($pedido['apellido'] ?? '')) ?></p>
                        <p><strong>Teléfono:</strong> <?= $pedido['telefono'] ?? '' ?> | <strong>Correo:</strong> <?= $pedido['correo'] ?? '' ?></p>
                        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha'] ?? 'now')) ?></p>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion_entrega'] ?? 'No especificada') ?></p>
                    </div>
                </div>
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-secondary text-white"><h5>Productos</h5></div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach ($detalle ?? [] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nombre_producto']) ?></td>
                                        <td><?= $item['cantidad'] ?></td>
                                        <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot><tr><td colspan="3" class="text-end"><strong>Total:</strong></td><td><strong>$<?= number_format($pedido['total'] ?? 0, 2) ?></strong></td></tr></tfoot>
                        </table>
                    </div>
                </div>
                <?php $estadoData = $pedidoModel->getEstadoData($pedido['estado'] ?? '', $pedido['motivo_rechazo'] ?? null); ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-<?= $estadoData['color'] ?> text-white"><h5>Estado del Pedido</h5></div>
                    <div class="card-body">
                        <span class="badge bg-<?= $estadoData['color'] ?> p-2 mb-3" style="font-size: 1rem;"><i class="fas <?= $estadoData['icono'] ?>"></i> <?= ucfirst($estadoData['estado']) ?></span>
                        <div class="alert alert-<?= $estadoData['clase_alerta'] ?> mt-2">
                            <strong><?= $estadoData['mensaje']['titulo'] ?></strong>
                            <p class="mb-0"><?= $estadoData['mensaje']['cuerpo'] ?></p>
                        </div>
                        <?php if ($estadoData['mostrar_boton']): ?>
                            <a href="?pagina=reportarPago&pedido=<?= $pedido['id_pedido'] ?>" class="btn btn-<?= $estadoData['boton_estilo'] ?> btn-lg mt-2"><i class="fas <?= $estadoData['boton_icono'] ?>"></i> <?= $estadoData['texto_boton'] ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
