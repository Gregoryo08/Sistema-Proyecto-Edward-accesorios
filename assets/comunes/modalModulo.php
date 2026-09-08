<div class="modal fade" id="modalRegistroModulo" tabindex="-1" aria-labelledby="modalRegistroModuloLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroModuloLabel">Registro de Módulos del Sistema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRegistroModulo">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modulo" class="form-label">Nombre del Módulo</label>
                        <input type="text" class="form-control" id="modulo" name="modulo" required maxlength="30" placeholder="Ingresa el nombre del Módulo">
                        <div id="error_modulo" class="text-danger small" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="registro">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarModulo" tabindex="-1" aria-labelledby="modalModificarModuloLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarModuloLabel">Modificar Módulo del Sistema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formModificarModulo">
                <div class="modal-body">
                    <input type="hidden" id="id_modulo" name="id_modulo">
                    <div class="mb-3">
                        <label for="modulo_modificar" class="form-label">Nombre del Módulo</label>
                        <input type="text" class="form-control" id="modulo_modificar" name="modulo_modificar" required maxlength="30" placeholder="Ingresa el nombre del Módulo">
                        <div id="error_modulo_modificar" class="text-danger small" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="modificar">Modificar</button>
                </div>
            </form>
        </div>
    </div>
</div>