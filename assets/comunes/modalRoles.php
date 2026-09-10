<div class="modal fade" id="modalRegistroRol" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width: 90%;" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Registro de Roles de Usuario</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroRol">
                    <div class="form-group " style="width: 100%;">
                        <label for="rol">Nombre del Rol</label>
                        <textarea name="tipo" id="rol" class="form-control" placeholder="Ingresa el nombre del Rol"
                            required></textarea>

                        <div class="mensaje">
                            <p id="texto_mensaje_rol" class="text-danger small mt-1 mb-0" style="display:none; margin: 0;"></p>
                        </div>
                    </div>
                    <div class="form-group" style="display: flex; flex-direction: column; width: 100%;">
                        <label for="nombreModificar">Seleccionar Permisos del Rol</label>

                        <div class=" form-group" style="display: flex; flex-direction: column; width: 100%;">
                            <ul class="seleccion_permisos" id="seleccion_permisos">
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="registro" style="display:none;">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerRol" tabindex="-1" role="dialog" aria-labelledby="modalVerRolLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width: 90%;" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerRolLabel">Ver Permisos del Rol '<span id="nombre_rol"></span>'</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroRol">
                    <div class="form-group mostrarPermisos" style="width: 100%;" id="mostrarPermisos">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="btn_cancelar_ver">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarRol" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width: 90%;" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Modificar Rol y permisos de Usuario</h5>

            </div>
            <div class="modal-body">
                <form id="formModificarRol">
                    <input type="hidden" id="id_rol">

                    <div class="form-group modificarPermisos" style="width: 100%;" id="modificarPermisos">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="btn_cancel_modificar">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modificar">Modificar</button>
            </div>
        </div>
    </div>
</div>

<style>
    #rol,
    #rol:focus,
    #rol:active,
    #rol.is-invalid,
    #rol.is-invalid:focus,
    #rol.is-invalid:active {
        border: 1px solid #cfd4da !important;
        border-radius: 8px;
        box-shadow: none !important;
        outline: none !important;
        background: #ffffff !important;
        background-image: none !important;
        color: #111827 !important;
        font-size: 16px;
        padding: 12px 14px;
    }

    body.dark-mode #rol,
    body.dark-mode #rol:focus,
    body.dark-mode #rol:active,
    body.dark-mode #rol.is-invalid,
    body.dark-mode #rol.is-invalid:focus,
    body.dark-mode #rol.is-invalid:active {
        background: rgba(15, 23, 42, 0.45) !important;
        border: 1px solid rgba(148, 163, 184, 0.6) !important;
        color: #f8fafc !important;
    }

    #rol::placeholder {
        color: rgba(55, 65, 81, 0.6);
    }

    body.dark-mode #rol::placeholder {
        color: rgba(226, 232, 240, 0.7);
    }

    #texto_mensaje_rol {
        display: none;
        color: #ff4d4d;
        font-size: 14px;
        font-weight: 500;
        margin-top: 8px;
        padding: 0;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        clip-path: none !important;
    }
</style>




