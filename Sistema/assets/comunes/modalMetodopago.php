<div class="modal fade" id="modalRegistroMetodopago" tabindex="-1" role="dialog" aria-labelledby="modalRegistroMetodopagoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroMetodopagoLabel">Registro de Método De Pago</h5>
            </div>
            <div class="modal-body">
                <form id="formRegistroMetodopago" novalidate>
                    <div class="form-group" style="width: 100%;">
                        <label for="nombre_metodopago">Método De Pago</label>
                        <div>
                            <input type="text" style="width: 100%;" class="form-control" id="nombre_metodopago" name="nombre_metodopago" placeholder="Ingresa el método de pago nuevo" required>

                            <div class="invalid-feedback" id="metodoFeedback">
                            </div>
                            <div class="valid-feedback">
                                ¡Perfecto!
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="width: 100%; display: flex; flex-direction: column; justify-content: center;">
                        <label for="nombre_metodopago" style="text-align: center;">Con que trabaja el metodo de pago?</label>
                        <div style="display: flex; justify-content: space-around;">
                            <div class="separador">
                                <input type="radio" class="radios" name="tipoCuenta" value="1" id="referencia">
                                <label for="referencia"> Referencia</label>
                            </div>

                            <div class="separador">
                                <input type="radio" class="radios" name="tipoCuenta" value="0" id="referencia">
                                <label for="fisico"> Fisico</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarMetodopago" disabled>Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarModificacion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarModificacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarModificacionLabel">Confirmar Modificación</h5>

            </div>
            <div class="modal-body confirmRegister">
                <p class="titleConfirm">¿Está Seguro?</p>
                <div class="contData">
                    <div class="data"><b>Metodo De Pago: </b><span id="editar_Nombremetodopago"></span></div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarCambios">Confirmar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificar" tabindex="-1" role="dialog" aria-labelledby="modalModificarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarLabel">Modificar Método De Pago</h5>

            </div>
            <div class="modal-body">
                <form id="formModificar">
                    <input type="hidden" id="MetodopagoId" name="id">

                    <input type="hidden" class="form-control" id="idModificar" required>
                    <div class="form-group" style="width: 100%;">
                        <label for="nombreModificar">Método De pago</label>
                        <input type="text" style="width: 100%;" class="form-control" id="nombreModificar" name="nombre_metodopago" required>
                        <div class="invalid-feedback" id="metodoModificarFeedback">
                        </div>
                        <div class="valid-feedback">
                            ¡Perfecto!
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" id="modificarDatos" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarEliminacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminacionLabel">Confirmar Eliminación</h5>
            </div>
            <div class="modal-body confirmRegister">
                <p class="titleConfirm">¿Estás seguro?</p>
                <input type="hidden" id="id_MetodoPago_delete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>