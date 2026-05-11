<div class="modal fade" id="modalRegistroModulo" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel"
    aria-hidden="true">
    <div class="modal-dialog" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Registro de Modulos del Sistema</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroModulo">
                    <div class="form-group " style="width: 100%;">
                        <label for="modulo">Nombre del Modulo</label>
                        <textarea name="tipo" id="modulo" class="form-control"
                            placeholder="Ingresa el nombre del Modulo" required></textarea>
                            <small id="mensajeModulo" style="color: red; display: none;"></small>

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

<div class="modal fade" id="modalModificarModulo" tabindex="-1" role="dialog"
    aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Modificar Modulo del Sistema</h5>

            </div>
            <div class="modal-body">
                <form id="formModificarRol">
                    <input type="hidden" id="id_modulo">

                    <div class="form-group " style="width: 100%;">
                        <label for="rol">Nombre del Modulo</label>
                        <textarea name="tipo" id="modulo_modificar" class="form-control"
                            placeholder="Ingresa el nombre del Modulo" required></textarea>
                            <small id="mensajeModuloModificar" style="color: red; display: none;"></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modificar">Modificar</button>
            </div>
        </div>
    </div>
</div>