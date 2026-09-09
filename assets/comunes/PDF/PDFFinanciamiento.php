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
<td style="width: 64%;"><h1>Reporte de Financiamiento</h1><p class="razon">EGC ACCESORIOS, CA. | RIF J-50318361-6</p><p class="ubicacion">Calle 25 esquina carrera 22, Centro Comercial Cosmos, nivel mesanina, local N° 8, Barquisimeto, Estado Lara.</p></td>
<td class="meta" style="width: 25%;"><strong>Fecha de emisión</strong><br><?= date('d-m-Y') ?><br><strong>Sistema Administrativo</strong></td>
</tr></table>
<h2 class="titulo-reporte">Detalle de financiamientos registrados</h2>
<p class="subtitulo">Listado generado con los filtros seleccionados.</p>
<?php if (!empty($data_to_pdf)): ?>
<table>
<thead>
<tr>
<th>ID</th>
<th>Cédula</th>
<th>Cliente</th>
<th>Producto</th>
<th>Monto</th>
<th>Estado</th>
<th>Equipo</th>
</tr>
</thead>
<tbody>
<?php foreach ($data_to_pdf as $row): ?>
<tr>
<td><?= htmlspecialchars($row['id_financiamiento'] ?? '') ?></td>
<td><?= htmlspecialchars($row['cedula_persona'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nombre_cliente'] ?? '') ?></td>
<td><?= htmlspecialchars($row['nombre_producto'] ?? '') ?></td>
<td>$<?= number_format((float)($row['monto_total'] ?? 0), 2, ',', '.') ?></td>
<td><?= htmlspecialchars($row['estado_financiamiento'] ?? '') ?></td>
<td><?= htmlspecialchars($row['estado_equipo'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<p class="no-data">No se encontraron financiamientos con los criterios seleccionados.</p>
<?php endif; ?>
</body>
</html>