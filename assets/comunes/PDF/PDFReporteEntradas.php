<?php
if (!isset($data_to_pdf)) {
    $data_to_pdf = [];
}
?>
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
        
        .badge-garantia { background-color: #27ae60; color: #fff; padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-sin-garantia { background-color: #6c757d; color: #fff; padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        
        .no-data { text-align: center; color: #888; font-style: italic; margin-top: 50px; }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td>
            <h1 class="titulo1">Edward Accesorios C.A</h1>
            <p class="info-empresa">Reporte de Entradas de Productos | Sistema Administrativo</p>
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
                <th>Proveedor</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Garantía</th>
            </tr>
        </thead>
        <tbody>
            <?php $contador = 1; ?>
            <?php foreach ($data_to_pdf as $row): ?>
                <tr>
                    <td><?= $contador++ ?></td>
                    <td><?= htmlspecialchars($row['nombre_proveedor']) ?></td>
                    <td><?= htmlspecialchars($row['nombre_producto']) ?></td>
                    <td><?= htmlspecialchars($row['cantidad']) ?></td>
                    <td><?= date("d-m-Y", strtotime($row['fecha_entrada'])) ?></td>
                    <td>
                        <?php if (!empty($row['dias_garantia'])): ?>
                            <span class="badge-garantia"><?= $row['dias_garantia'] ?> días</span>
                        <?php else: ?>
                            <span class="badge-sin-garantia">Sin garantía</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-data">No se encontraron entradas con los criterios seleccionados.</p>
<?php endif; ?>

</body>
</html>