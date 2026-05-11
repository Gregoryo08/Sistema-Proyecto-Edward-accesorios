<div class="modal fade" id="modalClaveCodigo" tabindex="-1" role="dialog" aria-labelledby="modalCodigoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCodigoLabel">Verificar Identidad</h5>
            </div>
            <div class="modal-body confirmRegister">
                <p>Ingrese su cédula y el código de seguridad de 6 dígitos.</p>
                <form id="formValidarCodigo">
                    <div class="form-group">
                        <label>Cédula / Usuario</label>
                        <div class="for">
                            <select name="prefijo" class="form-control" id="prefijo">
                                <option value=""></option>
                                <option value="V-">V-</option>
                                <option value="E-">E-</option>
                            </select>
                            <input type="text" class="form-control" id="cedula" placeholder="Ej: 12345678" required>
                        </div>
                        <div class="mensaje">
                            <p id="mensajeClave"></p>
                        </div>
                    </div>

                    <div class="groupClave form-group" style="width: 100%;">
                        <label>Código de Seguridad</label>
                        <input type="text" class="form-control" id="codigoSeguridad" name="codigoSeguridad" placeholder="Ingrese los 6 dígitos" required maxlength="6">
                        <div class="mensaje">
                            <p id="mensajeCodigo"></p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="data">
                            <b>Nota:</b> El código de seguridad es el que configuró al momento de crear su cuenta.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarCodigo" style="display: none;">Siguiente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCambioContraseña" tabindex="-1" role="dialog" aria-labelledby="modalCambioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambioLabel">Nueva Contraseña</h5>
            </div>
            <div class="modal-body confirmRegister">
                <p>Establezca su nueva contraseña de acceso.</p>
                <form id="formCambioClave">
                    <input type="hidden" id="id_cedula" name="id">
                    <input type="hidden" id="codigo_seguridad" name="codigo">
                    <input type="hidden" id="numeroSeguridadT" name="numero" value="0">

                    <div class="form-group">
                        <label>Nueva Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave" required>
                        <div class="mensaje">
                            <p id="texto_mensaje_clave_inicio"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="claveConfirm" name="claveConfirm" required>
                        <div class="mensaje">
                            <p id="texto_mensaje_clave_confirmacion"></p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <small>La contraseña debe tener al menos 5 caracteres, una mayúscula y un carácter especial.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmarContraseña" style="display: none;">Actualizar Contraseña</button>
            </div>
        </div>
    </div>
</div>