(function () {
    var totalUSD = parseFloat(document.body.getAttribute('data-total-usd')) || 0;
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

    function actualizarTasa() {
        if (!btnTasa) return;

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
    }

    if (btnTasa) {
        btnTasa.addEventListener('click', actualizarTasa);

        actualizarTasa();
    }
})();