(function () {
    var btn = document.getElementById('btnActualizarTasa');
    if (!btn) return;

    function fmt(n) {
        return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function actualizarTasa() {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando tasa...';

        fetch('?pagina=tasaEcommerce', { method: 'POST' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt"></i> ACTUALIZAR TASA';
                    mostrarError(data.message || 'Error al actualizar la tasa');
                    return;
                }

                var tasa = parseFloat(data.tasa) || 0;

                document.querySelectorAll('.tasa-valor').forEach(function (el) {
                    el.textContent = tasa.toFixed(2);
                });
                var fechaEl = document.querySelector('.tasa-fecha');
                if (fechaEl && data.fecha) fechaEl.textContent = data.fecha;

                var totalUSD = parseFloat(document.getElementById('total-usd').value) || 0;
                var bsEl = document.getElementById('total-bs-reportar');
                if (bsEl && totalUSD > 0 && tasa > 0) {
                    bsEl.textContent = fmt(totalUSD * tasa);
                }

                var btn2 = document.getElementById('btnActualizarTasa');
                if (btn2 && btn2.parentNode) btn2.parentNode.remove();
            })
            .catch(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-sync-alt"></i> ACTUALIZAR TASA';
                mostrarError('Error de conexión. Intenta nuevamente.');
            });
    }

    btn.addEventListener('click', actualizarTasa);
})();