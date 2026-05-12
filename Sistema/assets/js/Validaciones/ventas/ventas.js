$(document).ready(function () {
    let carrito = [];
    let permisos = { registrar: false, consultar: false, modificar: false, eliminar: false, control_total: false };

    $.get("?pagina=ventas&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.control_total || permisos.registrar) {
            $(".text-end button").show();
        } else {
            $(".text-end button").hide();
        }
        cargarTablaVentas();
    });

    function cargarTablaVentas() {
        $("#tablaVentas").DataTable({
            destroy: true,
            ajax: {
                url: "?pagina=ventas&ajax=true&accion=listarVentasRealizadas",
                dataSrc: "",
            },
            columns: [
                { data: "id_venta", visible: false },
                { 
                    data: "fecha", 
                    render: function(data) { return new Date(data).toLocaleString(); } 
                },
                { data: "cliente" },
                { data: "metodo_pago" },
                { data: "referencia_pago" },
                { 
                    data: "total", 
                    render: function(data) { return `<b>$${parseFloat(data).toFixed(2)}</b>`; } 
                },
                {
                    data: null,
                    render: function (data) {
                        return `<button class="btn btn-info btn-sm btn-ver-detalle" data-id="${data.id_venta}">
                                    <i class="bi bi-eye"></i>
                                </button>`;
                    }
                }
            ],
            order: [[0, "desc"]],
            pageLength: 8,
            columnDefs: [{ className: "dt-head-center", targets: "_all" }],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron ventas",
                paginate: { next: "Sig", previous: "Ant" }
            }
        });
    }

    $('#select_producto_venta').select2({
        dropdownParent: $('#modalRegistroVenta'),
        placeholder: 'Buscar equipo o accesorio...',
        theme: 'bootstrap-5',
        ajax: {
            url: '?pagina=ventas&ajax=true&accion=buscarProductos',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(p => ({
                    id: p.id,
                    text: p.text + " ($" + p.precio + ")",
                    precio: p.precio,
                    nombre: p.text
                }))
            })
        }
    });

    $.get("?pagina=ventas&ajax=true&accion=listarMetodos", function (data) {
        let metodos = JSON.parse(data);
        $("#metodo_pago_tienda").empty();
        metodos.forEach(m => {
            $("#metodo_pago_tienda").append(`<option value="${m.nombre_metodopago}">${m.nombre_metodopago}</option>`);
        });
    });

    $('#select_producto_venta').on('select2:select', function (e) {
        let p = e.params.data;
        let existe = carrito.find(item => item.id === p.id);
        if (existe) {
            existe.cant++;
        } else {
            carrito.push({ id: p.id, nom: p.nombre, pre: p.precio, cant: 1 });
        }
        renderizarCarrito();
        $(this).val(null).trigger('change');
    });

    function renderizarCarrito() {
        let html = "";
        let total = 0;
        carrito.forEach((p, i) => {
            let sub = p.pre * p.cant;
            total += sub;
            html += `<tr>
                <td>${p.nom}</td>
                <td><input type="number" class="form-control form-control-sm text-center" value="${p.cant}" min="1" onchange="cambiarCantidad(${i}, this.value)" style="width: 70px;"></td>
                <td>$${sub.toFixed(2)}</td>
                <td><button class="btn btn-sm text-danger" onclick="eliminarDelCarrito(${i})"><i class="bi bi-trash"></i></button></td>
            </tr>`;
        });
        $("#cuerpoVentaTienda").html(html);
        $("#total_mostrador").text("$" + total.toFixed(2));
    }

    window.cambiarCantidad = (i, v) => {
        if (v > 0) {
            carrito[i].cant = parseInt(v);
            renderizarCarrito();
        }
    };

    window.eliminarDelCarrito = (i) => {
        carrito.splice(i, 1);
        renderizarCarrito();
    };

    function limpiarEstadoModal() {
        $('.modal').each(function() {
            const instance = bootstrap.Modal.getInstance(this);
            if(instance) instance.hide();
        });
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open").css({ "overflow": "", "padding-right": "" });
        $("input").css("border", "1px solid #ced4da").css("box-shadow", "none");
        carrito = [];
        $("#cuerpoVentaTienda").empty();
        $("#total_mostrador").text("$0.00");
        $("#referencia_tienda").val("");
    }

    $('.modal').on('hidden.bs.modal', function () {
        limpiarEstadoModal();
    });

    $("#btn_finalizar_tienda").click(function () {
        if (carrito.length === 0) {
            mensaje("errorCustom", "Debe agregar al menos un producto.");
            return;
        }
        mensaje("pregunta", "¿Está seguro de procesar esta venta?", registrarVenta);
    });


$(document).on("click", ".btn-ver-detalle", function () {
    let id = $(this).data("id");
    
    $("#cuerpoDetalleVenta").html('<tr><td colspan="4" class="text-center p-4"><div class="spinner-border text-info"></div></td></tr>');
    const myModal = new bootstrap.Modal(document.getElementById('modalDetalleVenta'));
    myModal.show();

    $.ajax({
        type: "POST",
        url: "?pagina=ventas",
        data: { accion: 'verDetalleVenta', id: id },
        success: function (response) {
            let productos = JSON.parse(response);
            let html = "";
            
            productos.forEach(p => {
                let precio = parseFloat(p.precio_unitario);
                let cant = parseInt(p.cantidad);
                let sub = precio * cant;

                html += `
                    <tr>
                        <td class="text-start">${p.nombre_producto}</td>
                        <td>${cant}</td>
                        <td>$${precio.toFixed(2)}</td>
                        <td>$${sub.toFixed(2)}</td>
                    </tr>`;
            });

            $("#cuerpoDetalleVenta").html(html);
        }
    });
});

function registrarVenta() {
    let datos = {
        accion: 'registrarVenta',
        total: carrito.reduce((acc, p) => acc + (p.pre * p.cant), 0),
        metodo: $("#metodo_pago_tienda").val(),
        referencia: $("#referencia_tienda").val(),
        productos: carrito
    };

    Swal.fire({
        title: "Procesando!",
        timer: 800,
        color: "white",
        background: "#000910",
        didOpen: () => { Swal.showLoading(); },
    }).then(() => {
        $.ajax({
            type: "POST",
            url: "?pagina=ventas",
            data: datos,
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    limpiarEstadoModal();
                    mensaje("success");
                    $("#tablaVentas").DataTable().ajax.reload();
                } else if (res.error) {
                   
                    mensaje("errorCustom", res.error); 
                } else {
                    mensaje("error");
                }
            }
        });
    });
}

    function mensaje(accion, texto, funcion) {
        let config = { color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" };

        if (accion == "errorCustom") {
            Swal.fire({ ...config, title: "Atención", text: texto, icon: "warning" });
        } else if (accion == "error") {
            Swal.fire({ ...config, title: "Ups!", text: "Error en el Servidor!", icon: "error" });
        } else if (accion == "pregunta") {
            Swal.fire({ ...config, title: "Confirmar", text: texto, icon: "question", showCancelButton: true, confirmButtonText: "Aceptar" }).then((r) => { if (r.isConfirmed) funcion(); });
        } else {
            Swal.fire({ ...config, title: "Listo!", text: "Éxito!", icon: "success", showConfirmButton: false, timer: 1500 });
        }
    }
});