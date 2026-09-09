// =============================================
// REPORTE DE PAGOS ONLINE - INICIALIZACIÓN
// =============================================

console.log('✅ reporte_pagos_online.js cargado');

// =============================================
// CARGAR BANCOS (select dinámico)
// =============================================

function cargarBancos() {
    var select = document.querySelector('[name="banco_emisor"]');
    if (!select) return;
    select.innerHTML = '<option value="">Cargando...</option>';
    fetch('?pagina=listarBancos')
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">Seleccione su banco</option>';
            if (data.success && data.bancos && data.bancos.length > 0) {
                data.bancos.forEach(function(b) {
                    var opt = document.createElement('option');
                    opt.value = b.nombre_banco;
                    opt.textContent = b.nombre_banco;
                    select.appendChild(opt);
                });
            } else {
                select.innerHTML = '<option value="">No hay bancos disponibles</option>';
            }
        })
        .catch(function() {
            select.innerHTML = '<option value="">Error al cargar bancos</option>';
        });
}

// =============================================
// REAL-TIME: limpiar errores al escribir
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado, inicializando reporte');

    var refInput = document.querySelector('[name="referencia"]');
    if (refInput) {
        refInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            document.getElementById('msg-ref').style.display = 'none';
        });
    }

    var telfInput = document.querySelector('[name="telefono"]');
    if (telfInput) {
        telfInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            document.getElementById('msg-telf').style.display = 'none';
        });
    }

    var bancoSelect = document.querySelector('[name="banco_emisor"]');
    if (bancoSelect) {
        bancoSelect.addEventListener('change', function() {
            document.getElementById('msg-banco').style.display = 'none';
        });
    }

    var montoInput = document.querySelector('[name="monto"]');
    if (montoInput) {
        montoInput.addEventListener('input', function() {
            let montoRaw = this.value.replace(/,/g, '.');
            let montoBs = parseFloat(montoRaw);
            var msgMonto = document.getElementById('msg-monto');

            var equivEl = document.getElementById('monto-usd-equiv');
            var msgUsdEl = document.getElementById('msg-monto-usd');

            if (!isNaN(montoBs) && montoBs > 0) {
                var tasaEl = document.querySelector('.tasa-valor');
                var tasa = parseFloat(tasaEl ? tasaEl.textContent : '0') || 0;

                var equivUSD = (tasa > 0) ? (montoBs / tasa) : 0;

                if (equivUSD > 0) {
                    equivEl.textContent = '$' + equivUSD.toFixed(2) + ' USD';
                    equivEl.classList.remove('text-danger');
                    equivEl.classList.add('text-success');

                    var totalUSDInput = document.getElementById('total-usd');
                    var totalUSD = parseFloat(totalUSDInput ? totalUSDInput.value : '0') || 0;

                    if (equivUSD < totalUSD) {
                        msgUsdEl.style.display = 'block';
                    } else {
                        msgUsdEl.style.display = 'none';
                    }
                } else {
                    equivEl.textContent = '$0.00 USD';
                    equivEl.classList.remove('text-success');
                    equivEl.classList.add('text-danger');
                    msgUsdEl.style.display = 'none';
                }
            } else {
                if (equivEl) equivEl.textContent = '$0.00 USD';
                if (msgUsdEl) msgUsdEl.style.display = 'none';
            }

            if (msgMonto) {
                msgMonto.style.display = 'none';
            }
        });
    }

    var cedulaPagador = document.querySelector('[name="cedula_pagador"]');
    if (cedulaPagador) {
        cedulaPagador.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            document.getElementById('msg-cedula-page').style.display = 'none';
        });
    }

    const primerInput = document.querySelector('[name="referencia"]');
    if (primerInput) {
        primerInput.focus();
    }

    cargarBancos();

    console.log('✅ validarYEnviar:', typeof validarYEnviar);
    console.log('✅ enviarReporte:', typeof enviarReporte);
});