/*let notificacionesAbiertas = false;

$(document).ready(function () {



 document.getElementById("boton-ayuda").addEventListener("click", () => {
  document.getElementById("panel-ayuda").classList.toggle("oculto");
});

document.getElementById("cerrar-ayuda").addEventListener("click", () => {
  document.getElementById("panel-ayuda").classList.add("oculto");


  document.getElementById("vista-detalle").style.display = "none";
  document.getElementById("vista-principal").style.display = "block";
  document.getElementById("busqueda-ayuda").value = ""; 
});

 
  const temas = {
    reservaciones: "Para hacer una reservación, dirígete al módulo 'Reservaciones', elige fecha, cliente y área.",
    habitaciones: "Consulta el estado de las habitaciones desde el módulo 'Habitaciones'.",
    notificaciones: "Las notificaciones te alertan de eventos importantes del sistema. Puedes dirigirte al Menu lateral > Mi Perfil > Notificaciones para ver todas las notificaciones que haz recibido. Estas las puedes marcar como leídas o eliminarlas de tu lista si asi lo deseas.",
    reportes: "Los reportes te permiten obtener informacion relevante que te permite tomar deciciones en base su informacion. Para generar un reporte debes dirigirte al Menu lateral > Reportes y escoger la opcion del tipo de reporte que desees, luego puedes crear resportes estadisticos o un reporte sencillo. Si deseas exportar como 'PDF' estos resportes haz click en el boton 'Generar Reporte'.",
    bitacora: "La Bitacora te permite monitorear todas las acciones de todos los usuarios del sistema. para acceder a ella debes de dirigirte al Menu lateral > Manteniminento > Consultar Bitacora, y alli podras consultar la bitacora del sistema.",
    productos: "Para Administrar los productos del almacen debes dirigirte al Menu Lateral > Almacen > Productos donde podras consultar todos los productos, registra nuevos, modificarlos, y eliminarlos si asi lo desea.",
    movimientos: "En el almacen puedes realizar movimientos de 'Entrada' y 'Entrega' de los productos, para esto dirigete a Menu Lateral > Almacen y escoge 'Entradas de Productos' o 'Entregas de Productos'.",
    entrega: "Para realizar una entrega de productos debes dirigirte a Menu Lateral > Almacen > Entregas de Productos, en donde podras consultar y modificar todas las entregas realizadas, para realizar un entrega haz click en el boton 'Entregar Productos'. Luego selecciona un Empleado y acontinuacion selecciona los productos que vayas a entregar al mismo y establece una cantidad de entrega que sea valida, por ultimo haz click en el boton 'Confirmar Entrega'.",
    entrada: "Para realizar una entrada de productos debes dirigirte a Menu Lateral > Almacen > Entradas de Productos, en donde podras consultar y modificar todas las entradas realizadas, para realizar un entrada haz click en el boton 'Reponer Productos'. Luego selecciona un Proveedor y acontinuacion selecciona los productos que vayan a ser parte de la entrada y establece una cantidad de entrega que corresponda a cada producto, por ultimo haz click en el boton 'Confirmar Entrada'.",
  };

 
  document.querySelectorAll("#temas-ayuda li").forEach((item) => {
  item.addEventListener("click", () => {
    const tema = item.getAttribute("data-tema");
    const titulo = item.textContent;

    document.getElementById("titulo-detalle").textContent = titulo;
    document.getElementById("contenido-detalle").textContent = temas[tema] || "Contenido no disponible.";

    document.getElementById("vista-principal").style.display = "none";
    document.getElementById("vista-detalle").style.display = "block";
  });
});


document.getElementById("volver-atras").addEventListener("click", () => {
  document.getElementById("vista-detalle").style.display = "none";
  document.getElementById("vista-principal").style.display = "block";
});


document.getElementById("busqueda-ayuda").addEventListener("input", function () {
  const filtro = this.value.toLowerCase();
  document.querySelectorAll("#temas-ayuda li").forEach((li) => {
    li.style.display = li.textContent.toLowerCase().includes(filtro) ? "" : "none";
  });
});












  $("#contenedor").fadeOut(2000);

  setInterval(() => {

  $.ajax({
    url: 'controllers/notificaciones.php',
    method: "GET",
    dataType: "json",
    data: { accion: 'obtenerEnviadas' },
    success: function (data) {
      if (data.length > 0) {
        data.forEach(noti => {
          
          toastr.options = {
            closeButton: true,
            positionClass: "toast-bottom-right",
            timeOut: 5000,
            showMethod: "slideDown",
            hideMethod: "fadeOut"
          };

          toastr.info(noti.mensaje);
        });

        // Marcar como enviadas
        $.ajax({
          url: 'controllers/notificaciones.php',
          method: "POST",
          data:{accion: "enviar"}
        });
      }
    }
  });


}, 10000); 



});

function cargarNotificaciones() {

  if (notificacionesAbiertas) return;

    $.ajax({
        url: 'controllers/notificaciones.php',
        method: 'GET',
        dataType: 'json',
         data: { accion: 'obtenerLeidas' },
        success: function(data) {
            const contador = $('#contador');
            const contenedor = $('#dropdown_noti');

            if (data.length > 0) {
                contador.text(data.length);
                contador.show();

                let html = '';
                data.forEach(noti => {
                    html += `<div style="color: #555; padding: 10px; border-bottom: 1px solid #ddd;">
                        ${noti.mensaje}
                    </div>`;
                });
                contenedor.html(html);
            } else {
                contador.hide();
                contenedor.html(`<div style="padding: 10px; color: #555;">Sin notificaciones</div>`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error cargando notificaciones:', error);
        }
    });

}

$('#btn_noti').on('click', function () {

    const container = $('#container_notificacion');
    const estado = $(this).attr('data-click');
    const contador = $('#contador');

    if (estado === 'no') {
        container.show();
        $(this).attr('data-click', 'si');
        contador.hide();
        notificacionesAbiertas = true;

       $.ajax({
            url: 'controllers/notificaciones.php',
            method: 'POST',
            data: { accion: 'leer' },
            success: function () {            
                contador.hide();
            }
        });

    } else {
        container.hide();
        $(this).attr('data-click', 'no');
        notificacionesAbiertas = false;
    }
});

$(document).on('click', function (e) {
    const notiBtn = $('#btn_noti')[0];
    const contenedor = $('#container_notificacion')[0];

    if (!notiBtn.contains(e.target) && !contenedor.contains(e.target)) {
        $('#container_notificacion').hide();
        $('#btn_noti').attr('data-click', 'no');
        notificacionesAbiertas = false;
    }
});

$(document).ready(function() {
    cargarNotificaciones(); // Se ejecuta solo una vez al iniciar

    setInterval(function (){
      if(!notificacionesAbiertas){ 
      cargarNotificaciones(); // primera ejecución inmediata
      }
    }, 10000); // Luego cada 10s si el menú está cerrado
});





(function () {
  "use strict";

  let scrollTop = document.querySelector(".scroll-top");

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100
        ? scrollTop.classList.add("active")
        : scrollTop.classList.remove("active");
    }
  }
  scrollTop.addEventListener("click", (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  window.addEventListener("load", toggleScrollTop);
  document.addEventListener("scroll", toggleScrollTop);

  function aosInit() {
    AOS.init({
      duration: 600,
      easing: "ease-in-out",
      once: true,
      mirror: false,
    });
  }
  window.addEventListener("load", aosInit);

  const header = document.getElementById("header");
  const menu = document.getElementById("navmenu");
  const contenido = document.getElementById("hero");
  const main = document.getElementById("main");
  const contenido_footer = document.getElementById("contenido_footer");
  const logo = document.querySelector(".img_menu_opciones");
  const titulo = document.querySelector(".sitename");

  if (
    header &&
    contenido &&
    menu &&
    main &&
    footer &&
    contenido_footer &&
    logo &&
    titulo
  ) {
    const alturaHeader = header.offsetHeight;
    const anchuraHeader = header.offsetWidth;
    const alturaMenu = menu.offsetHeight;
    const anchuraMenu = menu.offsetWidth;

    const espacio = document.createElement("div");
    espacio.classList.add("espacio-header");
    espacio.style.height = alturaHeader + "px";
    espacio.style.width = anchuraHeader - 30 + "px";

    const espacio_menu = document.createElement("div");
    espacio_menu.classList.add("espacio_menu");
    espacio_menu.style.height = alturaMenu - alturaHeader + "px";
    espacio_menu.style.width = anchuraMenu + "px";

    const espacio_menu2 = espacio_menu.cloneNode(true);

    logo.style.width = anchuraMenu - 10 + "px";
    logo.style.height = anchuraMenu - 10 + "px";

    const espacio_header = document.createElement("div");
    espacio_header.classList.add("espacio_header");
    espacio_header.style.width = anchuraMenu + "px";
    espacio_header.style.height = alturaHeader + "px";

    main.parentNode.insertBefore(espacio, main);
    contenido.parentNode.insertBefore(espacio_menu, contenido);
    contenido_footer.parentNode.insertBefore(espacio_menu2, contenido_footer);
    titulo.parentNode.insertBefore(espacio_header, titulo);
  }

  const opcion = document.getElementById("opcionTitulo");

  const alturaOpcion = opcion.offsetHeight;

  $(".opcionTitulo").css("height", alturaOpcion + "px");
  $(".opcionTitulo > .opcion").css("height", alturaOpcion + "px");

  $(".opcionTitulo").on("click", function () {
    var accion = $(this).data("accion");

    if (accion == "abrir") {
      $(this).css("height", "100%").data("accion", "cerrar");
      $(this).find(".listOptionSlice").css("opacity", "1");

      setTimeout(() => {
        $(this).find(".listDownMuch").css("display", "flex");
      }, 10);
      $(this).find(".listDownMuch").css("opacity", "1");
    }
    else{
      $(this).css("height", "100%").data("accion", "abrir");
      $(this).css("height", alturaOpcion + "px");
      $(this).find(".listOptionSlice").css("opacity", "0");
  
      $(this).find(".listDownMuch").css("visibility", "0");
      setTimeout(() => {
        $(this).find(".listDownMuch").css("display", "none");
      }, 400);
    }
  });

  $(".navmenu").mouseleave(function () { 
    $(".opcionTitulo").css("height", "100%").data("accion", "abrir");
    $(".opcionTitulo").css("height", alturaOpcion + "px");
    $(".opcionTitulo").find(".listOptionSlice").css("opacity", "0");

    $(".opcionTitulo").find(".listDownMuch").css("visibility", "0");
    setTimeout(() => {
      $(".opcionTitulo").find(".listDownMuch").css("display", "none");
    }, 400);
  });

  $(document).on("click", ".li_option", function (e) {
    e.preventDefault();
  });

  window.cargar = function () {
    
  }

  /*
  window.cargar = function () {
    $.ajax({
      url: "",
      type: "GET",
      data: {
        n: "true",
      },
      success: function (response) {
        var res = JSON.parse(response);
        const container_notificaciones = $("#container_register");

        $("#container_register").html("");
        if (typeof res.registro == "object") {
          var lista = "",
            i = 0,
            conteo = res.registro.length;

          if (res.registro.length > 0) {
            $.each(res.registro, function (index, value) {
              i++;
              lista = `<div class="li_container">
              <p class="">${
                res.cedula == "hotelcampana"
                  ? value.mensaje_usuario
                  : value.mensaje
              }</p>
              </div>
              ${i == conteo ? `` : `<hr>`}`;

              container_notificaciones.append(lista);
            });
          } else {
            lista = `<div class="li_container">
              <p class="">No hay notificaciones Recientes</p>
              </div>`;

            container_notificaciones.append(lista);
          }
        } else {
          container_notificaciones.innerHTML = `<div class="li_container">
          <p class="">No hay notificaciones Recientes</p>
          </div>`;
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error(
          "Error en la petición AJAX para notificaciones:",
          textStatus,
          errorThrown
        );
      },
    });
  };
  window.cargar();

  $("#btn_noti").click(function () {
    var click = $(this).data("click");

    if (click == "no") {
      $("#container_notificacion").css("display", "flex");

      setTimeout(() => {
        $("#container_notificacion").css("top", "100%");
        $("#container_notificacion").css("opacity", "1");
      }, 10);

      $(".vacio").css("display", "block");
      $(this).data("click", "si");
    } else {
      $(".vacio").css("display", "none");

      $("#container_notificacion").css("top", "0%");
      $("#container_notificacion").css("opacity", "0");

      setTimeout(() => {
        $("#container_notificacion").css("display", "none");
      }, 500);
      $(this).data("click", "no");
    }
  });

  $(".vacio").click(function () {
    $(".vacio").css("display", "none");

    $("#container_notificacion").css("top", "0%");
    $("#container_notificacion").css("opacity", "0");

    setTimeout(() => {
      $("#container_notificacion").css("display", "none");
    }, 500);
    $("#btn_noti").data("click", "no");
  });
*/

