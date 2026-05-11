<div class="modal fade" id="modalRegistroMarca" tabindex="-1" role="dialog" aria-labelledby="modalRegistroMarcaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroMarcaLabel">Registro De Una Marca De Teléfono</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistroMarca" style="display: flex; flex-direction: column;">
                    <div class="form-group mb-3">
                        <label for="nombre" class="form-label">Nombre De La Marca</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Ej: Samsung, Apple, Xiaomi" required>
                        <div class="mensaje mt-2">
                            <p id="texto_mensaje_nombre" class="text-danger small"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnRegistrarMarca">Registrar Marca</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarMarca" tabindex="-1" role="dialog" aria-labelledby="modalModificarMarcaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarMarcaLabel">Modificar Datos De La Marca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModificarMarca" style="display: flex; flex-direction: column;">
                    <input type="hidden" id="marca_id" name="id">
                    <div class="form-group mb-3">
                        <label for="nombreModificar" class="form-label">Nombre de la Marca</label>
                        <input type="text" class="form-control" id="nombreModificar" name="nombre" required>
                        <div class="mensaje mt-2">
                            <p id="texto_mensaje_nombre_modificar" class="text-danger small"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnModificarMarca" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>