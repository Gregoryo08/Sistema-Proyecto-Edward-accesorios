<?php
$metodo = $metodo ?? $metodoPago ?? 'pago_movil';
$totalDolares = floatval($pedido['total'] ?? 0);
$tasaVal = floatval($tasa ?? 0);
$totalBs = $totalDolares * $tasaVal;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de Pago | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/ecommerce/temaBase.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="?pagina=web_Catalogo">Edward<span class="text-primary">Accesorios</span></a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> <?= htmlspecialchars($titulo ?? ($metodo === 'transferencia' ? 'Transferencia Bancaria' : 'Pago Móvil')) ?></h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Realiza el pago exacto de <strong>$<?= number_format($totalDolares, 2) ?> USD</strong>
                        (<strong>Bs. <span id="totalBsEfectivo"><?= number_format($totalBs, 2) ?></span></strong>)
                        usando los siguientes datos:
                    </div>
                    
                    <div class="row small text-muted mb-2 align-items-center">
                        <div class="col-8">
                            Tasa: <span class="tasa-valor" id="tasaValor"><?= number_format($tasaVal, 2) ?></span> Bs./USD
                            <span class="tasa-alert <?= ($vigencia['expirada'] ?? false) ? 'text-danger fw-bold' : 'text-success' ?>" id="tasaAlert">
                                <?= htmlspecialchars($vigencia['mensaje'] ?? '') ?>
                            </span>
                        </div>
                        <div class="col-4 text-end" id="tasaAccion">
                            <?php if ($vigencia['expirada'] ?? false): ?>
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <span class="small text-danger fw-bold" id="tasaPulsa">pulsa para actualizar tasa &#8594;</span>
                                    <button type="button" class="btn btn-warning btn-sm fw-bold" id="btnActualizarTasa">
                                        TASA $/BCV HOY
                                    </button>
                                </div>
                                <style>
                                    #tasaPulsa {
                                        display: inline-block;
                                        transform: translateX(0);
                                        animation: golpear 1.2s ease-in-out infinite;
                                    }
                                    @keyframes golpear {
                                        0%, 100% { transform: translateX(0); }
                                        50%      { transform: translateX(6px); }
                                    }
                                </style>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($metodo === 'transferencia'): ?>
                        <table class="table table-bordered">
                            <tr><th>Banco</th><td>Banco de Venezuela (0102)</td></tr>
                            <tr><th>Número de cuenta</th><td><strong>0102-0123-45-1234567890</strong></td></tr>
                            <tr><th>Titular</th><td>Edward Accesorios C.A.</td></tr>
                            <tr><th>RIF</th><td>J-12345678-9</td></tr>
                        </table>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr><th>Banco</th><td>Banco de Venezuela (0102)</td></tr>
                            <tr><th>Teléfono</th><td><strong>0412-1234567</strong></td></tr>
                            <tr><th>Cédula</th><td>V-12345678</td></tr>
                            <tr><th>RIF</th><td>J-12345678-9</td></tr>
                        </table>
                    <?php endif; ?>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong> Una vez realizada la transferencia o pago móvil, haz clic en el botón "YA PAGUÉ" para reportar tu pago.
                        <br><small>Reporta el monto exacto en <strong>Bs. <span id="totalBsReportar"><?= number_format($totalBs, 2) ?></span></strong></small>
                    </div>

                    <a href="?pagina=reportarPago&pedido=<?= htmlspecialchars($id_pedido ?? '') ?>&metodo=<?= htmlspecialchars($metodo) ?>"
                       class="btn btn-success btn-lg w-100 mt-3" id="btnYaPague"
                       <?php if ($vigencia['expirada'] ?? false): ?>disabled style="pointer-events:none;opacity:.65;"<?php endif; ?>>
                        <i class="fas fa-check-circle"></i> YA PAGUÉ
                    </a>

                    <a href="?pagina=carrito" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left"></i> Volver al carrito
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var totalUSD = <?= $totalDolares; ?>;
    var btnTasa = document.getElementById('btnActualizarTasa');
    var btnYaPague = document.getElementById('btnYaPague');
    var tasaValor = document.getElementById('tasaValor');
    var tasaAlert = document.getElementById('tasaAlert');
    var tasaAccion = document.getElementById('tasaAccion');
    var totalBsEfectivo = document.getElementById('totalBsEfectivo');
    var totalBsReportar = document.getElementById('totalBsReportar');

    function fmt(n) {
        return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    if (btnTasa) {
        btnTasa.addEventListener('click', function () {
            var pulsar = document.getElementById('tasaPulsa');
            if (pulsar) pulsar.remove();

            btnTasa.disabled = true;
            btnTasa.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando BCV...';

            fetch('?pagina=tasaEcommerce', { method: 'POST' })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) {
                        btnTasa.disabled = false;
                        btnTasa.innerHTML = 'TASA $/BCV HOY <span class="d-inline-block align-middle"><i class="fas fa-sync-alt"></i></span>';
                        var err = document.createElement('div');
                        err.className = 'small text-danger fw-bold';
                        err.textContent = data.message || 'Error al actualizar la tasa';
                        tasaAccion.appendChild(err);
                        return;
                    }

                    if (tasaValor) tasaValor.textContent = fmt(data.tasa);
                    if (tasaAlert) {
                        tasaAlert.textContent = 'Tasa actualizada (0 min)';
                        tasaAlert.className = 'tasa-alert text-success fw-bold';
                    }

                    if (totalBsEfectivo) totalBsEfectivo.textContent = fmt(totalUSD * data.tasa);
                    if (totalBsReportar) totalBsReportar.textContent = fmt(totalUSD * data.tasa);

                    tasaAccion.innerHTML = '';

                    if (btnYaPague) {
                        btnYaPague.removeAttribute('disabled');
                        btnYaPague.style.pointerEvents = '';
                        btnYaPague.style.opacity = '';
                    }
                })
                .catch(function () {
                    btnTasa.disabled = false;
                    btnTasa.innerHTML = 'TASA $/BCV HOY';
                    var err = document.createElement('div');
                    err.className = 'small text-danger fw-bold';
                    err.textContent = 'Error de conexión. Intenta nuevamente.';
                    tasaAccion.appendChild(err);
                });
        });
    }

    function cargarTasaCambio() {
        fetch('?pagina=tasaCambio&action=obtener')
        .then(r => r.json())
        .then(data => {
            const tasa = parseFloat(data.tasa) || 0;
            const vigencia = data.vigencia || {};
            const fecha = data.fecha || '';

            document.querySelectorAll('.tasa-valor').forEach(el => {
                el.textContent = tasa.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            });

            var tasaAlert = document.getElementById('tasaAlert');
            if (tasaAlert) {
                tasaAlert.textContent = vigencia.mensaje || '';
                tasaAlert.className = 'tasa-alert ' + (vigencia.expirada ? 'text-danger fw-bold' : 'text-success');
            }

            var totalBsEfectivo = document.getElementById('totalBsEfectivo');
            var totalBsReportar = document.getElementById('totalBsReportar');
            
            var totalBsCalculado = (totalUSD * tasa).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (totalBsEfectivo) totalBsEfectivo.textContent = totalBsCalculado;
            if (totalBsReportar) totalBsReportar.textContent = totalBsCalculado;
        })
        .catch(err => console.error('Error cargando tasa:', err));
    }

    document.addEventListener('DOMContentLoaded', cargarTasaCambio);

    var btnTasaControlador = document.getElementById('btnActualizarTasa');
    if (btnTasaControlador) {
        btnTasaControlador.addEventListener('click', function () {
            btnTasaControlador.disabled = true;
            btnTasaControlador.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando BCV...';

            fetch('?pagina=tasaCambio&action=scrapear', { method: 'POST' })
                .then(r => r.json())
                .then(data => {
                    btnTasaControlador.disabled = false;
                    btnTasaControlador.innerHTML = 'TASA $/BCV HOY';
                    if(data.success) {
                        cargarTasaCambio();
                        
                        var btnYaPague = document.getElementById('btnYaPague');
                        if (btnYaPague) {
                            btnYaPague.removeAttribute('disabled');
                            btnYaPague.style.pointerEvents = '';
                            btnYaPague.style.opacity = '';
                        }
                    } else {
                        alert(data.message || 'No se pudo actualizar la tasa');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btnTasaControlador.disabled = false;
                    btnTasaControlador.innerHTML = 'TASA $/BCV HOY';
                });
        });
    }
})();
</script>
</body>
</html>