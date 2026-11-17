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
    <title>Reporte de Ventas | Edward Accesorios</title>
    <link rel="icon" href="./assets/img/icono.ico">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #000; color: #fff; padding: 10px; }
        .header-table td { border: none; padding: 5px; vertical-align: top; }
        .titulo1 { font-size: 20px; margin: 0; }
        .info-empresa { font-size: 11px; margin: 0; color: #ccc; }
        .fecha-reporte { text-align: right; font-size: 12px; }

        /* Estilo simple para las tablas sin diseño ni colores */
        table.data-table, table.totales-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td, table.totales-table td { border: 1px solid #000; padding: 6px; text-align: center; }
        
        table.data-table th { font-weight: bold; font-size: 10px; text-transform: uppercase; background-color: #fff; border: 1px solid #000; color: #000; }
        table.totales-table { width: 40%; margin-left: auto; margin-top: 15px; border: 1px solid #000; }
        table.totales-table td { padding: 6px 10px; border: 1px solid #000; }
        
        /* Alineaciones y utilidades de texto */
        .text-start { text-align: left !important; }
        .text-end { text-align: right !important; }
        .fw-bold { font-weight: bold; }
        .no-data { text-align: center; color: #888; font-style: italic; margin-top: 40px; font-size: 12px; }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td>
            <h1 class="titulo1">Edward Accesorios C.A</h1>
            <p class="info-empresa">Reporte de Ventas General | Sistema Administrativo</p>
        </td>
        <td class="fecha-reporte">
            <strong>Fecha de Emisión:</strong><br>
            <?= date("d-m-Y h:i A"); ?>
        </td>
    </tr>
</table>

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