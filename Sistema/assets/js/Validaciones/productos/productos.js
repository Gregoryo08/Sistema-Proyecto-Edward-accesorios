$(document).ready(function () {
    let permisos = { registrar: false, modificar: false, eliminar: false, control_total: false };

    $.get("?pagina=productos&permisos=true", function (data) {
        permisos = JSON.parse(data);
        if (permisos.control_total || permisos.registrar) {
            $(".text-start button").show();
        } else {
            $(".text-start button").hide();
        }
        cargarCategorias();
        cargarMarcas();
        cargarTablaProductos();
    });

    function cargarCategorias() {
        $.get("?pagina=productos&ajax=true&x=categorias", (r) => {
            let cats = JSON.parse(r);
            let opt = '<option value="">Seleccione...</option>';
            cats.forEach(c => opt += `<option value="${c.id_categoria}">${c.nombre_categoria}</option>`);
            $("#id_categoria, #categoriaModificar").html(opt);
        });
    }

    function cargarMarcas() {
        $.get("?pagina=productos&ajax=true&x=marcas", (r) => {
            let mar = JSON.parse(r);
            let opt = '<option value="">Seleccione...</option>';
            mar.forEach(m => opt += `<option value="${m.id_marca}">${m.nombre_marca}</option>`);
            $("#id_marca, #marcaModificar").html(opt);
        });
    }

    $(document).on("change", "#id_categoria, #categoriaModificar", function() {
        let seccion = ($(this).attr("id") === "id_categoria") ? "#seccion_telefono" : "#seccion_telefono_modificar";
        if ($(this).val() == "26") $(seccion).slideDown(); else $(seccion).slideUp();
    });

    function cargarTablaProductos() {
        $("#tablaProductos").DataTable({
            destroy: true,
            ajax: { url: "?pagina=productos&ajax=true&x=productos", dataSrc: "" },
            columns: [
                { data: "id_producto", visible: false },
                { data: "nombre_producto" },
                { data: "nombre_marca" },
                { data: "nombre_categoria" },
                { data: "stock_minimo" },
                { data: "stock_maximo" },
                { data: "stock_actual" },
                { data: "precio_detal", render: (d) => parseFloat(d).toFixed(2) },
                {
                    data: "estado",
                    render: (d) => d == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        let b = '<div class="btn-group">';
                        if (row.imei) b += `<button type="button" class="btn btn-info btn_verDetalles" data-imei="${row.imei}" data-ram="${row.memoria_ram}" data-alm="${row.almacenamiento}"><i class="bi bi-eye"></i></button>`;
                        
                        if (permisos.control_total || permisos.modificar) {
                            b += `<button type="button" class="btn btn-warning btn_modificarProducto" 
                                    data-id="${row.id_producto}" 
                                    data-nombre="${row.nombre_producto}" 
                                    data-marca="${row.id_marca}" 
                                    data-categoria="${row.id_categoria}" 
                                    data-smin="${row.stock_minimo}" 
                                    data-smax="${row.stock_maximo}" 
                                    data-sact="${row.stock_actual}" 
                                    data-precio="${row.precio_detal}" 
                                    data-imei="${row.imei || ''}" 
                                    data-ram="${row.memoria_ram || ''}" 
                                    data-alm="${row.almacenamiento || ''}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>`;
                        }
                        
                        if (permisos.control_total || permisos.eliminar) {
                            b += `<button type="button" class="btn btn-danger btn-eliminar-prod" data-id="${row.id_producto}"><i class="fa-solid fa-trash"></i></button>`;
                        }
                        return b + '</div>';
                    }
                }
            ],
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
        });
    }

    $(document).on("click", ".btn_verDetalles", function() {
        $("#det_imei").text($(this).data("imei"));
        $("#det_ram").text($(this).data("ram"));
        $("#det_alm").text($(this).data("alm"));
        $("#modalDetallesTelefono").modal("show");
    });

    $("#btnRegistrarProducto").on("click", function() {
        if ($("#nombre").val().trim() !== "") showSweetAlert("pregunta1");
        else showSweetAlert("errorC");
    });

    window.registerData = function() {
        const d = {
            nombre: $("#nombre").val(), id_marca: $("#id_marca").val(), id_categoria: $("#id_categoria").val(),
            stock_minimo: $("#stock_minimo").val(), stock_maximo: $("#stock_maximo").val(),
            stock_actual: $("#stock_actual").val(), precio: $("#precio").val(),
            imei: $("#imei").val(), ram: $("#ram").val(), almacenamiento: $("#almacenamiento").val(),
            accion: "registrarProducto"
        };
        ejecutarAjax(d, "#modalRegistroProducto");
    };

    $(document).on("click", ".btn_modificarProducto", function () {
        let btn = $(this);
        $("#producto_id").val(btn.data("id"));
        $("#nombreModificar").val(btn.data("nombre"));
        $("#marcaModificar").val(btn.data("marca"));
        $("#categoriaModificar").val(btn.data("categoria")).trigger("change");
        $("#stock_minimoModificar").val(btn.data("smin"));
        $("#stock_maximoModificar").val(btn.data("smax"));
        $("#stock_actualModificar").val(btn.data("sact"));
        $("#precioModificar").val(btn.data("precio"));
        $("#imeiModificar").val(btn.data("imei"));
        $("#ramModificar").val(btn.data("ram"));
        $("#almacenamientoModificar").val(btn.data("alm"));
        
        $("#modalModificarProducto").modal("show");
    });

    $("#btnModificarProducto").on("click", function() {
        if ($("#nombreModificar").val().trim() !== "") showSweetAlert("pregunta2");
        else showSweetAlert("errorC");
    });

    window.modifyData = function() {
        const d = {
            id: $("#producto_id").val(), nombre: $("#nombreModificar").val(), id_marca: $("#marcaModificar").val(),
            id_categoria: $("#categoriaModificar").val(), stock_minimo: $("#stock_minimoModificar").val(),
            stock_maximo: $("#stock_maximoModificar").val(), stock_actual: $("#stock_actualModificar").val(),
            precio: $("#precioModificar").val(), imei: $("#imeiModificar").val(),
            ram: $("#ramModificar").val(), almacenamiento: $("#almacenamientoModificar").val(),
            accion: "modificarProducto"
        };
        ejecutarAjax(d, "#modalModificarProducto");
    };

    $(document).on("click", ".btn-eliminar-prod", function() {
        $("#btn_deleteProducto").val($(this).data("id"));
        showSweetAlert("pregunta3");
    });

    window.deleteData = function() {
        const d = { id: $("#btn_deleteProducto").val(), accion: "eliminarProducto" };
        ejecutarAjax(d, null);
    };

    function ejecutarAjax(datos, modalId) {
        showProcessingAlert();
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: datos,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    if (modalId) $(modalId).modal("hide");
                    $(".modal-backdrop").remove();
                    $("body").removeClass("modal-open");
                    showSweetAlert("success").then(() => {
                        $("#tablaProductos").DataTable().ajax.reload();
                    });
                } else {
                    showSweetAlert("invalido", res.invalido || res.error || "Error");
                }
            },
            error: function() { showSweetAlert("error"); }
        });
    }

    const commonSwalMixin = Swal.mixin({
        color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)"
    });

    function showProcessingAlert() {
        Swal.fire({
            title: "Procesando!", timerProgressBar: true,
            didOpen: () => { Swal.showLoading(); },
            background: "#000910", color: "white", allowOutsideClick: false
        });
    }

    function showSweetAlert(action, message = "") {
        const config = {
            pregunta1: { title: "¿Registrar?", text: "¿Desea guardar este producto?", icon: "question", cb: registerData },
            pregunta2: { title: "¿Modificar?", text: "¿Desea guardar los cambios?", icon: "question", cb: modifyData },
            pregunta3: { title: "¿Eliminar?", text: "¿Desea eliminar este producto?", icon: "warning", cb: deleteData }
        };

        if (config[action]) {
            return commonSwalMixin.fire({
                title: config[action].title, text: config[action].text, icon: config[action].icon,
                showCancelButton: true, confirmButtonText: "Sí, confirmar"
            }).then((result) => { if (result.isConfirmed) config[action].cb(); });
        }

        const simple = {
            success: { title: "¡Listo!", icon: "success", timer: 1500, showConfirmButton: false },
            errorC: { title: "Campos incompletos", icon: "error" },
            error: { title: "Error de servidor", icon: "error" },
            invalido: { title: "Atención", text: message, icon: "warning" }
        };
        return commonSwalMixin.fire(simple[action]);
    }
});