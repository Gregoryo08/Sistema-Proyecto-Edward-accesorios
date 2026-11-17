<div class="modal fade" id="modalRegistroEmpleados" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">Registro de Empleados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistroEmpleado" style="display: flex; flex-wrap: wrap; justify-content: space-between;">

                    <div class="form-group" style="width: 48%; display: flex; flex-direction: column;">
                        <label for="cargo">Cargo</label>
                        <select name="cargo" class="form-control" id="cargo"></select>
                        <div class="mensaje">
                            <p id="texto_mensaje_cargo"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 48%;">
                        <label for="cedula">Cédula</label>
                        <div class="input-group">
                            <select name="prefijo" class="form-control" id="prefijo" style="max-width: 80px;">
                                <option value=""></option>
                                <option value="V-">V-</option>
                                <option value="E-">E-</option>
                            </select>
                            <input type="text" class="form-control" id="cedula" placeholder="Número" required>
                        </div>
                        <div class="mensaje">
                            <p id="texto_mensaje_cedula"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 48%;">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre" required>
                        <div class="mensaje">
                            <p id="texto_mensaje_nombre"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 48%;">
                        <label for="apellido">Apellido</label>
                        <input type="text" class="form-control" id="apellido" placeholder="Apellido" required>
                        <div class="mensaje">
                            <p id="texto_mensaje_apellido"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 48%;">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" required>
                        <div class="mensaje">
                            <p id="texto_mensaje_fecha_nacimiento"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 48%;">
                        <label for="sexo">Sexo</label>
                        <select name="sexo" class="form-control" id="sexo" required>
                            <option value="">Seleccione...</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                        <div class="mensaje">
                            <p id="texto_mensaje_sexo"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 100%;">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" placeholder="ejemplo@correo.com" required>
                        <div class="mensaje">
                            <p id="texto_mensaje_correo"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 100%;">
                        <label for="telefono">Teléfono</label>
                        <div class="input-group">
                            <select name="operadora" class="form-control" id="operadora" style="max-width: 100px;">
                                <option value=""></option>
                                <option value="0412">0412</option>
                                <option value="0416">0416</option>
                                <option value="0426">0426</option>
                                <option value="0414">0414</option>
                                <option value="0424">0424</option>
                            </select>
                            <input type="tel" class="form-control" id="telefono" placeholder="Número" required>
                        </div>
                        <div class="mensaje">
                            <p id="texto_mensaje_telefono"></p>
                        </div>
                    </div>

                    <div class="form-group" style="width: 100%;">
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
                <button type="button" class="btn btn-primary" id="btn_registrar">Registrar</button>
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
                        <label for="cedulaModificar">Cédula</label>
                        <div class="d-flex">
                            <select id="prefijoModificar" class="form-control" style="width: 30%;" disabled>
                                <option value="V-">V-</option>
                                <option value="E-">E-</option>
                            </select>
                            <input type="text" class="form-control" id="cedulaModificar" readonly>
                        </div>
                        <p id="texto_mensaje_cedula_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <div class="form-group">
                        <label for="cargoModificar">Cargo</label>
                        <select name="cargo" class="form-control" id="cargoModificar">
                            <option value="">Seleccione un cargo</option>
                        </select>
                        <p id="texto_mensaje_cargo_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <div class="form-group">
                        <label for="nombreModificar">Nombre</label>
                        <input type="text" class="form-control" id="nombreModificar" name="nombre" required>
                        <p id="texto_mensaje_nombre_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <div class="form-group">
                        <label for="apellidoModificar">Apellido</label>
                        <input type="text" class="form-control" id="apellidoModificar" name="apellido" required>
                        <p id="texto_mensaje_apellido_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <div class="form-group">
                        <label for="sexoModificar">Sexo</label>
                        <select name="sexo" class="form-control" id="sexoModificar">
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="correoModificar">Correo</label>
                        <input type="email" class="form-control" id="correoModificar" name="correo" required>
                        <p id="texto_mensaje_correo_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <div class="form-group">
                        <label for="telefonoModificar">Teléfono</label>
                        <div class="d-flex">
                            <select id="operadoraModificar" class="form-control" style="width: 40%;">
                                <option value="0412">0412</option>
                                <option value="0414">0414</option>
                                <option value="0424">0424</option>
                                <option value="0416">0416</option>
                                <option value="0426">0426</option>
                            </select>
                            <input type="text" class="form-control" id="telefonoModificar" name="telefono" required>
                        </div>
                        <p id="texto_mensaje_telefono_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <div class="form-group">
                        <label for="direccionModificar">Dirección</label>
                        <textarea name="direccion" id="direccionModificar" class="form-control" required></textarea>
                        <p id="texto_mensaje_direccion_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                    </div>

                    <input type="hidden" id="fecha_nacimiento_real" name="fecha_nacimiento_real">

                    <div class="form-group">
                        <label for="fechaNacimientoModificar">Fecha de Nacimiento</label>
                        <input type="text" class="form-control" id="fechaNacimientoModificar" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="modificarDatos" class="btn btn-primary">Guardar Cambios</button>
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
                    <div class="data" style="width: 33%;"><b>Sexo: </b><br><span id="VerSexo"></span></div>
                    <div class="data" style="width: 33%;"><b>Fecha Nac.: </b><br><span id="VerFechaNac"></span></div>

                    <div class="data" style="width: 50%;"><b>Correo: </b><br><span id="VerCorreo"></span></div>
                    <div class="data" style="width: 50%;"><b>Cargo: </b><br><span id="VerCargo"></span></div>

                    <div class="data" style="width: 100%;"><b>Dirección: </b><br><span id="VerDireccion"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Salir</button>
            </div>
        </div>
    </div>
</div>

<style>
    #modalRegistroEmpleados input,
    #modalRegistroEmpleados select,
    #modalRegistroEmpleados textarea,
    #modalModificar input,
    #modalModificar select,
    #modalModificar textarea {
        border: 1px solid #cfd4da !important;
        box-shadow: none !important;
        outline: none !important;
        background-image: none !important;
    }

    #modalRegistroEmpleados .mensaje p,
    #modalModificar .mensaje p,
    #modalRegistroEmpleados [id^="texto_mensaje_"],
    #modalModificar [id^="texto_mensaje_"] {
        color: #ff4d4d !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        margin-top: 4px !important;
        text-align: left !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        clip-path: none !important;
        line-height: 1.3 !important;
    }
</style>
