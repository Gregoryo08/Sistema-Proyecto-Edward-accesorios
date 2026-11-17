<div class="modal fade" id="modalRegistroMetodopago" tabindex="-1" role="dialog" aria-labelledby="modalRegistroMetodopagoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroMetodopagoLabel">Registro de Método De Pago</h5>
            </div>
            <div class="modal-body">
                <form id="formRegistroMetodopago" novalidate>
                    <div class="form-group mb-3" style="width: 100%;">
                        <label for="nombre_metodopago" class="form-label">Método De Pago</label>
                        <div>
                            <input type="text" style="width: 100%;" class="form-control" id="nombre_metodopago" name="nombre_metodopago" placeholder="Ingresa el método de pago nuevo" required>
                            <div class="invalid-feedback" id="metodoFeedback"></div>
                            <div class="valid-feedback">¡Perfecto!</div>
                        </div>
                    </div>

                    <div class="form-group mb-3" style="width: 100%; display: flex; flex-direction: column; justify-content: center;">
                        <label class="form-label text-center">¿Con qué trabaja el método de pago?</label>
                        <div style="display: flex; justify-content: space-around;">
                            <div class="separador">
                                <input type="radio" class="radios" name="tipoCuenta" value="1" id="referencia" checked>
                                <label for="referencia"> Referencia</label>
                            </div>
                            <div class="separador">
                                <input type="radio" class="radios" name="tipoCuenta" value="0" id="fisico">
                                <label for="fisico"> Físico</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3" style="width: 100%;">
                        <label for="moneda" class="form-label">Moneda</label>
                        <select class="form-select" id="moneda" name="moneda" required>
                            <option value="" selected disabled>Seleccione una moneda</option>
                            <option value="VES">VES (Bolívares)</option>
                            <option value="USD">USD (Dólares)</option>
                        </select>
                        <div class="invalid-feedback">Por favor, seleccione una moneda.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarMetodopago">Registrar</button>
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
                <form id="formModificar" novalidate>
                    <input type="hidden" id="MetodopagoId" name="id">
                    <input type="hidden" class="form-control" id="idModificar" required>
                    
                    <div class="form-group mb-3" style="width: 100%;">
                        <label for="nombreModificar" class="form-label">Método De Pago</label>
                        <input type="text" style="width: 100%;" class="form-control" id="nombreModificar" name="nombre_metodopago" required>
                        <div class="invalid-feedback" id="metodoModificarFeedback"></div>
                        <div class="valid-feedback">¡Perfecto!</div>
                    </div>

                    <div class="form-group mb-3" style="width: 100%; display: flex; flex-direction: column; justify-content: center;">
                        <label class="form-label text-center">¿Con qué trabaja el método de pago?</label>
                        <div style="display: flex; justify-content: space-around;">
                            <div class="separador">
                                <input type="radio" class="radios" name="tipoCuentaModificar" value="1" id="referenciaModificar">
                                <label for="referenciaModificar"> Referencia</label>
                            </div>
                            <div class="separador">
                                <input type="radio" class="radios" name="tipoCuentaModificar" value="0" id="fisicoModificar">
                                <label for="fisicoModificar"> Físico</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3" style="width: 100%;">
                        <label for="monedaModificar" class="form-label">Moneda</label>
                        <select class="form-select" id="monedaModificar" name="moneda" required>
                            <option value="" selected disabled>Seleccione una moneda</option>
                            <option value="VES">VES (Bolívares)</option>
                            <option value="USD">USD (Dólares)</option>
                        </select>
                        <div class="invalid-feedback">Por favor, seleccione una moneda.</div>
                    </div>

                    <div class="form-group mb-3" style="width: 100%;">
                        <label for="estadoModificar" class="form-label">Estado</label>
                        <select class="form-select" id="estadoModificar" name="estado" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
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

<div class="modal fade" id="modalConfirmarModificacion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarModificacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarModificacionLabel">Confirmar Modificación</h5>
            </div>
            <div class="modal-body confirmRegister">
                <p class="titleConfirm" style="font-size: 1.2rem; font-weight: bold; text-align: center;">¿Está Seguro de aplicar estos cambios?</p>
                <div class="contData p-3 bg-light rounded">
                    <div class="data mb-2"><b>Método De Pago: </b><span id="editar_Nombremetodopago"></span></div>
                    <div class="data mb-2"><b>Tipo de Flujo: </b><span id="editar_Tipometodopago"></span></div>
                    <div class="data mb-2"><b>Moneda: </b><span id="editar_Monedametodopago"></span></div>
                    <div class="data mb-2"><b>Estado: </b><span id="editar_Estadometodopago"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarCambios">Confirmar Cambios</button>
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
                <p class="titleConfirm text-center text-danger" style="font-size: 1.2rem; font-weight: bold;">¿Estás seguro de que deseas eliminar este método?</p>
                <input type="hidden" id="id_MetodoPago_delete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>