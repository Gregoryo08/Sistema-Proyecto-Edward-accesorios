<?php require_once('assets/comunes/menu.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title style="">Ventas Online | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .main-content { padding: 60px 20px 20px 20px; margin-left: 65px; transition: margin-left 0.3s ease, width 0.3s ease; width: calc(100% - 65px); box-sizing: border-box; }
        .card-pendiente { border-left: 5px solid #ffc107; }
        .card-aprobado { border-left: 5px solid #28a745; }
        .stats-card { transition: transform 0.2s; cursor: pointer; border: none; border-radius: 15px; }
        .stats-card:hover { transform: translateY(-5px); }
        .card-header-custom { cursor: pointer; border-radius: 10px 10px 0 0 !important; }
        .mb-4 { margin-bottom: 0.75rem !important; }
        .card { margin-bottom: 0.75rem; }
        .main-title { margin-bottom: 1.5rem !important; margin-top: 0.5rem !important; }
        .main-title h2 { font-size: 1.5rem; margin-bottom: 0; display: flex; align-items: center; gap: 10px; }
        .stat-icon { font-size: 2rem; opacity: 0.7; }
        
    </style>
</head>
<body>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center main-title">
        <h2 style="color: black; padding-top: 10px;" class="align-items-center main-title" ><i class="fa-solid fa-globe me-2"></i>Ventas Online</h2>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-secondary p-2">
                <i class="fas fa-user me-1"></i> Cajera: <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
            </span>
        </div>
    </div>

    <!-- SECCIÓN 1: ESTADÍSTICAS RÁPIDAS -->
    <div class="card shadow">
        <div class="card-header bg-dark text-white card-header-custom" onclick="toggleSection('statsSection')">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>📊 Resumen de Ventas Online ▼</h5>
        </div>
        <div id="statsSection" class="collapse show">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><div class="card text-white bg-danger shadow stats-card"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="card-title">Pagos Pendientes</h6><h2 class="mb-0" id="pendientesCount"><?= $stats['pendientes'] ?? 0 ?></h2></div><i class="fas fa-question-circle stat-icon"></i></div></div></div></div>
                    <div class="col-md-3"><div class="card text-white bg-success shadow stats-card"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="card-title">Aprobados x Despachar</h6><h2 class="mb-0" id="aprobadosCount"><?= $stats['aprobados'] ?? 0 ?></h2></div><i class="fas fa-dollar-sign stat-icon"></i></div></div></div></div>
                    <div class="col-md-3"><div class="card text-white bg-warning shadow stats-card"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="card-title">Despachos en Ruta</h6><h2 class="mb-0" id="rutaCount"><?= $stats['en_ruta'] ?? 0 ?></h2></div><i class="fas fa-motorcycle stat-icon"></i></div></div></div></div>
                    <div class="col-md-3"><div class="card text-white bg-dark shadow stats-card"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><h6 class="card-title">Total Pedidos</h6><h2 class="mb-0" id="totalPedidosCount">-</h2></div><i class="fas fa-chart-simple stat-icon"></i></div></div></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: PENDIENTES POR APROBAR -->
    <div class="card shadow">
        <div class="card-header bg-danger text-white card-header-custom" onclick="toggleSection('pendientesSection')">
            <h5 class="mb-0"><i class="fas fa-hourglass-half me-2"></i>⏳ Pendientes por Aprobar ▼</h5>
        </div>
        <div id="pendientesSection" class="collapse show">
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($pagos_pendientes)): ?>
                    <div class="alert alert-info text-center">No hay pagos pendientes por verificar.</div>
                <?php else: ?>
                    <?php foreach ($pagos_pendientes as $p): ?>
                        <div class="card mb-2 card-pendiente" id="pendiente-<?= $p['id_reporte'] ?>">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Pedido #<?= $p['id_pedido'] ?></h6>
                                        <p class="mb-1 small">
                                            <i class="fas fa-user"></i> <?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? '')) ?><br>
                                            <i class="fas fa-phone"></i> <?= $p['telefono'] ?? 'N/A' ?>
                                        </p>
                                        <p class="mb-1 small"><strong>Total Pedido:</strong> $<?= number_format($p['total'] ?? 0, 2) ?></p>
                                        <p class="mb-1 small"><strong>Monto Reportado:</strong> Bs. <?= number_format($p['monto_reportado'], 2) ?></p>
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
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($pagos_aprobados)): ?>
                    <div class="alert alert-info text-center">No hay pagos aprobados pendientes de despacho.</div>
                <?php else: ?>
                    <?php foreach ($pagos_aprobados as $i => $p): ?>
                        <div class="card mb-2 card-aprobado" id="aprobado-<?= $p['id_reporte'] ?>">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Pedido #<?= $p['id_pedido'] ?></h6>
                                        <p class="mb-1 small">
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

    <!-- SECCIÓN 5: ESTADÍSTICAS CON HIGHCHARTS -->
    <div class="card shadow">
        <div class="card-header bg-dark text-white card-header-custom" onclick="toggleSection('estadisticasSection')">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>📈 Estadísticas de Ventas Online ▼</h5>
        </div>
        <div id="estadisticasSection" class="collapse show">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><div id="chartVentasDiarias" style="height: 280px;"></div></div>
                    <div class="col-md-4"><div id="chartVentasMensuales" style="height: 280px;"></div></div>
                    <div class="col-md-4"><div id="chartVentasNoConcretadas" style="height: 280px;"></div></div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6"><div id="chartMetodosPago" style="height: 300px;"></div></div>
                    <div class="col-md-6"><div id="chartTopClientes" style="height: 300px;"></div></div>
                </div>
                <div class="row mt-3">
                    <div class="col-12"><div id="chartTopProductos" style="height: 300px;"></div></div>
                </div>
                <div class="row mt-3">
                    <div class="col-12"><div id="chartDespachosMensuales" style="height: 280px;"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="assets/js/ecommerce/panelCajera.js"></script>

</body>
</html>