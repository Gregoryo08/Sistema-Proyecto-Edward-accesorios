<div class="modal fade" id="modalRegistroEmpleados" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog"" role=" document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Registro de Empleados</h5>

            </div>
            <div class="modal-body">
                <form id="formRegistroEmpleado" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                    <div class="form-group" style="display: flex; flex-direction: column;">
                        <label for="cargo">Cargo</label>
                        <select name="cargo" class="form-control" id="cargo">
                        </select>

                        <div class="mensaje">
                            <p id="texto_mensaje_cargo"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Cedula</label>
                        <div class="for">
                            <select name="prefijo" class="form-control" id="prefijo">
                                <option value=""></option>
                                <option value="V-" class="">V-</option>
                                <option value="E-" class="">E-</option>
                            </select>
                            <input type="text" class="form-control" id="cedula" placeholder="Ingresa tu cedula" required>

                        </div>
                        <div class="mensaje">
                            <p id="texto_mensaje_cedula"></p>
                        </div>
                    </div>
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
                                <option value=""></option>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel_register">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarCliente" style="display:none;">Registrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificar" tabindex="-1" role="dialog" aria-labelledby="modalModificarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarLabel">Modificar datos de Empleados</h5>

            </div>
            <div class="modal-body">
                <form id="formModificar">
                    <input type="hidden" id="id_empleado">

                    <div class="form-group">
                        <label for="cargo">Cargo</label>
                        <select name="cargo" class="form-control" id="cargoModificar">
                            <option value=""></option>
                        </select>

                        <div class="mensaje">
                            <p id="texto_mensaje_cargo_modificar"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="apellido">Cedula</label>
                        <div class="for">
                            <select name="prefijo" class="form-control" id="prefijoModificar">
                                <option value=""></option>
                                <option value="V-" class="">V-</option>
                                <option value="E-" class="">E-</option>
                            </select>
                            <input type="text" class="form-control" id="cedulaModificar" placeholder="Ingresa tu cedula" required>

                        </div>
                        <div class="mensaje">
                            <p id="texto_mensaje_cedula_modificar"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombreModificar">Nombre</label>
                        <input type="text" class="form-control" id="nombreModificar" name="nombre" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_nombre_modificar"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apellidoModificar">Apellido</label>
                        <input type="text" class="form-control" id="apellidoModificar" name="apellido" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_apellido_modificar"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="correoModificar">Correo</label>
                        <input type="email" class="form-control" id="correoModificar" name="correo" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_correo_modificar"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefonoModificar">Teléfono</label>
                        <div class="for">
                            <select name="operadora" class="form-control" id="operadoraModificar">
                                <option value="0412" class="">0412</option>
                                <option value="0416" class="">0416</option>
                                <option value="0426" class="">0426</option>
                                <option value="0414" class="">0414</option>
                                <option value="0424" class="">0424</option>
                            </select>
                            <input type="text" class="form-control" id="telefonoModificar" name="telefono" required>

                        </div>
                        <div class="mensaje">
                            <p id="texto_mensaje_telefono_modificar"></p>
                        </div>
                    </div>
                    <div class="form-group direccion">
                        <label for="direccionModificar">Dirección</label>
                        <textarea name="direccion" id="direccionModificar" class="form-control" placeholder="Ingresa la dirección" required></textarea>

                        <div class="mensaje">
                            <p id="texto_mensaje_direccion_modificar"></p>
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
                <input type="hidden" id="id_empleado_delete">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerDatos" tabindex="-1" aria-labelledby="modalConfirmarRegistroLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarRegistroLabel">Ver Datos Personales</h5>

            </div>
            <div class="modal-body confirmRegister" style="width: 100%;">
                <div class="contData" style="width: 100%;">
                    <div class="data" style="width: 33%;"><b>Nombre y Apellido: </b><br><span id="VerNombre"></span>
                        <span id="VerApellido"></span>
                    </div>
                    <div class="data" style="width: 33%;"><b>Cedula: </b><br><span id="VerCedula"></span></div>
                    <div class="data" style="width: 33%;"><b>Telefono: </b><br><span id="VerTelefono"></span></div>
                    <div class="data"><b>Correo: </b><br><span id="VerCorreo"></span></div>
                    <div class="data"><b>Cargo: </b><br><span id="VerCargo"></span></div>
                    <div class="data" style="width: 100%;"><b>Dirección: </b><br><span id="VerDireccion"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

<!-- -------------------------------------------------------- -->

<div class="modal fade" id="modalCrearPerfil" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarModificacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarModificacionLabel">Crear Perfil de Usuario</h5>

            </div>
            <div class="modal-body confirmRegister">
                <p>Contraseña</p>

                <form id="formPerfil">
                    <div class="form-group">
                        <input type="hidden" id="empleadoPerfil" name="id">
                        <input type="hidden" id="codigoC" name="codigo">
                    </div>

                    <div class="form-group">
                        <div class="data"><b>Nota: </b>El acceso a las funcionalidades del sistema se gestiona mediante
                            perfiles de usuario. Cada empleado, segun su rol, tendrá acceso a diferentes
                            herramientas y secciones del sistema, garantizando así un uso eficiente y seguro del sistema.</div>
                    </div>

                    <div class="form-group">
                        <div class="data"><b>Usuario: </b><span id="usuario_Cedula"></span></div>
                    </div>

                    <div class="form-group">
                        <label for="nombreModificar">Contraseña</label>
                        <input type="password" class="form-control" id="clave" name="clave" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_clave_inicio"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nombreModificar">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="claveConfirm" name="claveConfirm" required>

                        <div class="mensaje">
                            <p id="texto_mensaje_clave_confirmacion"></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <p>La contraseña debe de ser mayor a 5 caracteres, usar por lo menos una letra en Mayusculas,
                            numeros y caracteres especiales.</p>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="button_cancelar">Cancelar</button>
                <button type="button" class="btn btn-primary" id="registrarPerfil" style="display: none;">Siguiente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSeguridad" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarModificacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="display: flex; justify-content: center;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarModificacionLabel">Crear Perfil de Usuario</h5>

            </div>
            <div class="modal-body confirmRegister">
                <p>Seguridad de la Cuenta</p>

                <form id="formPerfilPreguntas">
                    <div class="form-group" style="display: flex; flex-direction: column; width: 100%;">
                        <label for="nombreModificar">Selecciona el Tipo de Rol</label>
                        <select name="idRoles" id="idRoles" class="form-control" style="width: 100%;">
                        </select>

                        <div class="mensaje">
                            <p id="texto_mensaje_rol"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 100%; display: flex; justify-content: center;">
                        <div class="data"><b>Codigo de Seguridad: </b><span id="usuario_Codigo"></span></div>
                    </div>

                    <div class="form-group" style="width: 100%;">
                        <div class="data">Un código de seguridad protege el acceso a datos y sistemas, previniendo
                            riesgos cibernéticos.</div>
                    </div>

                    <div class="form-group" style="width: 100%;">
                        <div class="data">Ayudan a confirmar que eres el legítimo propietario de la cuenta en caso de
                            olvidar tu contraseña o detectar actividad sospechosa.</div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" id="button_preguntas">Atras</button>
                <button type="button" class="btn btn-primary" id="crearPerfil" style="display: none;">Crear Perfil</button>
            </div>
        </div>
    </div>
</div>
