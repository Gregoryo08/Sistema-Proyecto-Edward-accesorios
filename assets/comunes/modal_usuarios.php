<form id="formSeguridad">
    <input type="hidden" id="accion" name="accion">
    <div class="modal fade" id="modalSeguridad" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Credenciales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="cedula" class="form-label">Cédula</label>
                    <input type="text" class="form-control mb-1" id="cedula" name="cedula" value="V-" placeholder="V-12345678">
                    <p id="msg_cedula" class="mensaje-error-usr"></p>

                    <label for="clave" class="form-label mt-2">Contraseña</label>
                    <input type="password" class="form-control mb-1" id="clave" name="clave" placeholder="Clave">
                    <p id="msg_clave" class="mensaje-error-usr"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="limpiarFormularioSeguridad()">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSiguienteSeguridad">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="formDatosPersonales">
    <div class="modal fade" id="modalDatosPersonales" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Información Personal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control mb-1" id="nombre" name="nombre" placeholder="Nombre">
                    <p id="msg_nombre" class="mensaje-error-usr"></p>

                    <label class="form-label mt-2">Apellido</label>
                    <input type="text" class="form-control mb-1" id="apellido" name="apellido" placeholder="Apellido">
                    <p id="msg_apellido" class="mensaje-error-usr"></p>

                    <label class="form-label mt-2">Correo Electrónico</label>
                    <input type="email" class="form-control mb-1" id="correo" name="correo" placeholder="ejemplo@correo.com">
                    <p id="msg_correo" class="mensaje-error-usr"></p>

                    <label class="form-label mt-2">Teléfono</label>
                    <input type="text" class="form-control mb-1" id="telefono" name="telefono" placeholder="Número de teléfono">
                    <p id="msg_telefono" class="mensaje-error-usr"></p>

                    <label class="form-label mt-2">Dirección</label>
                    <input type="text" class="form-control mb-1" id="direccion" name="direccion" placeholder="Dirección">
                    <p id="msg_direccion" class="mensaje-error-usr"></p>

                    <label class="form-label mt-2">Fecha de Nacimiento</label>
                    <input type="date" class="form-control mb-1" id="fecha_nacimiento" name="fecha_nacimiento">
                    <p id="msg_fecha_nacimiento" class="mensaje-error-usr"></p>

                    <label class="form-label mt-2">Sexo</label>
                    <select class="form-select mb-1" id="sexo" name="sexo">
                        <option value="">Seleccione...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                    <p id="msg_sexo" class="mensaje-error-usr"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiarFormularioDatos()">Cancelar</button>
                    <button type="button" class="btn btn-secondary" data-bs-target="#modalSeguridad" data-bs-toggle="modal">Atrás</button>
                    <button type="button" class="btn btn-primary" id="btnSiguienteDatos">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="formClasificacion">
    <div class="modal fade" id="modalClasificacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clasificación, Rol y Cargo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Tipo de Usuario</label>
                    <select class="form-select mb-1" id="tipoUsuario" name="tipo_usuario" onchange="toggleOpciones(this.value)">
                        <option value="">Seleccione tipo...</option>
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                    </select>
                    <p id="msg_tipoUsuario" class="mensaje-error-usr"></p>

                    <div id="container_rol" class="mb-2 d-none">
                        <label class="form-label mt-2">Rol</label>
                        <select class="form-select mb-1" id="id_rol" name="id_rol">
                            <option value="">Seleccione un rol</option>
                        </select>
                        <p id="msg_id_rol" class="mensaje-error-usr"></p>
                    </div>

                    <div id="container_cargo" class="d-none">
                        <label class="form-label mt-2">Cargo</label>
                        <select class="form-select mb-1" id="id_cargo" name="id_cargo">
                            <option value="">Seleccione un cargo</option>
                            <option value="1">Gerente</option>
                            <option value="2">Vendedor</option>
                        </select>
                        <p id="msg_id_cargo" class="mensaje-error-usr"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiarFormularioClasificacion()">Cancelar</button>
                    <button type="button" class="btn btn-secondary" data-bs-target="#modalDatosPersonales" data-bs-toggle="modal">Atrás</button>
                    <button type="button" class="btn btn-success" id="btnFinalizarRegistro">Registrar Todo</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form id="formModificarUsuario">
    <div class="modal fade" id="modalModificar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Modificar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="accion_mod" name="accion" value="modificar">
                    
                    <label class="form-label">Cédula</label>
                    <input type="text" class="form-control mb-1" id="cedula_mod" name="cedula" readonly>
                    
                    <label class="form-label mt-2">Nueva Contraseña (Opcional)</label>
                    <input type="password" class="form-control mb-1" id="clave_mod" name="clave" placeholder="Dejar en blanco para no cambiar">
                    <p id="msg_clave_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Rol</label>
                    <select class="form-select mb-1" id="id_rol_mod" name="id_rol"></select>
                    <p id="msg_id_rol_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Nombre</label>
                    <input type="text" class="form-control mb-1" id="nombre_mod" name="nombre">
                    <p id="msg_nombre_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Apellido</label>
                    <input type="text" class="form-control mb-1" id="apellido_mod" name="apellido">
                    <p id="msg_apellido_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Correo</label>
                    <input type="email" class="form-control mb-1" id="correo_mod" name="correo">
                    <p id="msg_correo_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Teléfono</label>
                    <input type="text" class="form-control mb-1" id="telefono_mod" name="telefono">
                    <p id="msg_telefono_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Dirección</label>
                    <input type="text" class="form-control mb-1" id="direccion_mod" name="direccion">
                    <p id="msg_direccion_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Fecha de Nacimiento</label>
                    <input type="date" class="form-control mb-1" id="fecha_nacimiento_mod" name="fecha_nacimiento">
                    <p id="msg_fecha_nacimiento_mod" class="mensaje-error-usr"></p>
                    
                    <label class="form-label mt-2">Sexo</label>
                    <select class="form-select mb-1" id="sexo_mod" name="sexo">
                        <option value="">Seleccione...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="No especificado">No especificado</option>
                    </select>
                    <p id="msg_sexo_mod" class="mensaje-error-usr"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarModificacion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    #modalSeguridad input, #modalSeguridad select,
    #modalDatosPersonales input, #modalDatosPersonales select,
    #modalClasificacion select, #modalModificar input, #modalModificar select {
        border: 1px solid #cfd4da !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .mensaje-error-usr {
        color: #ff4d4d !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        margin-top: 4px !important;
        text-align: left !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        line-height: 1.3 !important;
        display: none;
    }
</style>