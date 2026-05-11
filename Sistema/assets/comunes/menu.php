<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Edward Accesorios</title>
    <link rel="icon" href="./assets/img/icono.ico">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="assets/Library/DataTables/datatables.min.css">
    <link rel="stylesheet" href="assets/Library/SweetAlerts/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/Library/Select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="assets/Library/Toastr/toastr.min.css">
    <script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
    <script src="assets/Library/Select2/dist/js/select2.min.js"></script>
    <script src="assets/Library/DataTables/datatables.min.js"></script>
    <script src="assets/Library/Toastr/toastr.min.js"></script>
    <link rel="stylesheet" href="assets/css/main.css">
  
    <link rel="stylesheet" href="assets/css/preloader.css">
    <link rel="stylesheet" href="assets/css/menu.css">




   
    <link rel="stylesheet" href="assets/css/perfil.css">
>
   
</head>

<body class="index-page">
   

    <header id="header" class="header d-flex" style="background-color: #000910;">
        <div class="container_fluid position-relative d-flex align-items-center" >
            <a href="?pagina=pagina" class="logo d-flex align-items-center me-auto" >
                <h1 class="sitename" style="color: white;">Edward Accesorios</h1>
            </a>
            <div class="btn_acciones" style="margin-right: 50px;">
                <div style="position: relative; display: inline-block; ">
                    <i class="bi bi-bell-fill" id="btn_noti" style="font-size: 24px; cursor: pointer; color: white;"></i>
                    <span id="contador" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; display: none;">0</span>
                    <div class="container_notificacion" id="container_notificacion" style="display: none; position: absolute; right: 0; top: 40px; background: white; border: 1px solid #ccc; width: 280px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; color: #333;">
                        <div class="header_cuadro" style="padding: 10px; border-bottom: 1px solid #eee; background: #f8f9fa;">
                            <p class="title" style="margin:0; font-weight: bold;">Notificaciones</p>
                        </div>
                        <div id="dropdown_noti" style="max-height: 300px; overflow-y: auto; padding: 10px;"></div>
                    </div>
                </div>
                <button id="boton-ayuda" class="cta-btn">Ayuda</button>
                <a class="cta-btn" href="?pagina=salida" style="background: #d9534f;">Cerrar Sesión</a>
            </div>
            
        </div>
    </header>

    <div id="panel-ayuda" class="ayuda-lateral oculto">
        <div class="header-ayuda">
            <h4>Centro de Ayuda</h4>
            <div id="cerrar-ayuda" class="fas fa-times"></div>
        </div>
        <div id="vista-principal">
            <input type="text" id="busqueda-ayuda" placeholder="Buscar en la ayuda..." />
            <ul id="temas-ayuda">
                <li data-tema="reservaciones">¿Cómo hacer una reservación?</li>
                <li data-tema="habitaciones">¿Cómo consultar habitaciones?</li>
                <li data-tema="bitacora">¿Qué es la Bitácora?</li>
                <li data-tema="reportes">¿Qué son los Reportes?</li>
                <li data-tema="productos">¿Cómo Administrar Productos?</li>
            </ul>
        </div>
        <div id="vista-detalle" style="display:none;">
            <div class="fas fa-arrow-left" id="volver-atras"></div>
            <h4 id="titulo-detalle"></h4>
            <p id="contenido-detalle"></p>
        </div>
    </div>

    
    <nav id="navmenu" class="navmenu">
        <ul class="lista_opciones">
            <li class="opcion-item">
                <a href="?pagina=principal" class="opcion">
                    <i class="bi bi-house-fill"></i>
                    <span class="textoOption">Inicio</span>
                </a>
            </li>

  
      <?php
use App\Sistema\models\Usuarios;
if (!isset($obj_usuario)) {
    $obj_usuario = new Usuarios();
}
?>      



   <?php if (
    
    $obj_usuario->tienePermiso("Administrar Perfil", "control_total") 
): ?>
    <li class="dropdown">
        <a href="#" class="opcion">
            <i class="bi bi-person-circle"></i>
            <span class="textoOption">Mi Perfil</span>
        </a>
        <ul class="listOptionSlice">
            <?php if ($obj_usuario->tienePermiso("Administrar Perfil", "listar")): ?>
                <li><a href="?pagina=miperfil"><i class="bi bi-arrow-return-right"></i> Mis Datos</a></li>
            <?php endif; ?>

            <?php if ($obj_usuario->tienePermiso("Administrar Perfil", "modificar")): ?>
                <li><a href="?pagina=contraseña"><i class="bi bi-arrow-return-right"></i> Contraseña</a></li>
            <?php endif; ?>
            
            <?php if ($obj_usuario->tienePermiso("Administrar Perfil", "consultar")): ?>
                <li><a href="?pagina=notificacion"><i class="bi bi-arrow-return-right"></i> Notificaciones</a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>


   <?php if (
    $obj_usuario->tienePermiso("Administrar Empleados", "listar") || 
    $obj_usuario->tienePermiso("Administrar Usuarios", "listar") || 
    $obj_usuario->tienePermiso("Administrar Turnos", "listar")
): ?>
    <li class="dropdown">
        <a href="#" class="opcion">
            <i class="bi bi-people-fill"></i>
            <span class="textoOption">Personal</span>
        </a>
        <ul class="listOptionSlice">
            <?php if ($obj_usuario->tienePermiso("Administrar Empleados", "listar")): ?>
                <li><a href="?pagina=empleado"><i class="bi bi-arrow-return-right"></i> Empleados</a></li>
            <?php endif; ?>
            
            <?php if ($obj_usuario->tienePermiso("Administrar Usuarios", "listar")): ?>
                <li><a href="?pagina=usuarios"><i class="bi bi-arrow-return-right"></i> Usuarios</a></li>
            <?php endif; ?>
            
            <?php if ($obj_usuario->tienePermiso("Administrar Turnos", "listar")): ?>
                <li><a href="?pagina=turno"><i class="bi bi-arrow-return-right"></i> Turnos</a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>



    <?php if ($obj_usuario->tienePermiso("Administrar Clientes", "listar")): ?>
    <li class="opcion-item">
        <a href="?pagina=clientes" class="opcion">
            <i class="bi bi-person-standing"></i>
            <span class="textoOption">Clientes</span>
        </a>
    </li>
<?php endif; ?>


   <?php if (
    $obj_usuario->tienePermiso("Administrar financiamiento", "listar") || 
    $obj_usuario->tienePermiso("Administrar equipos", "listar") || 
    $obj_usuario->tienePermiso("Administrar Telefono", "listar")
): ?>
    <li class="dropdown">
    <a href="#" class="opcion">
        <i class="fa-solid fa-hand-holding-dollar"></i>
        <span class="textoOption">Financiamiento</span>
    </a>
    <ul class="listOptionSlice">
        <?php if ($obj_usuario->tienePermiso("Administrar Financiamiento", "listar")): ?>
            <li><a href="?pagina=financiamiento"><i class="bi bi-arrow-return-right"></i> Financiamiento</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Planes", "listar")): ?>
            <li><a href="?pagina=equipos"><i class="bi bi-arrow-return-right"></i> Planes De Financiamiento</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Telefono", "listar")): ?>
            <li><a href="?pagina=telefono"><i class="bi bi-arrow-return-right"></i> Teléfonos</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>


   <?php if (
    $obj_usuario->tienePermiso("Administrar menu", "listar") || 
    $obj_usuario->tienePermiso("Administrar despacho", "listar") 
): ?>
    <li class="dropdown">
    <a href="#" class="opcion">
        <i class="fa-solid fa-handshake"></i>
        <span class="textoOption">Servicio Técnico</span>
    </a>
    <ul class="listOptionSlice">
        <?php if ($obj_usuario->tienePermiso("Administrar Técnicos", "listar")): ?>
            <li><a href="?pagina=menu"><i class="bi bi-arrow-return-right"></i> Tecnicos</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Reparaciones", "listar")): ?>
            <li><a href="?pagina=despacho"><i class="bi bi-arrow-return-right"></i> Reparacion</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>


       <?php if (
    $obj_usuario->tienePermiso("Administrar producto", "listar") || 
    $obj_usuario->tienePermiso("Administrar entrada_almacen", "listar") 
): ?>       
    <li class="dropdown">
    <a href="#" class="opcion">
        <i class="fa-solid fa-store"></i>
        <span class="textoOption">Ventas</span>
    </a>
    <ul class="listOptionSlice">
        <?php if ($obj_usuario->tienePermiso("Administrar Ventas", "listar")): ?>
            <li><a href="?pagina=producto"><i class="bi bi-arrow-return-right"></i> Ventas De Pisos</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Ventas Online", "listar")): ?>
            <li><a href="?pagina=entradas_almacen"><i class="bi bi-arrow-return-right"></i> Ventas Online</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>

       <?php if (
    $obj_usuario->tienePermiso("Administrar areas", "listar") || 
    $obj_usuario->tienePermiso("Administrar servicio_adicional", "listar") || 
    $obj_usuario->tienePermiso("Administrar reservaciones_areas", "listar") 
): ?> 
   <li class="dropdown">
    <a href="#" class="opcion">
        <i class="fa-solid fa-people-roof"></i>
        <span class="textoOption">Inventario</span>
    </a>
    <ul class="listOptionSlice">
        <?php if ($obj_usuario->tienePermiso("Administrar Productos", "listar")): ?>
            <li><a href="?pagina=areas"><i class="bi bi-arrow-return-right"></i> Articulos/productos</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Entrada de Artículos", "listar")): ?>
            <li><a href="?pagina=servicio_adicional"><i class="bi bi-arrow-return-right"></i> Entrada de Articulo </a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Administrar Proveedores", "listar")): ?>
            <li><a href="?pagina=reservaciones_areas"><i class="bi bi-arrow-return-right"></i> Proveedores</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>

       <?php if (
    $obj_usuario->tienePermiso("Administrar metodo", "listar") || 
     $obj_usuario->tienePermiso("Administrar bancos", "listar") || 
     $obj_usuario->tienePermiso("Administrar especialidad", "listar") || 
      $obj_usuario->tienePermiso("Administrar Marcas", "listar") || 
    $obj_usuario->tienePermiso("Administrar Categoria", "listar") || 
    $obj_usuario->tienePermiso("Administrar cargos", "listar") 
): ?>
    <li class="dropdown">
    <a href="#" class="opcion">
        <i class="bi bi-archive-fill"></i>
        <span class="textoOption">Catálogo</span>
    </a>
    <ul class="listOptionSlice">
        
        <?php if ($obj_usuario->tienePermiso("Administrar Métodos de Pago", "listar")): ?>
            <li><a href="?pagina=metodo"><i class="bi bi-arrow-return-right"></i> Métodos Pago</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Administrar Bancos", "listar")): ?>
            <li><a href="?pagina=bancos"><i class="bi bi-arrow-return-right"></i> Bancos</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Administrar Especialidad", "listar")): ?>
            <li><a href="?pagina=especialidad"><i class="bi bi-arrow-return-right"></i> Especialidad</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Administrar Marcas", "listar")): ?>
            <li><a href="?pagina=marcas"><i class="bi bi-arrow-return-right"></i> Marcas De teléfono</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Administrar Categoria", "listar")): ?>
            <li><a href="?pagina=categoria"><i class="bi bi-arrow-return-right"></i> Categoria</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Administrar Cargos", "listar")): ?>
            <li><a href="?pagina=cargos"><i class="bi bi-arrow-return-right"></i> Cargos</a></li>
        <?php endif; ?>
        
    </ul>
</li>
<?php endif; ?>


       <?php if (
    $obj_usuario->tienePermiso("Administrar BaseDatos", "listar") || 
     $obj_usuario->tienePermiso("Administrar bitacora", "listar") || 
     $obj_usuario->tienePermiso("Administrar especialidad", "listar") || 
      $obj_usuario->tienePermiso("Administrar roles", "listar") || 
    $obj_usuario->tienePermiso("Administrar modulo", "listar") 
): ?>
    <li class="dropdown">
    <a href="#" class="opcion">
        <i class="bi bi-tools"></i>
        <span class="textoOption">Mantenimiento</span>
    </a>
    <ul class="listOptionSlice">
        
        <?php if ($obj_usuario->tienePermiso("Administrar Base de Datos", "listar")): ?>
            <li><a href="?pagina=baseDatos"><i class="bi bi-arrow-return-right"></i> Respaldo DB</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Bitácora", "listar")): ?>
            <li><a href="?pagina=bitacora"><i class="bi bi-arrow-return-right"></i> Bitácora</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Roles", "listar")): ?>
            <li><a href="?pagina=roles"><i class="bi bi-arrow-return-right"></i> Roles</a></li>
        <?php endif; ?>
        
        <?php if ($obj_usuario->tienePermiso("Administrar Modulos", "listar")): ?>
            <li><a href="?pagina=modulo"><i class="bi bi-arrow-return-right"></i> Administrar Modulos</a></li>
        <?php endif; ?>
        
    </ul>
</li>
<?php endif; ?>



       <?php if (
    $obj_usuario->tienePermiso("Administrar reporteAlquiler", "listar") || 
     $obj_usuario->tienePermiso("Administrar reporteReservaArea", "listar") 
): ?>
    <li class="dropdown">
    <a href="#" class="opcion">
        <i class="fa-solid fa-chart-pie"></i>
        <span class="textoOption">Reportes</span>
    </a>
    <ul class="listOptionSlice">
        <?php if ($obj_usuario->tienePermiso("Reporte de Ventas", "listar")): ?>
            <li><a href="?pagina=reporteAlquiler"><i class="bi bi-arrow-return-right"></i> Ventas</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Reporte de Servicio Técnico", "listar")): ?>
            <li><a href="?pagina=reporteReservaArea"><i class="bi bi-arrow-return-right"></i> Servicio Tecnico</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Reporte de Inventario", "listar")): ?>
            <li><a href="?pagina=reporteAlquiler"><i class="bi bi-arrow-return-right"></i> Inventario</a></li>
        <?php endif; ?>

        <?php if ($obj_usuario->tienePermiso("Reporte de Financiamiento", "listar")): ?>
            <li><a href="?pagina=reporteAlquiler"><i class="bi bi-arrow-return-right"></i> Financiamiento</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>

        </ul>
    </nav>
  

    <script>
        $(document).ready(function() {
            function quitarLoader() {
                $('#preloader-sistema').fadeOut(500, function() {
                    $(this).remove();
                });
            }
            $(window).on('load', quitarLoader);
            setTimeout(quitarLoader, 3000);

            $('.dropdown > .opcion').on('click', function(e) {
                e.preventDefault();
                let $parent = $(this).parent();
                if ($parent.hasClass('open')) {
                    $parent.removeClass('open');
                } else {
                    $('.dropdown').removeClass('open');
                    $parent.addClass('open');
                }
            });

            $('.navmenu').on('mouseleave', function() {
                $('.dropdown').removeClass('open');
            });

            $('#btn_noti').on('click', function(e) {
                e.stopPropagation();
                $('#container_notificacion').fadeToggle(200);
            });

            $(document).on('click', function() {
                $('#container_notificacion').fadeOut(200);
            });

            $('#container_notificacion').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>
</html>