<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #000; color: #fff; padding: 10px; }
        .header-table td { border: none; padding: 5px; vertical-align: top; }
        
        .titulo1 { font-size: 20px; margin: 0; }
        .info-empresa { font-size: 11px; margin: 0; color: #ccc; }
        .fecha-reporte { text-align: right; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; font-size: 11px; }
        th { background-color: #2c2c2c; color: #fff; }
        
        .no-data { text-align: center; color: #888; font-style: italic; margin-top: 50px; }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td>
            <h1 class="titulo1">Edward Accesorios C.A</h1>
            <p class="info-empresa">Reporte de Financiamiento | Sistema Administrativo</p>
        </td>
        <td class="fecha-reporte">
            <strong>Fecha:</strong><br>
            <?php echo date("d-m-Y"); ?>
        </td>
    </tr>
</table>

<?php if (!empty($data_to_pdf)): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cédula</th>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Monto</th>
                <th>Estatus</th>
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
                    <td><?= number_format((float)($row['monto_total'] ?? 0), 2, ',', '.') ?></td>
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