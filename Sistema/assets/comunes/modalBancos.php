<div class="modal fade" id="modalBancos" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Registrar Bancos</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroBancos" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                    <div class="form-group">
                        <label for="nombre">Nombre del banco</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Ingresa el nombre del banco" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_nombre"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="precio">Numero de Cuenta</label>
                        <input type="text" class="form-control" id="numero" placeholder="Ingresa el numero de cuenta" required>
                        
                        <div class="mensaje">
                            <p id="texto_mensaje_numero"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Cedula / RIF</label>
                        <input type="text" class="form-control" id="cedula" placeholder="Ingresa el numero de cuenta" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_cedula"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Telefono</label>
                        <input type="text" class="form-control" id="telefono" placeholder="Ingresa el telefono de la cuenta" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_telefono"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="registrar" style="display:none;">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificar" tabindex="-1" role="dialog" aria-labelledby="modalModificarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarLabel">Modificar datos de las cuentas de banco</h5>

            </div>
            <div class="modal-body">
                <form id="formModificar" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                    <input type="hidden" id="id_banco" name="id">

                    <div class="form-group">
                        <label for="nombre">Nombre del banco</label>
                        <input type="text" class="form-control" id="nombre_modificar" placeholder="Ingresa el nombre del banco" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_nombre_modificar"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="precio">Numero de Cuenta</label>
                        <input type="text" class="form-control" id="numero_modificar" placeholder="Ingresa el numero de cuenta" required>
                        
                        <div class="mensaje">
                            <p id="texto_mensaje_numero_modificar"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Cedula / RIF</label>
                        <input type="text" class="form-control" id="cedula_modificar" placeholder="Ingresa el numero de cuenta" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_cedula_modificar"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Telefono</label>
                        <input type="text" class="form-control" id="telefono_modificar" placeholder="Ingresa el telefono de la cuenta" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_telefono_modificar"></p>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" id="modificar" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerService" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarModificacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarModificacionLabel">Ver Detalles del Servicio Adicional</h5>

            </div>
            <div class="modal-body confirmRegister" style="height: 50vh;">
                <div class="contData" style="align-items: flex-start; width: 100%;">
                    <div class="data"><b>Nombre del Servicio: </b><br><span id="ver_Nombre"></span></div>
                    <div class="data"><b>Precio del Servicio: </b><br><span id="ver_Precio"></span></div>
                    <div class="data" style="width: 100%; height: 100%; overflow-y: scroll;"><b>Caracteristicas del Servicio: </b><br><span id="ver_Caracteristicas"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>