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
                            <p id="texto_mensaje_rol"></p>
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