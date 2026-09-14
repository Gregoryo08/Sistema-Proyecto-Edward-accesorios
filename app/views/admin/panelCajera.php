<?php require_once('assets/comunes/menu.php'); ?>
<main class="main-content">
    <!-- Título -->
    <div class="d-flex justify-content-between align-items-center main-title" style="padding-top: 40px;">
        <h2 class="fw-bold"><i class="fa-solid fa-globe me-2"></i>Ventas Online</h2>
        <div class="d-flex align-items-center gap-3">

            <span class="badge bg-secondary p-2">
                <i class="fas fa-user me-1"></i> Cajera: <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
            </span>
        </div>
    </div>

    <!-- SECCIÓN 3: PENDIENTES POR APROBAR -->
    <div class="card shadow">
        <div class="card-header bg-danger text-white card-header-custom" onclick="toggleSection('pendientesSection')">
            <h5 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>⏳ Pendientes por Aprobar ▼</h5>
        </div>
        <div id="pendientesSection" class="collapse show">
            <div class="card-body dashboard-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($pagos_pendientes)): ?>
                    <div class="alert alert-info text-center">No hay pagos pendientes por verificar.</div>
                <?php else: ?>
                    <?php foreach ($pagos_pendientes as $p): ?>
                        <div class="card mb-2 card-pendiente" id="pendiente-<?= $p['id_reporte'] ?>">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Pedido #<?= $p['id_pedido'] ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-user"></i> <?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?><br>
                                            <i class="fas fa-phone"></i> <?= $p['telefono'] ?? 'N/A' ?>
                                        </p>
                                        <p class="mb-1 small"><strong>Total Pedido:</strong> $<?= number_format($p['total'] ?? 0, 2) ?></p>
                                        <p class="mb-1 small text-muted"><strong>Monto Reportado:</strong> Bs. <?= number_format($p['monto_reportado'], 2) ?></p>
                                        <p class="mb-1 small"><strong>Referencia:</strong> <?= $p['referencia'] ?></p>
                                        <p class="mb-0 small"><strong>Banco:</strong> <?= $p['banco_emisor'] ?></p>
                                    </div>
                                    <div>
                                        <button class="btn btn-success btn-sm mb-1" onclick="aprobarPago(<?= $p['id_reporte'] ?>, <?= $p['id_pedido'] ?>)"><i class="fas fa-check"></i> Aprobar</button>
                                        <button class="btn btn-danger btn-sm" onclick="rechazarPago(<?= $p['id_reporte'] ?>, <?= $p['id_pedido'] ?>)"><i class="fas fa-times"></i> Rechazar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 4: APROBADOS PENDIENTES DE DESPACHO -->
    <div class="card shadow">
        <div class="card-header bg-secondary text-white card-header-custom" onclick="toggleSection('aprobadosSection')">
            <h5 class="mb-0"><i class="fas fa-truck me-2"></i>✅ Aprobados - Pendientes de Despacho ▼</h5>
        </div>
        <div id="aprobadosSection" class="collapse show">
            <div class="card-body dashboard-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($pagos_aprobados)): ?>
                    <div class="alert alert-info text-center">No hay pagos aprobados pendientes de despacho.</div>
                <?php else: ?>
                    <?php foreach ($pagos_aprobados as $i => $p): ?>
                        <div class="card mb-2 card-aprobado" id="aprobado-<?= $p['id_reporte'] ?>">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Pedido #<?= $p['id_pedido'] ?></h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-user"></i> <?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?><br>
                                            <i class="fas fa-phone"></i> <?= $p['telefono'] ?? 'N/A' ?>
                                        </p>
                                        <p class="mb-1 small"><strong>Dirección:</strong> <?= htmlspecialchars($p['direccion_entrega'] ?? 'No especificada') ?></p>
                                        <p class="mb-1 small"><strong>Total:</strong> $<?= number_format($p['total'] ?? 0, 2) ?></p>
                                    </div>
                                    <div>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="mostrarFormularioDespacho(
                                                <?= $p['id_reporte'] ?>, 
                                                <?= $p['id_pedido'] ?>, 
                                                '<?= addslashes($p['cedula_persona'] ?? '') ?>', 
                                                '<?= addslashes(htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''))) ?>', 
                                                '<?= addslashes($p['telefono'] ?? '') ?>', 
                                                '<?= addslashes(htmlspecialchars($p['direccion_entrega'] ?? '')) ?>'
                                            )">
                                            <i class="fas fa-truck"></i> Asignar Despacho
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- SECCIÓN 4.1: DESPACHOS EN RUTA -->
    <div class="card shadow">
        <div class="card-header bg-warning text-white card-header-custom" onclick="toggleSection('despachosSection')">
            <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>🚚 Despachos en Ruta ▼</h5>
        </div>
        <div id="despachosSection" class="collapse show">
            <div class="card-body dashboard-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($despachos_activos)): ?>
                    <div class="alert alert-info text-center">No hay despachos activos en este momento.</div>
                <?php else: ?>
                    <?php foreach ($despachos_activos as $d):
                        $claseEstado = ['pendiente' => 'secondary', 'asignado' => 'info', 'en_ruta' => 'warning'][$d['estado_despacho'] ?? 'pendiente'] ?? 'secondary';
                    ?>
                        <div class="card mb-2 card-despacho" id="despacho-row-<?= $d['id_pedido'] ?>">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Pedido #<?= $d['id_pedido'] ?>
                                            <span class="badge bg-<?= $claseEstado ?> text-white ms-1"><?= strtoupper($d['estado_despacho'] ?? 'pendiente') ?></span>
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-user"></i> <?= htmlspecialchars(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? '')) ?><br>
                                            <i class="fas fa-phone"></i> <?= htmlspecialchars($d['telefono'] ?? 'N/A') ?>
                                        </p>
                                        <p class="mb-1 small"><strong>Dirección:</strong> <?= htmlspecialchars($d['direccion_entrega'] ?? 'No especificada') ?></p>
                                        <p class="mb-1 small"><strong>Total:</strong> $<?= number_format($d['total'] ?? 0, 2) ?></p>
                                        <p class="mb-1 small"><strong>Motorizado:</strong> <?= htmlspecialchars(($d['despachador_nombre'] ?? 'No asignado')) . (!empty($d['despachador_telefono']) ? ' - ' . htmlspecialchars($d['despachador_telefono']) : '') ?></p>
                                        <p class="mb-0 small text-muted"><strong>Entrega estimada:</strong> <?= htmlspecialchars($d['fecha_entrega_estimada'] ?? 'N/A') ?></p>
                                    </div>
                                    <div>
                                        <button class="btn btn-success btn-sm mb-1" onclick="actualizarEstadoDespacho(<?= $d['id_pedido'] ?>, 'entregado')"><i class="fas fa-check-double"></i> Entregado</button>
                                        <?php if (($d['estado_despacho'] ?? 'pendiente') !== 'en_ruta'): ?>
                                            <button class="btn btn-info btn-sm mb-1" onclick="actualizarEstadoDespacho(<?= $d['id_pedido'] ?>, 'en_ruta')"><i class="fas fa-motorcycle"></i> Iniciar Ruta</button>
                                        <?php endif; ?>
                                        <button class="btn btn-danger btn-sm" onclick="actualizarEstadoDespacho(<?= $d['id_pedido'] ?>, 'cancelado')"><i class="fas fa-times"></i> Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-content {
            padding: 60px 20px 20px 20px;
            transition: margin-left 0.3s ease, width 0.3s ease;
            box-sizing: border-box;
        }

        .card-pendiente {
            border-left: 5px solid #ffc107;
        }

        .card-aprobado {
            border-left: 5px solid #28a745;
        }

        .stats-card {
            transition: transform 0.2s;
            cursor: pointer;
            border: none;
            border-radius: 15px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .card-header-custom {
            cursor: pointer;
            border-radius: 10px 10px 0 0 !important;
        }

        .dashboard-body {
            background-color: #e9ecef !important;
            border-radius: 0 0 10px 10px;
        }

        .mb-4 {
            margin-bottom: 0.75rem !important;
        }

        h5 {
            color: white;
        }
        .card {

            margin-bottom: 0.75rem;
        }

        .main-title {
            color: black;
            margin-bottom: 1.5rem !important;
            margin-top: 0.5rem !important;
        }

        .main-title h2 {
            color: black;
            font-size: 1.5rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
</style>
<script src="assets/js/ecommerce/panelCajera.js"></script>