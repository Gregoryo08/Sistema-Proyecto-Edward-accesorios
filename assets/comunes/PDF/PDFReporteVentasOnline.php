<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Helvetica, sans-serif; font-size: 10px; color: #20252b; }
.header { width: 100%; border-collapse: collapse; background: #12263a; color: #fff; border-bottom: 5px solid #f2b134; }
.header td { border: 0; padding: 10px 12px; vertical-align: middle; }
.logo { width: 78px; height: 58px; object-fit: contain; }
h1 { font-size: 19px; margin: 0 0 4px; color: #f2b134; }
.razon { font-size: 11px; font-weight: bold; margin: 0 0 3px; }
.ubicacion { font-size: 9px; line-height: 1.35; margin: 0; color: #e8edf2; }
.meta { text-align: right; font-size: 10px; line-height: 1.5; }
.titulo-reporte { margin: 18px 0 4px; font-size: 16px; color: #12263a; }
.subtitulo { margin: 0 0 12px; color: #66717c; }
table { width: 100%; border-collapse: collapse; margin-top: 12px; }
th, td { border: 1px solid #d8dee5; padding: 6px 5px; text-align: center; }
th { background: #12263a; color: #fff; font-size: 9px; }
tr:nth-child(even) td { background: #f5f7fa; }
.text-start { text-align: left !important; }
.text-end { text-align: right !important; }
.totales { width: 40%; margin-left: auto; }
.totales td { padding: 6px 10px; }
.no-data { margin-top: 40px; text-align: center; color: #777; }
</style>
</head>
<body>
<?php
$logoPath = dirname(__DIR__, 2) . '/img/logo_pdf.jpg';
$logoData = is_file($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

if (!isset($data_to_pdf)) {
    $data_to_pdf = [];
}

$montoTotal = 0;
$pedidosValidos = 0;
$pagosAprobados = 0;

foreach ($data_to_pdf as $row) {
    $estadoPedido = strtolower($row['estado_pedido'] ?? '');
    $estadoPago = strtolower($row['estado_pago'] ?? '');
    if ($estadoPedido !== 'rechazado' && $estadoPedido !== 'cancelado') {
        $montoTotal += floatval($row['total'] ?? 0);
        $pedidosValidos++;
    }
    if ($estadoPago === 'aprobado') {
        $pagosAprobados++;
    }
}

$filtroFechas = '';
$fIni = $_GET['fecha_inicio'] ?? '';
$fFin = $_GET['fecha_fin'] ?? '';
if (!empty($fIni) && !empty($fFin)) {
    $filtroFechas = ' | Período: ' . $fIni . ' al ' . $fFin;
}
?>
<table class="header"><tr>
<td style="width: 11%;"><?php if ($logoData): ?><img class="logo" src="data:image/jpeg;base64,<?= $logoData ?>" alt="Logo"> <?php endif; ?></td>
<td style="width: 64%;"><h1>Reporte de Ventas Online</h1><p class="razon">EGC ACCESORIOS, CA. | RIF J-50318361-6</p><p class="ubicacion">Calle 25 esquina carrera 22, Centro Comercial Cosmos, nivel mesanina, local N° 8, Barquisimeto, Estado Lara.</p></td>
<td class="meta" style="width: 25%;"><strong>Fecha de emisión</strong><br><?= date('d-m-Y') ?><br><strong>Pedidos de la Tienda Online</strong></td>
</tr></table>
<h2 class="titulo-reporte">Detalle de pedidos online</h2>
<p class="subtitulo">Listado generado con los filtros seleccionados<?= $filtroFechas ?>.</p>
<?php if (!empty($data_to_pdf)): ?>
<table>
<thead><tr>
<th>N° Pedido</th><th>Fecha</th><th>Cliente</th><th>Cédula</th><th>Teléfono</th><th>Método</th><th>Estado Pago</th><th>Estado Pedido</th><th>Despacho</th><th>Total</th>
</tr></thead>
<tbody>
<?php foreach ($data_to_pdf as $row): ?>
<tr>
<td class="text-start">#<?= htmlspecialchars($row['id_pedido'] ?? '') ?></td>
<td><?= htmlspecialchars($row['fecha'] ?? '') ?></td>
<td class="text-start"><?= htmlspecialchars($row['nombre_cliente'] ?? '') ?></td>
<td class="text-start"><?= htmlspecialchars($row['cedula_persona'] ?? '') ?></td>
<td class="text-start"><?= htmlspecialchars($row['telefono_cliente'] ?? '') ?></td>
<td class="text-start"><?= htmlspecialchars($row['metodo_pago'] ?? '-') ?></td>
<td class="text-capitalize"><?= htmlspecialchars($row['estado_pago'] ?? 'Sin Pago') ?></td>
<td class="text-capitalize"><?= htmlspecialchars($row['estado_pedido'] ?? '') ?></td>
<td class="text-capitalize"><?= htmlspecialchars($row['estado_despacho'] ?? 'Sin despacho') ?></td>
<td class="text-end">$<?= number_format((float)($row['total'] ?? 0), 2, ',', '.') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<table class="totales">
<tr><td class="text-start"><strong>Monto Total:</strong></td><td class="text-end"><strong>$<?= number_format($montoTotal, 2, ',', '.') ?></strong></td></tr>
<tr><td class="text-start">Pedidos Online:</td><td class="text-end"><?= $pedidosValidos ?></td></tr>
<tr><td class="text-start">Pagos Aprobados:</td><td class="text-end"><?= $pagosAprobados ?></td></tr>
</table>
<?php else: ?><p class="no-data">No se encontraron pedidos online con los filtros seleccionados.</p><?php endif; ?>
</body>
</html>