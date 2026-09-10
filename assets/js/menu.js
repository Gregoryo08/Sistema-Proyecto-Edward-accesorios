$(document).ready(function () {
    function quitarLoader() {
        $('#preloader-sistema').fadeOut(500, function () {
            $(this).remove();
        });
    }
    $(window).on('load', quitarLoader);
    setTimeout(quitarLoader, 3000);

    $('.dropdown > .opcion').on('click', function (e) {
        e.preventDefault();
        let $parent = $(this).parent();
        if ($parent.hasClass('open')) {
            $parent.removeClass('open');
        } else {
            $('.dropdown').removeClass('open');
            $parent.addClass('open');
        }
    });

    $('.navmenu').on('mouseleave', function () {
        $('.dropdown').removeClass('open');
    });

    $("#btn_noti").click(function (e) {
        e.stopPropagation();
        $("#container_notificacion").toggle();
    });

    $(document).on("click", "#cerrar_noti", function () {
        $("#container_notificacion").hide();
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('.notificacion-wrapper').length) {
            $("#container_notificacion").hide();
        }
    });

    function actualizarNotificaciones() {
        $.get("?pagina=notificacion&ajax=true&x=listar&historial=false", function (res) {
            const todasNotificaciones = res.data || [];
            const notificaciones = todasNotificaciones.filter(n => parseInt(n.leida) === 0 || n.leida === "0" || n.leida === false || !n.leida);
            const $dropdown = $("#dropdown_noti");
            const $contador = $("#contador");
            const $btnLimpiar = $("#btn_limpiar_todas");

            if (notificaciones.length > 0) {
                $contador.text(notificaciones.length).show();
                $dropdown.empty();
                $btnLimpiar.show();

                notificaciones.forEach(n => {
                    $dropdown.append(`
                        <div class="item-notificacion" style="padding: 10px 12px; border-bottom: 1px solid #eee; cursor: pointer; font-size: 13px; transition: background 0.2s;" data-id="${n.id_notificacion}">
                            <div style="display: flex; align-items: flex-start; gap: 8px;">
                                <i class="bi bi-exclamation-triangle-fill" style="color: #f0ad4e; margin-top: 1px;"></i> 
                                <div style="flex: 1; color: #333;">${n.mensaje}</div>
                            </div>
                        </div>
                    `);
                });
            } else {
                $contador.hide();
                $btnLimpiar.hide();
                $dropdown.html('<p style="text-align:center; color:#999; padding:15px; margin:0;">No hay notificaciones nuevas</p>');
            }
        }, 'json');
    }

    $(document).on("click", ".item-notificacion", function () {
        let $item = $(this);
        $item.css({ "background-color": "#d4edda", "color": "#155724" });
        let id = $item.data("id");
        setTimeout(function() {
            marcarLeidaDesdeDropdown(id);
        }, 250);
    });

    $(document).on("click", "#btn_limpiar_todas", function () {
        $.post("?pagina=notificacion&ajax=true&x=marcar_todas_leidas", function (res) {
            $("#dropdown_noti").html('<p style="text-align:center; color:#999; padding:15px; margin:0;">No hay notificaciones nuevas</p>');
            $("#contador").hide();
            $("#btn_limpiar_todas").hide();

            if ($.fn.DataTable.isDataTable("#tablaNotificaciones")) {
                $("#tablaNotificaciones").DataTable().ajax.reload(null, false);
            }
        }, 'json').fail(function() {
            $("#dropdown_noti").html('<p style="text-align:center; color:#999; padding:15px; margin:0;">No hay notificaciones nuevas</p>');
            $("#contador").hide();
            $("#btn_limpiar_todas").hide();
        });
    });

    function marcarLeidaDesdeDropdown(id) {
        $.post("?pagina=notificacion&ajax=true&x=marcar_leida", { id: id }, function (res) {
            if (res.success) {
                actualizarNotificaciones();
                if ($.fn.DataTable.isDataTable("#tablaNotificaciones")) {
                    $("#tablaNotificaciones").DataTable().ajax.reload(null, false);
                }
            }
        }, 'json');
    }

    $(document).on("notificacionMarcarLeida", function () {
        actualizarNotificaciones();
    });

    window.actualizarNotificaciones = actualizarNotificaciones;

    actualizarNotificaciones();
    setInterval(actualizarNotificaciones, 60000);

    if ($.fn.DataTable && $.fn.DataTable.ext) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                },
                aria: {
                    sortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sortDescending: ": Activar para ordenar la columna de manera descendente"
                }
            }
        });
    }

    const $btnTasa = $('.btn-tasa');
    const $tooltipTasa = $('#tasa-tooltip');

    $btnTasa.on('mouseenter', function () {
        $tooltipTasa.stop(true, true).fadeIn(150);
    });

    $btnTasa.on('mouseleave', function () {
        $tooltipTasa.stop(true, true).fadeOut(150);
    });

    function cargarTasaHeader() {
        $.ajax({
            url: '?pagina=ventas&accion=obtenerTasaCambio',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.tasa && data.tasa > 0) {
                    let valorTasa = parseFloat(data.tasa).toFixed(2);
                    $('#tasa-valor').text(`${valorTasa} Bs.`);
                    if (data.fecha) {
                        $('#tasa-fecha').text(data.fecha);
                    } else {
                        let hoy = new Date().toLocaleDateString('es-VE');
                        $('#tasa-fecha').text(hoy);
                    }
                } else {
                    $('#tasa-valor').text('N/A');
                }
            },
            error: function () {
                $('#tasa-valor').text('Error');
            }
        });
    }

    cargarTasaHeader();
});

const toggleBtn = document.getElementById('theme-toggle');
const icon = document.getElementById('theme-icon');

if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');

        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
        } else {
            localStorage.setItem('theme', 'light');
            icon.classList.replace('bi-sun-fill', 'bi-moon-fill');
        }
    });
}

const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark' && icon) {
    document.body.classList.add('dark-mode');
    icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
}