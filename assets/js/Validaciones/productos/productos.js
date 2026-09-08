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

    function ajustarCamposPorCategoria(select) {
        let isMod = ($(select).attr("id") === "categoriaModificar");
        let seccionTel = isMod ? "#seccion_telefono_modificar" : "#seccion_telefono";
        let stockAct = isMod ? "#stock_actualModificar" : "#stock_actual";
        let stockMin = isMod ? "#stock_minimoModificar" : "#stock_minimo";
        let stockMax = isMod ? "#stock_maximoModificar" : "#stock_maximo";

        if ($(select).val() == "26") {
            $(seccionTel).slideDown();
            $(stockAct).val(1).attr("readonly", true);
            $(stockMin).closest(".col-md-4, .col-md-3, div").hide();
            $(stockMax).closest(".col-md-4, .col-md-3, div").hide();
        } else {
            $(seccionTel).slideUp();
            $(stockAct).attr("readonly", false);
            $(stockMin).closest(".col-md-4, .col-md-3, div").show();
            $(stockMax).closest(".col-md-4, .col-md-3, div").show();
        }
    }

    $(document).on("change", "#id_categoria, #categoriaModificar", function() {
        ajustarCamposPorCategoria(this);
    });

    function cargarTablaProductos() {
        $("#tablaProductos").DataTable({
            destroy: true,
            ajax: { url: "?pagina=productos&ajax=true&x=productos", dataSrc: "" },
            columns: [
                { data: "id_producto", visible: false },
                {
                    data: "imagen_principal",
                    orderable: false,
                    searchable: false,
                    render: function (d, type, row) {
                        let img = (d && d !== "null" && d !== "")
                            ? "assets/img/productos/" + d
                            : "assets/img/productos/default.jpg";
                        return `<img src="${img}" 
                            onerror="this.src='assets/img/productos/default.jpg'" 
                            class="img-thumbnail-producto" 
                            data-id="${row.id_producto}" 
                            data-nombre="${row.nombre_producto}" 
                            data-imagen="${d || 'default.jpg'}" 
                            title="Click para gestionar imagen" 
                            style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:2px solid #0ef;cursor:pointer;">`;
                    }
                },
                { data: "nombre_producto" },
                { data: "nombre_marca" },
                { data: "nombre_categoria" },
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
                        
                        b += `<button type="button" class="btn btn-success btn-sm btn_gestionarImagen" data-id="${row.id_producto}" data-nombre="${row.nombre_producto}" data-imagen="${row.imagen_principal || 'default.jpg'}" title="Gestionar imagen"><i class="fa-solid fa-image"></i></button>`;
                        
                        b += `<button type="button" class="btn btn-info btn-sm btn_verDetalles" data-id="${row.id_producto}" data-nombre="${row.nombre_producto}" data-descripcion="${row.descripcion || ''}" data-categoria="${row.nombre_categoria || ''}" data-marca="${row.nombre_marca || ''}" data-precio="${row.precio_detal}" data-sact="${row.stock_actual}" data-smin="${row.stock_minimo}" data-smax="${row.stock_maximo}" data-estado="${row.estado}" data-imagen="${row.imagen_principal || ''}"><i class="fa-solid fa-eye"></i></button>`;

                        if (row.imei) {
                            b += `<button type="button" class="btn btn-white btn-sm btn_verTelefono" data-imei="${row.imei}" data-ram="${row.memoria_ram}" data-alm="${row.almacenamiento}"><i class="bi bi-phone"></i></button>`;
                        }
                        
                        if (permisos.control_total || permisos.modificar) {
                            b += `<button type="button" class="btn btn-warning btn-sm btn_modificarProducto" data-id="${row.id_producto}" data-nombre="${row.nombre_producto}" data-descripcion="${row.descripcion || ''}" data-marca="${row.id_marca}" data-categoria="${row.id_categoria}" data-smin="${row.stock_minimo}" data-smax="${row.stock_maximo}" data-sact="${row.stock_actual}" data-precio="${row.precio_detal}" data-imei="${row.imei || ''}" data-ram="${row.memoria_ram || ''}" data-alm="${row.almacenamiento || ''}" data-imagen="${row.imagen_principal || ''}"><i class="fa-solid fa-pen-to-square"></i></button>`;
                        }
                        if (permisos.control_total || permisos.eliminar) {
                            b += `<button type="button" class="btn btn-danger btn-sm btn-eliminar-prod" data-id="${row.id_producto}"><i class="fa-solid fa-trash"></i></button>`;
                        }
                        return b + '</div>';
                    }
                }
            ],
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
        });
    }

    $(document).on("click", ".btn_verDetalles", function() {
        let btn = $(this);
        let nombre = btn.data("nombre") || "";
        let desc = btn.data("descripcion");
        let cat = btn.data("categoria") || "—";
        let marca = btn.data("marca") || "—";
        let precio = btn.data("precio");
        $("#det_nombre").text(nombre);
        $("#det_categoria").text(cat);
        $("#det_marca").text(marca);
        $("#det_precio").text("$" + parseFloat(precio).toFixed(2));
        $("#det_stock_actual").text(btn.data("sact"));
        $("#det_stock_min").text(btn.data("smin"));
        $("#det_stock_max").text(btn.data("smax"));
        let img = btn.data("imagen");
        let imgSrc = (img && img !== "null" && img !== "") ? "assets/img/productos/" + img : "assets/img/productos/default.jpg";
        $("#det_imagen").attr("src", imgSrc);
        $("#det_nombre_imagen").text(img && img !== "null" && img !== "" ? img : "default.jpg");
        let est = btn.data("estado");
        $("#det_estado").attr("class", "badge " + (est == 1 ? "bg-success" : "bg-danger")).text(est == 1 ? "Activo" : "Inactivo");
        $("#det_descripcion").text(desc && desc.trim() !== "" ? desc : "Sin descripción registrada.");
        $("#modalDetallesProducto").modal("show");
    });

    $(document).on("click", ".btn_verTelefono", function() {
        $("#det_imei").text($(this).data("imei"));
        $("#det_ram").text($(this).data("ram"));
        $("#det_alm").text($(this).data("alm"));
        $("#modalDetallesTelefono").modal("show");
    });

    // Clic en la foto del producto → abre el modal de gestionar imagen
    $(document).on("click", ".img-thumbnail-producto", function() {
        let btn = $(this);
        abrirGestionarImagen(btn.data("id"), btn.data("nombre"), btn.data("imagen"));
    });

    // Ícono de imagen → abre el modal de gestionar imagen (no el de modificar)
    $(document).on("click", ".btn_gestionarImagen", function() {
        let btn = $(this);
        abrirGestionarImagen(btn.data("id"), btn.data("nombre"), btn.data("imagen"));
    });

    function abrirGestionarImagen(id, nombre, imagen) {
        $("#img_producto_id").val(id);
        let img = (imagen && imagen !== "null" && imagen !== "") ? "assets/img/productos/" + imagen : "assets/img/productos/default.jpg";
        let src = img;
        $("#previewImgGestionar").html(`<img src="${src}" onerror="this.src='assets/img/productos/default.jpg'" style="max-height:150px; max-width:200px; border-radius:8px; object-fit:contain;">`);
        $("#img_nombre_archivo").text(nombre ? "Producto: " + nombre : "");
        $("#imagenGestionar").val("");
        $("#modalGestionarImagen").modal("show");
    }

    // Vista previa en el modal de gestionar imagen
    $(document).on("change", "#imagenGestionar", function() {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $("#previewImgGestionar").html(`<img src="${e.target.result}" style="max-height:150px; max-width:200px; border-radius:8px; object-fit:contain;">`);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Subir imagen
    $("#btnSubirImagenProducto").on("click", function() {
        let id = $("#img_producto_id").val();
        let archivo = document.getElementById("imagenGestionar").files[0];
        if (!archivo) {
            showSweetAlert("invalido", "Seleccione una imagen para subir.");
            return;
        }
        let fd = new FormData();
        fd.append("accion", "subirImagen");
        fd.append("id", id);
        fd.append("imagen", archivo);
        showProcessingAlert();
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: fd,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    Swal.close();
                    $("#modalGestionarImagen").modal("hide");
                    showSweetAlert("success").then(() => { $("#tablaProductos").DataTable().ajax.reload(); });
                } else {
                    Swal.close();
                    $(".modal-backdrop").remove();
                    $("body").removeClass("modal-open");
                    showSweetAlert("invalido", res.invalido || res.error || "Error");
                }
            },
            error: function() { Swal.close(); showSweetAlert("error"); }
        });
    });

    // Eliminar imagen
    $("#btnEliminarImagenProducto").on("click", function() {
        let id = $("#img_producto_id").val();
        let config = {
            title: "¿Eliminar imagen?",
            text: "La imagen se restablecerá a la de defecto.",
            icon: "warning",
            cb: function() {
                let fd = new FormData();
                fd.append("accion", "eliminarImagen");
                fd.append("id", id);
                showProcessingAlert();
                $.ajax({
                    type: "POST",
                    url: window.location.href,
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(res) {
                        if (res.success) {
                            Swal.close();
                            $("#modalGestionarImagen").modal("hide");
                            showSweetAlert("success").then(() => { $("#tablaProductos").DataTable().ajax.reload(); });
                        } else {
                            Swal.close();
                            showSweetAlert("invalido", res.invalido || res.error || "Error");
                        }
                    },
                    error: function() { Swal.close(); showSweetAlert("error"); }
                });
            }
        };
        commonSwalMixin.fire({ title: config.title, text: config.text, icon: config.icon, showCancelButton: true, confirmButtonText: "Sí, eliminar" }).then((result) => { if (result.isConfirmed) config.cb(); });
    });

    function previewImagen(input, target) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                $(target).html(`<img src="${e.target.result}" style="max-height:110px; border-radius:6px;">`);
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            $(target).html('<span class="text-muted">Sin imagen seleccionada</span>');
        }
    }

    $(document).on("change", "#imagen", function() {
        previewImagen(this, "#previewImagen");
    });

    $(document).on("change", "#imagenModificar", function() {
        previewImagen(this, "#previewImagenModificar");
    });

    $("#btnRegistrarProducto").on("click", function() {
        if ($("#nombre").val().trim() !== "") showSweetAlert("pregunta1");
        else showSweetAlert("errorC");
    });

    window.registerData = function() {
        let fd = new FormData();
        fd.append("accion", "registrarProducto");
        fd.append("nombre", $("#nombre").val());
        fd.append("descripcion", $("#descripcion").val());
        fd.append("id_marca", $("#id_marca").val());
        fd.append("id_categoria", $("#id_categoria").val());
        fd.append("stock_minimo", $("#stock_minimo").val());
        fd.append("stock_maximo", $("#stock_maximo").val());
        fd.append("stock_actual", $("#stock_actual").val());
        fd.append("precio", $("#precio").val());
        fd.append("imei", $("#imei").val() || "");
        fd.append("ram", $("#ram").val() || "");
        fd.append("almacenamiento", $("#almacenamiento").val() || "");

        let archivo = document.getElementById("imagen").files[0];
        if (archivo) fd.append("imagen", archivo);

        ejecutarAjaxFormData(fd, "#modalRegistroProducto");
    };

    $(document).on("click", ".btn_modificarProducto", function () {
        let btn = $(this);
        $("#producto_id").val(btn.data("id"));
        $("#nombreModificar").val(btn.data("nombre"));
        $("#descripcionModificar").val(btn.data("descripcion"));
        $("#marcaModificar").val(btn.data("marca"));
        $("#categoriaModificar").val(btn.data("categoria"));
        $("#stock_minimoModificar").val(btn.data("smin"));
        $("#stock_maximoModificar").val(btn.data("smax"));
        $("#stock_actualModificar").val(btn.data("sact"));
        $("#precioModificar").val(btn.data("precio"));
        $("#imeiModificar").val(btn.data("imei"));
        $("#ramModificar").val(btn.data("ram"));
        $("#almacenamientoModificar").val(btn.data("alm"));

        let imgActual = btn.data("imagen") || "";
        $("#imagen_actualModificar").val(imgActual);
        if (imgActual && imgActual !== "null") {
            $("#previewImagenModificar").html(`<img src="assets/img/productos/${imgActual}" onerror="this.src='assets/img/productos/default.jpg'" style="max-height:110px; border-radius:6px;">`);
        } else {
            $("#previewImagenModificar").html('<span class="text-muted">Sin imagen</span>');
        }
        $("#imagenModificar").val("");

        ajustarCamposPorCategoria(document.getElementById("categoriaModificar"));
        $("#modalModificarProducto").modal("show");
    });

    $("#btnModificarProducto").on("click", function() {
        if ($("#nombreModificar").val().trim() !== "") showSweetAlert("pregunta2");
        else showSweetAlert("errorC");
    });

    window.modifyData = function() {
        let fd = new FormData();
        fd.append("accion", "modificarProducto");
        fd.append("id", $("#producto_id").val());
        fd.append("nombre", $("#nombreModificar").val());
        fd.append("descripcion", $("#descripcionModificar").val());
        fd.append("id_marca", $("#marcaModificar").val());
        fd.append("id_categoria", $("#categoriaModificar").val());
        fd.append("stock_minimo", $("#stock_minimoModificar").val());
        fd.append("stock_maximo", $("#stock_maximoModificar").val());
        fd.append("stock_actual", $("#stock_actualModificar").val());
        fd.append("precio", $("#precioModificar").val());
        fd.append("imei", $("#imeiModificar").val() || "");
        fd.append("ram", $("#ramModificar").val() || "");
        fd.append("almacenamiento", $("#almacenamientoModificar").val() || "");
        fd.append("imagen_actual", $("#imagen_actualModificar").val() || "");

        let archivo = document.getElementById("imagenModificar").files[0];
        if (archivo) fd.append("imagen", archivo);

        ejecutarAjaxFormData(fd, "#modalModificarProducto");
    };

    $(document).on("click", ".btn-eliminar-prod", function() {
        $("#btn_deleteProducto").val($(this).data("id"));
        showSweetAlert("pregunta3");
    });

    window.deleteData = function() {
        const d = { id: $("#btn_deleteProducto").val(), accion: "eliminarProducto" };
        ejecutarAjax(d, null);
    };

    // =============================================
    // AJAX HELPERS
    // =============================================

    function ejecutarAjaxFormData(fd, modalId) {
        showProcessingAlert();
        $.ajax({
            type: "POST",
            url: window.location.href,
            data: fd,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    if (modalId) $(modalId).modal("hide");
                    $(".modal-backdrop").remove();
                    $("body").removeClass("modal-open");
                    showSweetAlert("success").then(() => { $("#tablaProductos").DataTable().ajax.reload(); });
                } else {
                    showSweetAlert("invalido", res.invalido || res.error || "Error");
                }
            },
            error: function() { showSweetAlert("error"); }
        });
    }

    function ejecutarAjax(datos, modalId) {
        showProcessingAlert();
        $.ajax({
            type: "POST", url: window.location.href, data: datos, dataType: "json",
            success: function(res) {
                if (res.success) {
                    if (modalId) $(modalId).modal("hide");
                    $(".modal-backdrop").remove();
                    $("body").removeClass("modal-open");
                    showSweetAlert("success").then(() => { $("#tablaProductos").DataTable().ajax.reload(); });
                } else {
                    showSweetAlert("invalido", res.invalido || res.error || "Error");
                }
            },
            error: function() { showSweetAlert("error"); }
        });
    }

    const commonSwalMixin = Swal.mixin({ color: "white", background: "#000910", confirmButtonColor: "rgb(238, 191, 0)" });

    function showProcessingAlert() {
        Swal.fire({ title: "Procesando!", timerProgressBar: true, didOpen: () => { Swal.showLoading(); }, background: "#000910", color: "white", allowOutsideClick: false });
    }

    function showSweetAlert(action, message = "") {
        const config = {
            pregunta1: { title: "¿Registrar?", text: "¿Desea guardar este producto?", icon: "question", cb: registerData },
            pregunta2: { title: "¿Modificar?", text: "¿Desea guardar los cambios?", icon: "question", cb: modifyData },
            pregunta3: { title: "¿Eliminar?", text: "¿Desea eliminar este producto?", icon: "warning", cb: deleteData }
        };
        if (config[action]) {
            return commonSwalMixin.fire({ title: config[action].title, text: config[action].text, icon: config[action].icon, showCancelButton: true, confirmButtonText: "Sí, confirmar" }).then((result) => { if (result.isConfirmed) config[action].cb(); });
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
