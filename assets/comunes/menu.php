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
    <!-- <link rel="stylesheet" href="assets/Library/Toastr/toastr.min.css"> -->
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
    <script src="assets/Library/Select2/dist/js/select2.min.js"></script>
    <script src="assets/Library/DataTables/datatables.min.js"></script>
    <!-- <script src="assets/Library/Toastr/toastr.min.js"></script> -->
    <link rel="stylesheet" href="assets/css/preloader.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
</head>

<body class="index-page">

   <header id="header" class="header d-flex">
    <div class="container_fluid position-relative d-flex align-items-center w-100">
        <a href="?pagina=principal" class="logo d-flex align-items-center me-auto">
            <!-- <img src="/src/assets/img/icono.ico" alt="Edward Accesorios" style="width: 40px; height: 40px; margin-right: 10px;"> -->
            <h1 class="sitename">Edward Accesorios</h1>
        </a>
        
        <div class="btn_acciones d-flex align-items-center">
            <div class="notificacion-wrapper">
    <i class="bi bi-bell-fill" id="btn_noti"></i>
    <span id="contador">0</span>
   <div class="container_notificacion" id="container_notificacion" style="display: none; width: 320px; background: #fff; box-shadow: 0px 4px 15px rgba(0,0,0,0.2); border-radius: 10px; position: absolute; right: 0; z-index: 1000; overflow: hidden;">
    
    
    <div class="header_cuadro" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background-color: #0b2545; color: #fff;">
        <p class="title" style="margin: 0; font-weight: bold; font-size: 14px;">NOTIFICACIONES</p>
        <i class="bi bi-x-lg" id="cerrar_noti" style="cursor: pointer; font-size: 16px;"></i>
    </div>

    
    <div id="dropdown_noti" style="max-height: 280px; overflow-y: auto;"></div>

    
    <div style="padding: 10px; background: #f8f9fa; border-top: 1px solid #eee; text-align: center;">
        <button id="btn_limpiar_todas" style="width: 100%; background: #007bff; color: #fff; border: none; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;">
            <i class="bi bi-check2-all" style="font-size: 16px;"></i> Marcar todas como leídas
        </button>
    </div>

</div>
</div>

            <button id="theme-toggle" class="btn btn-sm">
                <i class="bi bi-moon-fill" id="theme-icon"></i>
            </button>

            <button class="btn-sm btn-tasa bg-white border rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;">
                <i class="bi bi-currency-dollar text-success" id="tasa-dolar" style="font-size: 1.25rem;"></i>
            </button>
            <div id="tasa-tooltip" class="tasa-tooltip p-2" style="display: none;">
                <div class="d-flex flex-column align-items-center text-center gap-1">
                    <span class="text-success fw-bold" style="font-size: 0.75rem;">TASA BCV</span>
                    <span id="tasa-valor" class="fw-bold fs-6 text-dark">0.00 Bs.</span>
                    <span class="text-muted" style="font-size: 0.75rem;">
                        Última Actualización:<br>
                        <span id="tasa-fecha" class="fw-bold text-dark">--/--/----</span>
                    </span>
                </div>
            </div>
            <div class="dropdown">
    <button class="btn btn-sm dropdown-toggle btn-usuario d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-5"></i>
        <span class="d-none d-sm-inline">Perfil</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><button class="dropdown-item" id="boton-ayuda">Ayuda</button></li>
        <li><a class="dropdown-item text-danger" href="?pagina=salida">Cerrar Sesión</a></li>
    </ul>
</div>
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
                <li data-tema="Venta">¿Cómo hacer una Venta?</li>
                <li data-tema="Financiamiento">¿Cómo consultar un financiamiento?</li>
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
                $obj_usuario->tienePermiso("Administrar Perfil", "control_total") ||
                $obj_usuario->tienePermiso("Administrar Notificacion", "listar")
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

                        <?php if ($obj_usuario->tienePermiso("Administrar Notificacion", "listar")): ?>
                            <li><a href="?pagina=notificacion"><i class="bi bi-arrow-return-right"></i> Notificaciones</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (
                $obj_usuario->tienePermiso("Administrar Usuarios", "listar") ||
                $obj_usuario->tienePermiso("Administrar Roles", "listar") ||
                $obj_usuario->tienePermiso("Administrar bitacora", "listar") ||
                 $obj_usuario->tienePermiso("Administrar Modulos", "listar")
            ): ?>
                <li class="dropdown">
                    <a href="#" class="opcion">
                        <i class="bi bi-shield-lock-fill"></i>
                        <span class="textoOption">Seguridad</span>
                    </a>
                    <ul class="listOptionSlice">
                        <?php if ($obj_usuario->tienePermiso("Administrar Usuarios", "listar")): ?>
                            <li><a href="?pagina=usuarios"><i class="bi bi-arrow-return-right"></i> Usuarios</a></li>
                        <?php endif; ?>

                        <?php if ($obj_usuario->tienePermiso("Administrar roles", "listar")): ?>
                            <li><a href="?pagina=roles"><i class="bi bi-arrow-return-right"></i> Roles</a></li>
                        <?php endif; ?>

                        <?php if ($obj_usuario->tienePermiso("Administrar bitacora", "listar")): ?>
                            <li><a href="?pagina=bitacora"><i class="bi bi-arrow-return-right"></i> Bitácora</a></li>
                        <?php endif; ?>

                        <?php if ($obj_usuario->tienePermiso("Administrar Modulos", "listar")): ?>
                            <li><a href="?pagina=modulo"><i class="bi bi-arrow-return-right"></i> Administrar Modulos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (
                $obj_usuario->tienePermiso("Administrar Empleados", "listar")||
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
                $obj_usuario->tienePermiso("Administrar Pago De Cuotas", "listar")
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
                         <?php if ($obj_usuario->tienePermiso("Administrar Pago De Cuotas", "listar")): ?>
                            <li><a href="?pagina=cliente_financiamiento"><i class="bi bi-arrow-return-right"></i> Pago De Cuotas</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (
                $obj_usuario->tienePermiso("Administrar Servicio Tecnico", "listar")
            ): ?>
                <li class="dropdown">
                    <a href="#" class="opcion">
                        <i class="fa-solid fa-handshake"></i>
                        <span class="textoOption">Servicio Técnico</span>
                    </a>
                    <ul class="listOptionSlice">
                        <?php if ($obj_usuario->tienePermiso("Administrar Servicio Tecnico", "listar")): ?>
                            <li><a href="?pagina=servicio_tecnico"><i class="bi bi-arrow-return-right"></i>Servicio Tecnico</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (
                $obj_usuario->tienePermiso("Administrar Ventas", "listar") ||
                $obj_usuario->tienePermiso("Administrar Chequeo", "listar")
            ): ?>
                <li class="dropdown">
                    <a href="#" class="opcion">
                        <i class="fa-solid fa-store"></i>
                        <span class="textoOption">Ventas</span>
                    </a>
                    <ul class="listOptionSlice">
                        <?php if ($obj_usuario->tienePermiso("Administrar Ventas", "listar")): ?>
                            <li><a href="?pagina=ventas"><i class="bi bi-arrow-return-right"></i> Ventas</a></li>
                        <?php endif; ?>

                        <?php if ($obj_usuario->tienePermiso("Administrar Chequeo", "listar")): ?>
                            <li><a href="?pagina=chequeo"><i class="bi bi-arrow-return-right"></i> Ventas Online</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (
                $obj_usuario->tienePermiso("Administrar Productos", "listar") ||
                $obj_usuario->tienePermiso("Administrar Proveedores", "listar") ||
                $obj_usuario->tienePermiso("Administrar Entradas", "listar")
            ): ?>
                <li class="dropdown">
                    <a href="#" class="opcion">
                        <i class="fa-solid fa-people-roof"></i>
                        <span class="textoOption">Inventario</span>
                    </a>
                    <ul class="listOptionSlice">
                        <?php if ($obj_usuario->tienePermiso("Administrar Productos", "listar")): ?>
                            <li><a href="?pagina=productos"><i class="bi bi-arrow-return-right"></i> Productos</a></li>
                        <?php endif; ?>

                        <?php if ($obj_usuario->tienePermiso("Administrar Proveedores", "listar")): ?>
                            <li><a href="?pagina=proveedores"><i class="bi bi-arrow-return-right"></i> Proveedores</a></li>
                        <?php endif; ?>
                        <?php if ($obj_usuario->tienePermiso("Administrar Entradas", "listar")): ?>
                            <li><a href="?pagina=entradas_productos"><i class="bi bi-arrow-return-right"></i> Entrada De Productos</a></li>
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
                        <span class="textoOption">Repertorio</span>
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
                            <li><a href="?pagina=marcas"><i class="bi bi-arrow-return-right"></i> Marcas</a></li>
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
                $obj_usuario->tienePermiso("Administrar BaseDatos", "listar") 
            ): ?>
                <li class="dropdown">
                    <a href="#" class="opcion">
                        <i class="bi bi-tools"></i>
                        <span class="textoOption">Mantenimiento</span>
                    </a>
                    <ul class="listOptionSlice">
                        <?php if ($obj_usuario->tienePermiso("Administrar Base De Datos", "listar")): ?>
                            <li><a href="?pagina=baseDatos_1"><i class="bi bi-arrow-return-right"></i> Backup Base De Datos</a></li>
                        <?php endif; ?>
                            <?php if ($obj_usuario->tienePermiso("Administrar Base De Datos", "listar")): ?>
                            <li><a href="?pagina=baseDatos_2"><i class="bi bi-arrow-return-right"></i> Restaurar Base De Datos</a></li>
                        <?php endif; ?>

                        
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (
                $obj_usuario->tienePermiso("Administrar Reportes", "listar") ||
                $obj_usuario->tienePermiso("Administrar Reportes", "listar")
            ): ?>
                <li class="dropdown">
                    <a href="#" class="opcion">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span class="textoOption">Reportes</span>
                    </a>
                    <ul class="listOptionSlice"> 
                        <?php if ($obj_usuario->tienePermiso("Administrar Reportes", "listar")): ?>
                            <li><a href="?pagina=reportefinanciamiento"><i class="bi bi-arrow-return-right"></i> Financiamiento</a></li>
                        <?php endif; ?>
                        <?php if ($obj_usuario->tienePermiso("Administrar Reportes", "listar")): ?>
                            <li><a href="?pagina=reporteservicio_tecnico"><i class="bi bi-arrow-return-right"></i>Servicio Tecnico</a></li>
                        <?php endif; ?>

                        <?php if ($obj_usuario->tienePermiso("Administrar Reportes", "listar")): ?>
                            <li><a href="?pagina=reporteVentas" class="submenu-link"><i class="bi bi-arrow-return-right"></i>Reporte Ventas</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <script src="assets/js/menu.js"></script>

    <!-- ============ ED-AI ASISTENTE (INTRANET) ============ -->
    <div class="edai-chat" id="edaiChat">
        <div id="edaiWindow" class="edai-window">
            <div class="edai-header">
                <img src="assets/avatar.jpg" alt="Ed-AI" class="edai-avatar">
                <div class="edai-header-info">
                    <strong>Ed-AI Asistente</strong>
                    <small id="edai-status">disponible</small>
                </div>
                <button id="edaiClose" type="button">&times;</button>
            </div>
            <div class="edai-body" id="edaiBody">
                <p class="edai-msg edai-bot">¡Hola! Soy <strong>Ed-AI</strong>, tu asistente del sistema. Pregúntame cómo usar los módulos (ventas, productos, financiamiento, clientes...) o sobre la tasa de cambio.</p>
            </div>
            <div class="edai-footer">
                <input type="text" id="edaiInput" placeholder="Escribe tu duda..." maxlength="300">
                <button id="edaiSend" type="button"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
        <button id="edaiFab" class="edai-fab" title="Ed-AI Asistente">
            <img src="assets/avatar.jpg" alt="Ed-AI" class="edai-fab-img">
            <span class="edai-dot"></span>
        </button>
        <div id="edaiDisponible" class="edai-aviso" style="display:none;"></div>
    </div>
    <script src="assets/js/edai.js"></script>

<style>
.btn-usuario {
  background: #ffffff !important;
  color: #1d1d1f !important;
  border: 1px solid #d2d2d7 !important;
  transition: all 0.3s ease !important;
}

body.dark-mode .btn-usuario {
  background: #1c1c1e !important;
  color: #f5f5f7 !important;
  border: 1px solid #3a3a3c !important;
}

/* ============ ED-AI CHAT ============ */
.edai-chat { position: fixed; right: 22px; bottom: 22px; z-index: 1060; }
.edai-fab {
  width: 58px; height: 58px; border-radius: 50%;
  background: #144272; color: #fff; border: none;
  box-shadow: 0 8px 20px rgba(20,66,114,.4);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; position: relative; transition: transform .2s ease;
}
.edai-fab:hover { transform: scale(1.08); }
.edai-fab-img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.edai-dot {
  position: absolute; top: 2px; right: 2px; width: 12px; height: 12px;
  background: #22c55e; border: 2px solid #fff; border-radius: 50%;
}
.edai-window {
  display: none;
  position: absolute; right: 0; bottom: 70px;
  width: 330px; max-width: 88vw; height: 440px;
  background: #fff; border-radius: 16px; overflow: hidden;
  box-shadow: 0 20px 45px rgba(0,0,0,.25);
  flex-direction: column;
}
.edai-window.open { display: flex; }
.edai-header {
  background: #144272; color: #fff; padding: 12px 14px;
  display: flex; align-items: center; gap: 10px;
}
.edai-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; }
.edai-header-info { flex: 1; line-height: 1.15; }
.edai-header-info small { display: block; font-size: 11px; opacity: .85; }
.edai-header button { background: transparent; border: none; color: #fff; font-size: 22px; cursor: pointer; }
.edai-body {
  flex: 1; overflow-y: auto; padding: 14px;
  display: flex; flex-direction: column; gap: 8px; background: #f8fafc;
}
.edai-msg {
  max-width: 82%; padding: 9px 12px; border-radius: 14px;
  font-size: 13px; line-height: 1.4; word-wrap: break-word;
}
.edai-bot { background: #eef2ff; color: #1e293b; align-self: flex-start; }
.edai-user { background: #144272; color: #fff; align-self: flex-end; }
.edai-footer { display: flex; gap: 6px; padding: 10px; background: #fff; }
.edai-footer input {
  flex: 1; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 14px; font-size: 13px;
}
.edai-footer button {
  background: #144272; color: #fff; border: none; border-radius: 50%;
  width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
}
.edai-aviso {
  position: absolute; right: 70px; bottom: 10px; background: #dc2626; color: #fff;
  padding: 8px 12px; border-radius: 10px; font-size: 12px; max-width: 240px; z-index: 1061;
}
body.dark-mode .edai-window { background: #1c1c1e; }
body.dark-mode .edai-body { background: #17171a; }
body.dark-mode .edai-msg.edai-bot { background: #26262b; color: #e5e7eb; }
body.dark-mode .edai-footer { background: #1c1c1e; }
body.dark-mode .edai-footer input { background: #26262b; border-color: #3a3a3c; color: #e5e7eb; }
</style>