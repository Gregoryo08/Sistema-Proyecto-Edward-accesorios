<div class="modal fade" id="modalRegistroEmpleados" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title" id="modalRegistroClienteLabel"><i class="bi bi-person-lines-fill" style="font-size: 1.2rem;"></i> Registro de Empleados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRegistroEmpleado">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Completa los campos obligatorios para registrar al empleado en el sistema.</p>
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cargo</label>
                            <div class="input-group">
                                <select name="cargo" class="form-select bg-light border-light-subtle" id="cargo" style="width: 100%;"></select>
                            </div>
                            <p class="mensaje" id="texto_mensaje_cargo"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cédula</label>
                            <div class="input-group">
                                <select name="prefijo" class="form-select bg-light border-light-subtle" id="prefijo" style="max-width: 80px;">
                                    <option value="V-">V-</option>
                                    <option value="E-">E-</option>
                                </select>
                                <input type="text" class="form-control bg-light border-light-subtle" id="cedula" placeholder="Ej: 12345678">
                            </div>
                            <p class="mensaje" id="texto_mensaje_cedula"></p>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="nombre" placeholder="Nombre completo">
                            <p class="mensaje" id="texto_mensaje_nombre"></p>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Apellido</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="apellido" placeholder="Apellido completo">
                            <p class="mensaje" id="texto_mensaje_apellido"></p>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento</label>
                            <input type="date" class="form-control bg-light border-light-subtle" id="fecha_nacimiento">
                            <p class="mensaje" id="texto_mensaje_fecha_nacimiento"></p>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Sexo</label>
                            <select name="sexo" class="form-select bg-light border-light-subtle" id="sexo">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <p class="mensaje" id="texto_mensaje_sexo"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                            <input type="email" class="form-control bg-light border-light-subtle" id="correo" placeholder="ejemplo@correo.com">
                            <p class="mensaje" id="texto_mensaje_correo"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <div class="input-group">
                                <select name="operadora" class="form-select bg-light border-light-subtle" id="operadora" style="max-width: 100px;">
                                    <option value="0412">0412</option>
                                    <option value="0414">0414</option>
                                    <option value="0416">0416</option>
                                    <option value="0422">0422</option>
                                    <option value="0424">0424</option>
                                    <option value="0426">0426</option>
                                </select>
                                <input type="tel" class="form-control bg-light border-light-subtle" id="telefono" placeholder="Número">
                            </div>
                            <p class="mensaje" id="texto_mensaje_telefono"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Dirección</label>
                            <textarea name="direccion" id="direccion" class="form-control bg-light border-light-subtle" rows="2" placeholder="Ingresa la dirección"></textarea>
                            <p class="mensaje" id="texto_mensaje_direccion"></p>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel_register">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_registrar">Registrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para modificar datos de empleados -->

<div class="modal fade" id="modalModificar" tabindex="-1" role="dialog" aria-labelledby="modalModificarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarLabel"><i class="bi bi-person-gear" style="font-size: 1.5 rem;"></i> Modificar datos de Empleados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formModificar">
                <div class="modal-body">
                    <input type="hidden" id="id_empleado">
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cargo</label>
                            <select name="cargo" class="form-select bg-light border-light-subtle" id="cargoModificar">
                                <option value="">Seleccione un cargo</option>
                            </select>
                            <p id="texto_mensaje_cargo_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cédula</label>
                            <div class="input-group">
                                <select id="prefijoModificar" class="form-select bg-light border-light-subtle" style="max-width: 100px;" disabled>
                                    <option value="V-">V-</option>
                                    <option value="E-">E-</option>
                                </select>
                                <input type="text" class="form-control" id="cedulaModificar" readonly>
                            </div>
                            <p id="texto_mensaje_cedula_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre</label>
                            <input type="text" class="form-control" id="nombreModificar" name="nombre">
                            <p id="texto_mensaje_nombre_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Apellido</label>
                            <input type="text" class="form-control" id="apellidoModificar" name="apellido">
                            <p id="texto_mensaje_apellido_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>

                        <input type="hidden" id="fecha_nacimiento_real" name="fecha_nacimiento_real">

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento</label>
                            <input type="text" class="form-control" id="fechaNacimientoModificar" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Sexo</label>
                            <select name="sexo" class="form-select bg-light border-light-subtle" id="sexoModificar">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div class="col-12 mb-2">
                            <label for="correoModificar" class="form-label small fw-bold text-muted text-uppercase">Correo</label>
                            <input type="email" class="form-control" id="correoModificar" name="correo">
                            <p id="texto_mensaje_correo_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <div class="input-group">
                                <select id="operadoraModificar" class="form-select" style="max-width: 110px;">
                                    <option value="0412">0412</option>
                                    <option value="0414">0414</option>
                                    <option value="0416">0416</option>
                                    <option value="0422">0422</option>
                                    <option value="0424">0424</option>
                                    <option value="0426">0426</option>
                                </select>
                                <input type="text" class="form-control" id="telefonoModificar" name="telefono">
                            </div>
                            <p id="texto_mensaje_telefono_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>

                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Dirección</label>
                            <textarea name="direccion" id="direccionModificar" class="form-control" rows="2"></textarea>
                            <p id="texto_mensaje_direccion_modificar" class="text-danger small" style="display: none; margin: 0;"></p>
                        </div>
                    </div>  
                </div>
            </form>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body confirmRegister text-center">
                <p class="titleConfirm fs-5 mb-0">¿Estás seguro?</p>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarRegistroLabel"><i class="bi bi-person-vcard" style="font-size: 1.5rem; margin-right: 0.5rem;"></i>Ver Datos Personales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 ms-3">
                    <div class="col-md-6"><b>Nombre y Apellido:</b><br><span id="VerNombre"></span> <span id="VerApellido"></span></div>
                    <div class="col-md-6"><b>Cédula:</b><br><span id="VerCedula"></span></div>
                    <div class="col-md-6"><b>Teléfono:</b><br><span id="VerTelefono"></span></div>
                    <div class="col-md-6"><b>Sexo:</b><br><span id="VerSexo"></span></div>
                    <div class="col-md-6"><b>Fecha Nac.:</b><br><span id="VerFechaNac"></span></div>
                    <div class="col-md-6"><b>Cargo:</b><br><span id="VerCargo"></span></div>
                    <div class="col-12"><b>Correo:</b><br><span id="VerCorreo"></span></div>
                    <div class="col-12"><b>Dirección:</b><br><span id="VerDireccion"></span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Salir</button>
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