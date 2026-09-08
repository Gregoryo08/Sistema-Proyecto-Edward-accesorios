document.addEventListener('DOMContentLoaded', function () {
    const btnAgregarRepuesto = document.getElementById('btnAgregarRepuesto');
    const cuerpoRepuestos = document.getElementById('orden_repuestos_rows');
    const marcaSelect = document.getElementById('orden_marca_tecnico');
    const totalRepuestos = document.getElementById('orden_repuestos_total_tecnico');
    const totalOrden = document.getElementById('orden_total_tecnico');
    const btnGuardarOrden = document.getElementById('btnGuardarOrdenTecnico');

    function cargarMarcas() {
        if (!marcaSelect) return;
        $.get('?pagina=orden&ajax=true&x=marcas', function (data) {
            let marcas = data;
            try { marcas = (typeof data === 'string') ? JSON.parse(data) : data; } catch (e) {}
            marcaSelect.innerHTML = '<option value="">Seleccione una marca</option>';
            if (Array.isArray(marcas) && marcas.length > 0) {
                marcas.forEach(function (marca) {
                    const option = document.createElement('option');
                    option.value = marca.id_marca || marca.id_marca;
                    option.textContent = marca.nombre_marca || marca.nombre_marca;
                    marcaSelect.appendChild(option);
                });
            }
        }).fail(function () {
            marcaSelect.innerHTML = '<option value="">No se cargaron marcas</option>';
        });
    }

    function formatCurrency(value) {
        return '$ ' + Number(value || 0).toFixed(2);
    }

    function calcularSubtotal(row) {
        const cantidad = Number(row.querySelector('.repuesto-cantidad').value || 0);
        const precio = Number(row.querySelector('.repuesto-precio').value || 0);
        const subtotal = cantidad * precio;
        row.querySelector('.repuesto-subtotal').textContent = formatCurrency(subtotal);
        return subtotal;
    }

    function recalcularTotales() {
        let repuestosTotal = 0;
        cuerpoRepuestos.querySelectorAll('.orden-repuesto-row').forEach(row => {
            repuestosTotal += calcularSubtotal(row);
        });
        totalRepuestos.value = formatCurrency(repuestosTotal);
        totalOrden.value = formatCurrency(repuestosTotal);
    }

    function crearFilaRepuesto() {
        const fila = document.createElement('tr');
        fila.className = 'orden-repuesto-row';
        fila.innerHTML = `
            <td><input type="text" class="form-control form-control-sm repuesto-nombre" placeholder="Ej: Pantalla"></td>
            <td><input type="number" min="1" value="1" class="form-control form-control-sm repuesto-cantidad"></td>
            <td><input type="number" min="0" step="0.01" value="0.00" class="form-control form-control-sm repuesto-precio"></td>
            <td class="text-end repuesto-subtotal">$ 0.00</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-repuesto">x</button></td>
        `;
        return fila;
    }

    btnAgregarRepuesto.addEventListener('click', function () {
        cuerpoRepuestos.appendChild(crearFilaRepuesto());
        recalcularTotales();
    });

    cuerpoRepuestos.addEventListener('input', function (event) {
        if (event.target.classList.contains('repuesto-cantidad') || event.target.classList.contains('repuesto-precio')) {
            recalcularTotales();
        }
    });

    cuerpoRepuestos.addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-eliminar-repuesto')) {
            const row = event.target.closest('.orden-repuesto-row');
            if (row) {
                row.remove();
                recalcularTotales();
            }
        }
    });


    cargarMarcas();

    function aplicarRojo(selector) {
        try { 
            const el = document.querySelector(selector);
            if (el) {
                el.style.border = '1px solid rgb(158, 3, 3)';
                el.style.boxShadow = '0 0 8px rgb(158, 3, 3)';
            }
        } catch (e) {}
    }

    function limpiarEstilos() {
        try {
            document.querySelectorAll('#formularioOrdenTecnico input, #formularioOrdenTecnico select, #formularioOrdenTecnico textarea').forEach(function (el) {
                el.style.border = '';
                el.style.boxShadow = '';
            });
        } catch (e) {}
    }

    function validarFormularioOrden() {
        limpiarEstilos();
        let valido = true;
        const clienteVal = document.getElementById('orden_cliente_tecnico').value.trim();
        const marcaVal = marcaSelect.value;
        const equipoVal = document.getElementById('orden_equipo_tecnico').value.trim();
        const imeiVal = document.getElementById('orden_imei_tecnico').value.trim();
        const repuestosRows = Array.from(cuerpoRepuestos.querySelectorAll('.orden-repuesto-row'));

        if (!clienteVal) { aplicarRojo('#orden_cliente_tecnico'); valido = false; }
        if (!marcaVal) { aplicarRojo('#orden_marca_tecnico'); valido = false; }
        if (!equipoVal) { aplicarRojo('#orden_equipo_tecnico'); valido = false; }
        if (!imeiVal) { aplicarRojo('#orden_imei_tecnico'); valido = false; }

        // validar repuestos
        let repuestosTotalCalc = 0;
        for (let i = 0; i < repuestosRows.length; i++) {
            const row = repuestosRows[i];
            const nombre = row.querySelector('.repuesto-nombre').value.trim();
            const cantidad = Number(row.querySelector('.repuesto-cantidad').value || 0);
            const precio = Number(row.querySelector('.repuesto-precio').value || 0);
            if (!nombre) { aplicarRojo('.repuesto-nombre'); valido = false; }
            if (!(cantidad > 0)) { aplicarRojo('.repuesto-cantidad'); valido = false; }
            if (!(precio >= 0)) { aplicarRojo('.repuesto-precio'); valido = false; }
            repuestosTotalCalc += cantidad * precio;
        }

        const totalOrdenVal = Number((totalOrden.value || '$ 0.00').toString().replace('S/ ', '').replace('$ ', ''));

        // comparacion con tolerancia centavos
        if (Math.round(repuestosTotalCalc * 100) / 100 !== Math.round(totalOrdenVal * 100) / 100) {
            aplicarRojo('#orden_repuestos_total_tecnico');
            aplicarRojo('#orden_total_tecnico');
            valido = false;
        }

        if (!valido) {
            // usar alertas si está disponible, si no Swal
            if (typeof alertas === 'function') {
                alertas('errorC', 'Corrige los campos resaltados', 'Datos incompletos');
            } else {
                Swal.fire({ title: 'Error', text: 'Corrige los campos resaltados', icon: 'error', color: 'white', background: '#000910' });
            }
        }

        return valido;
    }

    btnGuardarOrden.addEventListener('click', function () {
        if (!validarFormularioOrden()) return;
        const repuestosArray = Array.from(cuerpoRepuestos.querySelectorAll('.orden-repuesto-row')).map(row => ({
            nombre: row.querySelector('.repuesto-nombre').value,
            cantidad: Number(row.querySelector('.repuesto-cantidad').value || 0),
            precio: Number(row.querySelector('.repuesto-precio').value || 0)
        }));

        const datos = {
            cliente: document.getElementById('orden_cliente_tecnico').value.trim(),
            estado: document.getElementById('orden_estado_tecnico').value,
            equipo: document.getElementById('orden_equipo_tecnico').value.trim(),
            imei: document.getElementById('orden_imei_tecnico').value.trim(),
            horas_estimadas: document.getElementById('orden_horas_estimadas_tecnico').value || '00:00',
            observaciones: document.getElementById('orden_observaciones_tecnico').value.trim(),
            id_marca: Number(marcaSelect.value || 0),
            total_repuestos: Number((totalRepuestos.value || '$ 0.00').toString().replace('S/ ', '').replace('$ ', '')),
            total_orden: Number((totalOrden.value || '$ 0.00').toString().replace('S/ ', '').replace('$ ', '')),
            repuestos: JSON.stringify(repuestosArray)
        };

        // determinar si es registrar o modificar según el campo oculto
        const ordenIdField = document.getElementById('orden_id_tecnico');
        if (ordenIdField && ordenIdField.value && Number(ordenIdField.value) > 0) {
            datos.accion = 'modificar';
            datos.id = Number(ordenIdField.value);
        } else {
            datos.accion = 'registrar';
        }

        Swal.fire({
            title: 'Procesando!',
            html: 'Guardando información en el sistema...',
            timer: 1400,
            color: 'white',
            background: '#000910',
            timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: datos,
                    success: function (response) {
                        let res = response;
                        try { res = (typeof response === 'string') ? JSON.parse(response) : response; } catch (e) {}

                        if (res.success) {
                            // cerrar modal y remover backdrop sobrante si hay alguno
                            const modalElement = document.getElementById('modalOrdenTecnico');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                            modalInstance.hide();
                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                            document.body.classList.remove('modal-open');

                            $('#formularioOrdenTecnico')[0].reset();
                            document.getElementById('orden_estado_tecnico').value = '0';
                            document.getElementById('orden_horas_estimadas_tecnico').value = '00:00';
                            cuerpoRepuestos.innerHTML = '';
                            cuerpoRepuestos.appendChild(crearFilaRepuesto());
                            recalcularTotales();
                            Swal.fire({ title: 'Listo!', text: res.success, icon: 'success', color: 'white', background: '#000910', timer: 1400, showConfirmButton: false });
                            try { $('#ordenTabla').DataTable().ajax.reload(); } catch (e) { console.warn('DataTable no inicializado.'); }
                        } else if (res.error) {
                            Swal.fire({ title: 'Ups!', text: res.error, icon: 'error', color: 'white', background: '#000910' });
                        } else if (res.incompleto) {
                            Swal.fire({ title: 'Lo Siento!', text: res.incompleto, icon: 'error', color: 'white', background: '#000910' });
                        } else if (res.invalido) {
                            Swal.fire({ title: 'Dato inválido', text: res.invalido, icon: 'warning', color: 'white', background: '#000910' });
                        } else {
                            Swal.fire({ title: 'Error', text: 'Respuesta inesperada del servidor.', icon: 'error', color: 'white', background: '#000910' });
                        }
                    },
                    error: function () {
                        Swal.fire({ title: 'Error', text: 'Error en la comunicación con el servidor.', icon: 'error', color: 'white', background: '#000910' });
                    }
                });
            }
        });
    });

    recalcularTotales();
});
