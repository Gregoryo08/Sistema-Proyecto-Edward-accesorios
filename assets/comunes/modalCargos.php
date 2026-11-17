<div class="modal fade" id="modalRegistroCargo" tabindex="-1" role="dialog" aria-labelledby="modalRegistroCargoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroCargoLabel">Registro de Cargos del Personal</h5>
            </div>
            <div class="modal-body">
                <form id="formRegistroCargo">
                    <div class="form-group" style="width: 100%;">
                        <label for="cargo">Nombre del Cargo</label>
                        <textarea name="tipo" id="cargo" class="form-control" placeholder="Ingresa el nombre del cargo" required></textarea>
                        <div id="error_cargo" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="registro">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarCargo" tabindex="-1" role="dialog" aria-labelledby="modalModificarCargoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarCargoLabel">Modificar Cargos del Personal</h5>
            </div>
            <div class="modal-body">
                <form id="formModificarCargo">
                    <input type="hidden" id="id_cargo">
                    <input type="hidden" id="nombre_cargo">

                    <div class="form-group mb-3" style="width: 100%;">
                        <label for="cargo_modificar">Nombre del Cargo</label>
                        <textarea name="tipo" id="cargo_modificar" class="form-control" placeholder="Ingresa el nombre del Cargo" required></textarea>
                        <div id="error_cargo_modificar" class="invalid-feedback"></div>
                    </div>

                    <div class="form-group" style="width: 100%;">
                        <label for="precio_modificar">Precio por alquiler de personal (opcional)</label>
                        <input type="text" class="form-control" id="precio_modificar" placeholder="Ingresa el precio de alquiler por cargo (Opcional)">
                        <div id="error_precio_modificar" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel_modificar">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modificar">Modificar</button>
            </div>
        </div>
    </div>
</div>