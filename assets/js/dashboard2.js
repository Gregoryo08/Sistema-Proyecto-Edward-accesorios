document.addEventListener("DOMContentLoaded", function () {
    function formatCurrency(amount) {
        if (typeof amount !== 'number') {
            amount = parseFloat(amount);
        }
        if (isNaN(amount)) {
            return '$0.00';
        }
        return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
    }

    function obtenerNombreMes(numeroMes) {
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        return meses[parseInt(numeroMes) - 1] || "";
    }

    function cargarDatosDashboardCliente(mesFiltro = null, anioFiltro = null) {
        const payload = {};
        if (mesFiltro && anioFiltro) {
            payload.mes = mesFiltro;
            payload.anio = anioFiltro;
        }

        fetch('?pagina=principal_cliente&action=DatosDashboardCliente', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data && !data.error) {
                let nombreMesReal = data.mesActualInfo ? obtenerNombreMes(data.mesActualInfo.mes) : "";

                const badgeCompromiso = document.querySelector('.badge.bg-warning'); 
                if (badgeCompromiso) {
                    badgeCompromiso.innerText = `Compromiso de ${nombreMesReal}`;
                }

                const lblCuotaTotal = document.getElementById('lbl-cuota-total');
                if (lblCuotaTotal) {
                    lblCuotaTotal.innerText = `Tu cuota total (${nombreMesReal}): ${formatCurrency(data.compromisoMesActual || 0)}`;
                }
                
                const lblInfo = document.getElementById('lbl-info-cuotas');
                if (lblInfo) {
                    lblInfo.innerText = `Tienes compromisos programados para este periodo en tus financiamientos activos.`;
                }

                if (data.proximoEquipo) {
                    const eq = data.proximoEquipo;
                    document.getElementById('prox-nombre-equipo').innerText = `${eq.equipo || 'Financiamiento #' + eq.id}`;
                    document.getElementById('prox-cuotas-num').innerText = `Cuota ${eq.numero_cuota}/${eq.total_cuotas}`;
                    document.getElementById('prox-monto').innerText = formatCurrency(eq.monto || 0);
                    document.getElementById('prox-vencimiento').innerHTML = `<i class="bi bi-clock-fill me-1"></i> Vence el ${eq.fecha_vencimiento}`;
                    document.getElementById('prox-progreso-texto').innerText = `Estado: ${eq.estado_cuota}`;

                    const total = parseInt(eq.total_cuotas) || 1;
                    const pagadas = parseInt(eq.cuotas_pagadas) || 0;
                    const porcentaje = Math.round((pagadas / total) * 100);

                    const barra = document.getElementById('prox-barra-progreso');
                    if (barra) {
                        barra.style.width = `${porcentaje}%`;
                        barra.setAttribute('aria-valuenow', porcentaje);
                    }
                } else {
                    document.getElementById('prox-nombre-equipo').innerText = "Sin equipo activo";
                    document.getElementById('prox-cuotas-num').innerText = "Cuota - de -";
                    document.getElementById('prox-monto').innerText = formatCurrency(0);
                    document.getElementById('prox-vencimiento').innerHTML = `<i class="bi bi-clock-fill me-1"></i> Sin fecha límite`;
                    document.getElementById('prox-progreso-texto').innerText = "Sin financiamiento";
                    
                    const barra = document.getElementById('prox-barra-progreso');
                    if (barra) {
                        barra.style.width = `0%`;
                        barra.setAttribute('aria-valuenow', 0);
                    }
                }

                const selectMes = document.getElementById('select-mes') || document.getElementById('filtro-mes');
                if (selectMes && selectMes.tagName === 'SELECT' && data.mesesDisponibles) {
                    selectMes.innerHTML = '';
                    data.mesesDisponibles.forEach(m => {
                        let option = document.createElement('option');
                        option.value = `${m.anio}-${m.mes}`;
                        option.innerText = `${obtenerNombreMes(m.mes)} ${m.anio} (${m.total_cuotas} cuotas)`;
                        if (data.mesSeleccionado && data.mesSeleccionado.mes == m.mes && data.mesSeleccionado.anio == m.anio) {
                            option.selected = true;
                        }
                        selectMes.appendChild(option);
                    });
                }

                const tbody = document.getElementById('tabla-cuotas-body');
                tbody.innerHTML = '';

                let cantidadCuotasActuales = 0;
                if (data.financiamientosActivos && data.financiamientosActivos.length > 0) {
                    cantidadCuotasActuales = data.financiamientosActivos.length;
                    data.financiamientosActivos.forEach(item => {
                        let esPagada = item.estado.toLowerCase() === 'pagada' || item.estado.toLowerCase() === 'pagado';
                        let badgeClase = esPagada ? 'bg-secondary' : 'bg-success';
                        let estadoTexto = esPagada ? 'Pagada' : 'Activo';

                        let tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${item.equipo || 'Financiamiento #' + item.id}</td>
                            <td>#${item.numero_cuota}</td>
                            <td>${item.fecha_vencimiento}</td>
                            <td class="fw-bold text-primary">${formatCurrency(item.monto || 0)}</td>
                            <td><span class="badge ${badgeClase}">${estadoTexto}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-3">No hay registros de cuotas para este mes.</td></tr>`;
                }

                let nombreMesFiltro = data.mesSeleccionado ? obtenerNombreMes(data.mesSeleccionado.mes) : "";
                const textoAlerta = document.getElementById('texto-alerta-mes');
                if (textoAlerta) {
                    textoAlerta.innerHTML = `Mostrando cuotas programadas para ${nombreMesFiltro} (${cantidadCuotasActuales} cuotas). Total acumulado: <span class="fw-bold text-dark">${formatCurrency(data.compromisoMes || 0)}</span>`;
                }

            } else {
                console.error("Error al cargar los datos del dashboard:", data.error || "Respuesta vacía");
            }
        })
        .catch(error => {
            console.error("Error en la petición AJAX:", error);
        });
    }

    cargarDatosDashboardCliente();

    const selectMes = document.getElementById('select-mes') || document.getElementById('filtro-mes');
    if (selectMes) {
        selectMes.addEventListener('change', function() {
            const partes = this.value.split('-');
            if(partes.length === 2) {
                cargarDatosDashboardCliente(partes[1], partes[0]);
            }
        });
    }
});