<?php
if (!isset($data_to_pdf)) {
    $data_to_pdf = [];
}

$montoTotalFiltrado = 0;
$totalVentasProcesadas = 0;
$totalVentasAnuladas = 0;

foreach ($data_to_pdf as $row) {
    $estado = strtolower($row['estado_venta'] ?? $row['estado'] ?? '');
    if ($estado === 'anulada' || $estado === 'cancelada') {
        $totalVentasAnuladas++;
    } else {
        $montoTotalFiltrado += floatval($row['total_venta'] ?? 0);
        $totalVentasProcesadas++;
    }
}
?>
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
        table.data-table, table.totales-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.data-table th, table.data-table td, table.totales-table td { border: 1px solid #d8dee5; padding: 6px 5px; text-align: center; }
        table.data-table th { background: #12263a; color: #fff; font-size: 9px; }
        table.data-table tr:nth-child(even) td { background: #f5f7fa; }
        table.totales-table { width: 40%; margin-left: auto; }
        table.totales-table td { padding: 6px 10px; }
        .text-start { text-align: left !important; }
        .text-end { text-align: right !important; }
        .fw-bold { font-weight: bold; }
        .no-data { margin-top: 40px; text-align: center; color: #777; }
    </style>
</head>
<body>

<?php
$logoPath = dirname(__DIR__, 2) . '/img/logo_pdf.jpg';
$logoData = is_file($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
?>
<table class="header"><tr>
<td style="width: 11%;"><?php if ($logoData): ?><img class="logo" src="data:image/jpeg;base64,<?= $logoData ?>" alt="Logo"> <?php endif; ?></td>
<td style="width: 64%;"><h1>Reporte de Ventas</h1><p class="razon">EGC ACCESORIOS, CA. | RIF J-50318361-6</p><p class="ubicacion">Calle 25 esquina carrera 22, Centro Comercial Cosmos, nivel mesanina, local N° 8, Barquisimeto, Estado Lara.</p></td>
<td class="meta" style="width: 25%;"><strong>Fecha de emisión</strong><br><?= date('d-m-Y') ?><br><strong>Sistema Administrativo</strong></td>
</tr></table>
<h2 class="titulo-reporte">Detalle de ventas registradas</h2>
<p class="subtitulo">Listado generado con los filtros seleccionados.</p>

<?php if (!empty($data_to_pdf)): ?>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 10%;">N° Venta</th>
                <th style="width: 18%;">Fecha / Hora</th>
                <th style="width: 12%;">Origen</th>
                <th style="width: 25%;">Cliente</th>
                <th style="width: 25%;">Operador</th>
                <th style="width: 15%;">Monto Total</th>
                <th style="width: 15%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_to_pdf as $row): 
                $cliente = !empty($row['nombre_cliente']) ? $row['nombre_cliente'] . " " . $row['apellido_cliente'] : 'Sin registrar';
                $empleado = !empty($row['nombre_empleado']) ? $row['nombre_empleado'] . " " . $row['apellido_empleado'] : 'Sin registrar';
                $origen = !empty($row['origen_venta']) ? $row['origen_venta'] : 'Mostrador';
                $estado = !empty($row['estado_venta']) ? $row['estado_venta'] : ($row['estado'] ?? 'Completada');
            ?>
                <tr>
                    <td class="fw-bold">#<?= htmlspecialchars($row['id_venta']) ?></td>
                    <td><?= date("d-m-Y h:i A", strtotime($row['fecha_venta'])) ?></td>
                    <td><?= htmlspecialchars($origen) ?></td>
                    <td class="text-start"><?= htmlspecialchars($cliente) ?></td>
                    <td class="text-start"><?= htmlspecialchars($empleado) ?></td>
                    <td class="text-end fw-bold">$<?= number_format($row['total_venta'], 2) ?></td>
                    <td class="text-capitalize"><?= htmlspecialchars(strtolower($estado)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totales-table">
        <tr>
            <td class="text-start fw-bold">Monto Total:</td>
            <td class="text-end fw-bold">$<?= number_format($montoTotalFiltrado, 2) ?></td>
        </tr>
        <tr>
            <td class="text-start">Ventas Procesadas:</td>
            <td class="text-end"><?= $totalVentasProcesadas ?></td>
        </tr>
        <tr>
            <td class="text-start">Ventas Anuladas:</td>
            <td class="text-end"><?= $totalVentasAnuladas ?></td>
        </tr>
    </table>

<?php else: ?>
    <p class="no-data">No se encontraron registros de ventas con los criterios seleccionados.</p>
<?php endif; ?>

</body>
</html>