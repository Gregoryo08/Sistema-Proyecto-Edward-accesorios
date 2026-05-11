
<div class="modal fade" id="modalModificarDatos" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Modificar datos de Perfil</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroCliente">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="cargo" id="cargos">
                    
                    <input type="hidden" class="form-control" id="cedula" placeholder="Ingresa tu cedula" required>

                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Ingresa el nombre" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_nombre"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" class="form-control" id="apellido" placeholder="Ingresa el apellido" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_apellido"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_correo"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <div class="for">
                            <select name="operadora" class="form-control" id="operadora">
                                <option value="0412" class="">0412</option>
                                <option value="0416" class="">0416</option>
                                <option value="0426" class="">0426</option>
                                <option value="0414" class="">0414</option>
                                <option value="0424" class="">0424</option>
                            </select>
                            <input type="tel" class="form-control" id="telefono" placeholder="Ingresa el teléfono" required>
                        </div>

                        <div class="mensaje">
                            <p id="texto_mensaje_telefono"></p>
                        </div>
                    </div>

                    <div class="form-group direccion" style="width: 100%;">
                        <label for="direccion">Dirección</label>
                        <textarea name="direccion" id="direccion" class="form-control" placeholder="Ingresa la dirección" required></textarea>

                        <div class="mensaje">
                            <p id="texto_mensaje_direccion"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modificar">Aceptar</button>
            </div>
        </div>
    </div>
</div>
