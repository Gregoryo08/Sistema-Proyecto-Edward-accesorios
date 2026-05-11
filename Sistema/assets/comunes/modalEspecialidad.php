<div class="modal fade" id="modalRegistroEspecialidad" tabindex="-1" aria-labelledby="modalRegistroEspecialidadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroEspecialidadLabel">Registrar Nueva Especialidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRegistroEspecialidad">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="especialidad" class="form-label">Nombre de la Especialidad</label>
                        <input type="text" class="form-control" id="especialidad" name="especialidad" required maxlength="30">
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

<div class="modal fade" id="modalModificarEspecialidad" tabindex="-1" aria-labelledby="modalModificarEspecialidadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarEspecialidadLabel">Modificar Especialidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formModificarEspecialidad">
                <div class="modal-body">
                    <input type="hidden" id="id_especialidad" name="id_especialidad">
                    <div class="mb-3">
                        <label for="especialidad_modificar" class="form-label">Nombre de la Especialidad</label>
                        <input type="text" class="form-control" id="especialidad_modificar" name="especialidad_modificar" required maxlength="30">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="modificar">Modificar</button>
                </div>
            </form>
        </div>
    </div>
</div>