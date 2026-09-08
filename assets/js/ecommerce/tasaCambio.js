function cargarTasaCambio() {
    fetch('?pagina=tasaCambio&action=obtener')
    .then(r => r.json())
    .then(data => {
        const tasa = parseFloat(data.tasa) || 0;
        const vigencia = data.vigencia || {};
        const fecha = data.fecha || '';

        document.querySelectorAll('.tasa-valor').forEach(el => {
            el.textContent = tasa.toFixed(2);
        });
        document.querySelectorAll('.tasa-fecha').forEach(el => {
            el.textContent = fecha;
        });

        var ddValor = document.getElementById('tasa-dd-valor');
        if (ddValor) ddValor.textContent = tasa.toFixed(2);
        var ddActual = document.getElementById('tasa-dd-actual');
        if (ddActual) ddActual.textContent = tasa.toFixed(2);
        var ddFecha = document.getElementById('tasa-dd-fecha');
        if (ddFecha) ddFecha.textContent = fecha;
        var ddAlert = document.getElementById('tasa-dd-alert');
        if (ddAlert) {
            var icono = vigencia.expirada ? 'fa-exclamation-triangle' : 'fa-check-circle';
            ddAlert.className = 'alert ' + (vigencia.expirada ? 'alert-danger' : 'alert-success') + ' py-1 mb-2';
            ddAlert.innerHTML = '<i class="fas ' + icono + ' me-1"></i> ' +
                (vigencia.mensaje || 'Tasa desactualizada (>24h)');
        }

        document.querySelectorAll('.monto-usd').forEach(el => {
            const usd = parseFloat(el.dataset.usd) || parseFloat(el.textContent.replace(/[^0-9.]/g, ''));
            if (usd > 0) {
                const bs = (usd * tasa).toFixed(2);
                const bsEl = document.getElementById('monto-bs-' + el.dataset.index);
                if (bsEl) bsEl.textContent = bs;
            }
        });

        if (vigencia.expirada) {
            document.querySelectorAll('.tasa-alert').forEach(el => {
                el.className = 'alert alert-danger tasa-alert mt-2';
                el.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> ' +
                    (vigencia.mensaje || 'Tasa desactualizada (>24h)');
                el.style.display = 'block';
            });
        }
    })
    .catch(err => console.error('Error cargando tasa:', err));
}

document.addEventListener('DOMContentLoaded', cargarTasaCambio);
