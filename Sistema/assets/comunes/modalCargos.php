<div class="modal fade" id="modalRegistroCargo" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog"" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Registro de Cargos del personal</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroCargo">
                    <div class="form-group " style="width: 100%;">
                        <label for="rol">Nombre del Cargo</label>
                        <textarea name="tipo" id="cargo" class="form-control" placeholder="Ingresa el nombre del cargo" required></textarea>

                        <div class="mensaje">
                            <p id="texto_mensaje_nombre"></p>
                        </div>
                    </div>

                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="registro" style="display:none;">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarCargo" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog"" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Modificar Cargos del Personal</h5>

            </div>
            <div class="modal-body">
                <form id="formModificarCargo">
                    <input type="hidden" id="id_cargo">
                    <input type="hidden" id="nombre_cargo">

                    <div class="form-group " style="width: 100%;">
                        <label for="rol">Nombre del Cargo</label>
                        <textarea name="tipo" id="cargo_modificar" class="form-control" placeholder="Ingresa el nombre del Cargo" required></textarea>

                        <div class="mensaje">
                            <p id="texto_mensaje_nombre_modificar"></p>
                        </div>
                    </div>

                    <div class="form-group " style="width: 100%;">
                        <label for="precio">Precio por alquiler de personal (opcional)</label>
                        <input type="text" class="form-control" id="precio_modificar" placeholder="Ingresa el precio de alquiler por cargo (Opcional)">

                        <div class="mensaje">
                            <p id="texto_mensaje_precio_modificar"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modificar">Modificar</button>
            </div>
        </div>
    </div>
</div>