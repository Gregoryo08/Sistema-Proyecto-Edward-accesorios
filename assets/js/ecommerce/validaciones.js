
function capitalizarPalabras(cadena) {
    if (!cadena) return "";
    cadena = cadena.toLowerCase();
    let palabras = cadena.split(' ');
    for (let i = 0; i < palabras.length; i++) {
        if (palabras[i].length > 0) {
            palabras[i] = palabras[i].charAt(0).toUpperCase() + palabras[i].slice(1);
        }
    }
    return palabras.join(' ');
}

function validarCedula(cedula) {
    var patron = /^(V|E|J|P)-\d{7,12}$/;
    return patron.test(cedula);
}

function validarCedulaVenezolana(cedula) {
    var soloNumeros = String(cedula).replace(/[^0-9]/g, '');
    return soloNumeros.length >= 7 && soloNumeros.length <= 8;
}

function validarReferencia(referencia) {
    var soloNumeros = String(referencia).replace(/[^0-9]/g, '');
    console.log('📌 Referencia ingresada:', referencia);
    console.log('📌 Solo números:', soloNumeros);
    console.log('📌 Longitud:', soloNumeros.length);
    return soloNumeros.length >= 12 && soloNumeros.length <= 13;
}

function validarFechaNacimiento(fecha) {
    if (!fecha) return false;
    var partes = fecha.split('-');
    if (partes.length !== 3) return false;
    var año = parseInt(partes[0], 10);
    var añoActual = new Date().getFullYear();
    return !isNaN(año) && año >= 1936 && año <= añoActual;
}

function validarCorreo(correo) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
}

function validarTelefono(telefono) {
    var soloNumeros = telefono.replace(/[^0-9]/g, '');
    return soloNumeros.length >= 10 && soloNumeros.length <= 11;
}

function validarTextoNoVacio(valor) {
    return typeof valor === 'string' && valor.trim().length > 0;
}

function validarSoloLetras(valor) {
    return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor);
}

function validarLongitudMinima(valor, min) {
    return typeof valor === 'string' && valor.trim().length >= min;
}

function validarMonto(monto) {
    var numero = normalizarMonto(monto);
    return !isNaN(numero) && numero > 0;
}

function normalizarMonto(valor) {
    var s = String(valor == null ? '' : valor).replace(/[^\d.,]/g, '').trim();
    if (!s) return NaN;
    var idxPunto = s.lastIndexOf('.');
    var idxComa = s.lastIndexOf(',');
    if (idxPunto === -1 && idxComa === -1) {
        return parseInt(s, 10);
    }
    var idxDecimal = (idxPunto > idxComa) ? idxPunto : idxComa;
    var digitosDecimales = s.length - 1 - idxDecimal;
    if (digitosDecimales < 1 || digitosDecimales > 2) {
        return parseInt(s.replace(/[.,]/g, ''), 10);
    }
    var parteEntera = s.slice(0, idxDecimal).replace(/[.,]/g, '');
    var num = parseFloat(parteEntera + '.' + s.slice(idxDecimal + 1));
    return isNaN(num) ? NaN : num;
}

function mostrarError(mensaje) {
    if (typeof Swal !== 'undefined') {
        Swal.fire('Error', mensaje, 'error');
    } else {
        alert('❌ Error: ' + mensaje);
    }
}

function mostrarExito(mensaje, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire('Éxito', mensaje, 'success').then(callback || function() {});
    } else {
        alert('✅ Éxito: ' + mensaje);
        if (callback) callback();
    }
}

function validarYEnviar() {
    let ref = document.querySelector('[name="referencia"]')?.value?.trim() || '';
    let banco = document.querySelector('[name="banco_emisor"]')?.value?.trim() || '';
    let telf = document.querySelector('[name="telefono"]')?.value?.trim() || '';
    let montoRaw = document.querySelector('[name="monto"]')?.value?.trim() || '';
    var tipoCed = document.querySelector('[name="tipo_cedula_pagador"]')?.value || 'V';
    let cedulaRaw = document.querySelector('[name="cedula_pagador"]')?.value?.trim() || '';

    if (!/^[0-9]+$/.test(ref)) {
        mostrarError('La referencia solo debe contener números');
        return;
    }
    if (!validarReferencia(ref)) {
        mostrarError('La referencia debe tener entre 12 y 13 dígitos');
        return;
    }

    if (!validarTextoNoVacio(banco) || banco.length < 3) {
        mostrarError('El banco emisor es obligatorio');
        return;
    }

    if (!/^[0-9]+$/.test(telf)) {
        mostrarError('El teléfono solo debe contener números');
        return;
    }
    if (!validarTelefono(telf)) {
        mostrarError('El teléfono debe tener al menos 10 dígitos');
        return;
    }

    if (!validarTextoNoVacio(montoRaw)) {
        mostrarError('El monto es obligatorio');
        return;
    }
    var montoNormalizado = normalizarMonto(montoRaw);
    if (isNaN(montoNormalizado) || montoNormalizado <= 0) {
        mostrarError('El monto debe ser un número válido mayor a 0');
        return;
    }

    var totalUsdInput = document.getElementById('total-usd');
    var totalUsdValor = parseFloat(totalUsdInput ? totalUsdInput.value : '0') || 0;
    var tasaEl = document.querySelector('.tasa-valor');
    var tasaActual = parseFloat(tasaEl ? tasaEl.textContent : '0') || 0;
    if (tasaActual > 0) {
        var equivUsd = montoNormalizado / tasaActual;
        if (equivUsd < totalUsdValor) {
            mostrarError('El monto en dólares (Bs. ' + montoNormalizado.toFixed(2) + ' → $' + equivUsd.toFixed(2) + ') no cubre el total del pedido ($' + totalUsdValor.toFixed(2) + '). Le falta dinero.');
            return;
        }
    }

    if (cedulaRaw.length > 0) {
        var soloNumeros = cedulaRaw.replace(/[^0-9]/g, '');
        if (!validarCedulaVenezolana(soloNumeros)) {
            mostrarError('La cédula debe tener entre 7 y 8 dígitos numéricos');
            return;
        }
        document.querySelector('[name="cedula_pagador"]').value = tipoCed + '-' + soloNumeros;
    }

    var montoInput = document.querySelector('[name="monto"]');
    if (montoInput) montoInput.value = montoNormalizado.toFixed(2);

    enviarReporte();
}

function enviarReporte() {
    const form = document.getElementById('form-reporte');
    if (!form) {
        mostrarError('Formulario no encontrado');
        return;
    }
    
    const btn = document.getElementById('btn-enviar');
    if (btn && btn.disabled) return;
    
    const formData = new FormData(form);
    
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
    }
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Enviando reporte...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    }
    
    fetch('?pagina=procesarReporte', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarExito(data.message || 'Tu pago ha sido reportado correctamente.', () => {
                window.location.href = data.redirect || '?pagina=misPedidos';
            });
        } else {
            if (data.message && data.message.toLowerCase().includes('ya existe un reporte de pago')) {
                mostrarPagoDuplicado(data.message, data.redirect);
            } else {
                mostrarError(data.message || 'No se pudo procesar el reporte');
            }
            
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> CONFIRMAR TODO Y ENVIAR';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Problemas de conexión: ' + error.message);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> CONFIRMAR TODO Y ENVIAR';
        }
    });
}

function mostrarPagoDuplicado(mensaje, redirectUrl) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: '💳 ¡Ya reportaste este pago!',
            html: `
                <div style="text-align: left; padding: 10px 0;">
                    <p style="font-size: 1.1rem;">${mensaje || 'Ya existe un reporte de pago para este pedido.'}</p>
                    <hr>
                    <p class="text-muted">¿Qué deseas hacer?</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-box"></i> Ver Mis Pedidos',
            cancelButtonText: '<i class="fas fa-store"></i> Seguir comprando',
            reverseButtons: true,
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.6)',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = redirectUrl || '?pagina=misPedidos';
            } else {
                window.location.href = '?pagina=web_Catalogo';
            }
        });
    } else {
        if (confirm(mensaje + '\n\n¿Ir a Mis Pedidos?')) {
            window.location.href = redirectUrl || '?pagina=misPedidos';
        } else {
            window.location.href = '?pagina=web_Catalogo';
        }
    }
}

console.log('✅ validaciones.js cargado correctamente');
console.log('✅ Funciones disponibles: validarYEnviar, enviarReporte, mostrarPagoDuplicado, validarCedula, validarMonto');