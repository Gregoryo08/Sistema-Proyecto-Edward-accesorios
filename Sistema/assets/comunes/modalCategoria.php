<div class="modal fade" id="modalRegistroCategoria" tabindex="-1" aria-labelledby="modalRegistroCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroCategoriaLabel">Registrar Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRegistroCategoria">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="categoria" name="categoria" required maxlength="30">
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

<div class="modal fade" id="modalModificarCategoria" tabindex="-1" aria-labelledby="modalModificarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarCategoriaLabel">Modificar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formModificarCategoria">
                <div class="modal-body">
                    <input type="hidden" id="id_categoria" name="id_categoria">
                    <div class="mb-3">
                        <label for="categoria_modificar" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="categoria_modificar" name="categoria_modificar" required maxlength="30">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="modificar">Modificar</button>
                </div>
            </form>
        </div>
    </div>
</div>