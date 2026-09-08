// =============================================
// VALIDACIONES REUTILIZABLES - validaciones.js
// =============================================

/**
 * Capitalizar palabras (ej: "gustavo miguel" → "Gustavo Miguel")
 */
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

/**
 * Validar formato de cédula (V-12345678, E-12345678, J-12345678, P-12345678)
 * ⚠️ IMPORTANTE: Valida PREFIJO y GUION (no eliminar)
 */
function validarCedula(cedula) {
    var patron = /^(V|E|J|P)-\d{7,12}$/;
    return patron.test(cedula);
}

/**
 * Validar cédula venezolana (solo números, 7-8 dígitos)
 */
function validarCedulaVenezolana(cedula) {
    var soloNumeros = String(cedula).replace(/[^0-9]/g, '');
    return soloNumeros.length >= 7 && soloNumeros.length <= 8;
}

/**
 * Validar referencia bancaria (solo números, 12-13 dígitos)
 */
function validarReferencia(referencia) {
    var soloNumeros = String(referencia).replace(/[^0-9]/g, '');
    console.log('📌 Referencia ingresada:', referencia);
    console.log('📌 Solo números:', soloNumeros);
    console.log('📌 Longitud:', soloNumeros.length);
    return soloNumeros.length >= 12 && soloNumeros.length <= 13;
}

/**
 * Validar fecha de nacimiento (año >= 1936 y <= actual)
 */
function validarFechaNacimiento(fecha) {
    if (!fecha) return false;
    var partes = fecha.split('-');
    if (partes.length !== 3) return false;
    var año = parseInt(partes[0], 10);
    var añoActual = new Date().getFullYear();
    return !isNaN(año) && año >= 1936 && año <= añoActual;
}

/**
 * Validar correo electrónico
 */
function validarCorreo(correo) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo);
}

/**
 * Validar teléfono (solo números, mínimo 10 dígitos)
 */
function validarTelefono(telefono) {
    var soloNumeros = telefono.replace(/[^0-9]/g, '');
    return soloNumeros.length >= 10 && soloNumeros.length <= 11;
}

/**
 * Validar que el campo no esté vacío
 */
function validarTextoNoVacio(valor) {
    return typeof valor === 'string' && valor.trim().length > 0;
}

/**
 * Validar que solo tenga letras y espacios (para nombres)
 */
function validarSoloLetras(valor) {
    return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(valor);
}

/**
 * Validar longitud mínima
 */
function validarLongitudMinima(valor, min) {
    return typeof valor === 'string' && valor.trim().length >= min;
}

/**
 * Validar monto (número positivo)
 * Soporta inglés "198.99" y español "198,99"
 */
function validarMonto(monto) {
    var normalizado = monto.replace(',', '.');
    var numero = parseFloat(normalizado);
    return !isNaN(numero) && numero > 0;
}

/**
 * Mostrar error (SweetAlert o alert)
 */
function mostrarError(mensaje) {
    if (typeof Swal !== 'undefined') {
        Swal.fire('Error', mensaje, 'error');
    } else {
        alert('❌ Error: ' + mensaje);
    }
}

/**
 * Mostrar éxito (SweetAlert o alert)
 */
function mostrarExito(mensaje, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire('Éxito', mensaje, 'success').then(callback || function() {});
    } else {
        alert('✅ Éxito: ' + mensaje);
        if (callback) callback();
    }
}

/**
 * Navegación entre pasos del formulario
 */
function siguientePaso(numero) {
    console.log('🔄 Cambiando al paso:', numero);
    document.querySelectorAll('[id^="paso"], #resumen').forEach(el => el.style.display = 'none');
    if (numero === 1) {
        var paso1 = document.getElementById('paso1');
        if (paso1) paso1.style.display = 'block';
    } else {
        var paso = document.getElementById('paso' + numero);
        if (paso) paso.style.display = 'block';
    }
}

/**
 * Mostrar resumen con validaciones
 */
function mostrarResumen() {
    let ref = document.querySelector('[name="referencia"]')?.value?.trim() || '';
    let banco = document.querySelector('[name="banco_emisor"]')?.value?.trim() || '';
    let telf = document.querySelector('[name="telefono"]')?.value?.trim() || '';
    let montoRaw = document.querySelector('[name="monto"]')?.value?.trim() || '';
    var tipoCed = document.querySelector('[name="tipo_cedula_pagador"]')?.value || 'V';
    let cedulaRaw = document.querySelector('[name="cedula_pagador"]')?.value?.trim() || '';

    // Validar referencia: solo números, 12-13 dígitos
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

    // Validar teléfono: solo números, mínimo 10 dígitos
    if (!/^[0-9]+$/.test(telf)) {
        mostrarError('El teléfono solo debe contener números');
        return;
    }
    if (!validarTelefono(telf)) {
        mostrarError('El teléfono debe tener al menos 10 dígitos');
        return;
    }

    // Validar monto
    if (!validarTextoNoVacio(montoRaw)) {
        mostrarError('El monto es obligatorio');
        return;
    }
    var montoNormalizado = montoRaw.replace(',', '.');
    if (isNaN(parseFloat(montoNormalizado)) || parseFloat(montoNormalizado) <= 0) {
        mostrarError('El monto debe ser un número válido mayor a 0');
        return;
    }

    // Validar que el equivalente en USD cubra el total del pedido
    var totalUsdInput = document.getElementById('total-usd');
    var totalUsdValor = parseFloat(totalUsdInput ? totalUsdInput.value : '0') || 0;
    var tasaEl = document.querySelector('.tasa-valor');
    var tasaActual = parseFloat(tasaEl ? tasaEl.textContent : '0') || 0;
    if (tasaActual > 0) {
        var equivUsd = parseFloat(montoNormalizado) / tasaActual;
        if (equivUsd < totalUsdValor) {
            mostrarError('El monto en dólares (Bs. ' + parseFloat(montoNormalizado).toFixed(2) + ' → $' + equivUsd.toFixed(2) + ') no cubre el total del pedido ($' + totalUsdValor.toFixed(2) + '). Le falta dinero.');
            return;
        }
    }

    // Validar cédula SOLO SI fue ingresada (es opcional)
    if (cedulaRaw.length > 0) {
        var soloNumeros = cedulaRaw.replace(/[^0-9]/g, '');
        if (!validarCedulaVenezolana(soloNumeros)) {
            mostrarError('La cédula debe tener entre 7 y 8 dígitos numéricos');
            return;
        }
        var cedulaCompleta = tipoCed + '-' + soloNumeros;
        document.querySelector('[name="cedula_pagador"]').value = cedulaCompleta;
    }
    // Si está vacía, no validar (es opcional)

    var montoLimpio = parseFloat(montoNormalizado).toFixed(2);
    var montoInput = document.querySelector('[name="monto"]');
    if (montoInput) montoInput.value = montoLimpio;

    var rRef = document.getElementById('r_ref');
    var rBanco = document.getElementById('r_banco');
    var rTelf = document.getElementById('r_telf');
    var rMonto = document.getElementById('r_monto');

    if (rRef) rRef.textContent = ref;
    if (rBanco) rBanco.textContent = banco;
    if (rTelf) rTelf.textContent = telf;
    if (rMonto) rMonto.textContent = montoLimpio;

    document.querySelectorAll('[id^="paso"]').forEach(el => el.style.display = 'none');
    var resumen = document.getElementById('resumen');
    if (resumen) resumen.style.display = 'block';
}

/**
 * Enviar reporte al controlador (AJAX)
 */
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

/**
 * ✅ MODAL PARA PAGO DUPLICADO
 */
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
            // ✅ BOTÓN 1: Ver Mis Pedidos
            confirmButtonText: '<i class="fas fa-box"></i> Ver Mis Pedidos',
            // ✅ BOTÓN 2: Seguir comprando
            cancelButtonText: '<i class="fas fa-store"></i> Seguir comprando',
            reverseButtons: true,
            background: '#fff',
            backdrop: 'rgba(0,0,0,0.6)',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // ✅ Redirige a Mis Pedidos
                window.location.href = redirectUrl || '?pagina=misPedidos';
            } else {
                // ✅ Redirige al Catálogo
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
console.log('✅ Funciones disponibles: siguientePaso, mostrarResumen, enviarReporte, mostrarPagoDuplicado, validarCedula, validarMonto');