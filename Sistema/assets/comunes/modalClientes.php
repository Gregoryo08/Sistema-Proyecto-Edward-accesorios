<div class="modal fade" id="modalRegistroCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h3 class="fw-black mb-0" style="color: #1a1a1a;">Registro de Cliente</h3>
                    <p class="text-muted small">Ingrese los datos personales y socioeconómicos del cliente</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formRegistroCliente">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cédula</label>
                            <div class="input-group">
                                <select name="prefijo" class="form-select bg-light border-light-subtle" id="prefijo" style="max-width: 80px;">
                                    <option value="V-">V-</option>
                                    <option value="E-">E-</option>
                                </select>
                                <input type="text" class="form-control bg-light border-light-subtle" id="cedula" placeholder="Ej: 12345678" required>
                            </div>
                            <p id="texto_mensaje_cedula" class="text-danger small mt-1"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Fecha de Nacimiento</label>
                            <input type="date" class="form-control bg-light border-light-subtle" id="fecha" required>
                            <p id="texto_mensaje_fecha_nacimiento" class="text-danger small mt-1"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="nombre" placeholder="Nombre completo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Apellido</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="apellido" placeholder="Apellido completo" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted text-uppercase">Correo Electrónico</label>
                            <input type="email" class="form-control bg-light border-light-subtle" id="correo" placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Sexo</label>
                            <select id="sexo" class="form-select bg-light border-light-subtle" required>
                                <option value="">Seleccione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <div class="input-group">
                                <select name="operadora" class="form-select bg-light border-light-subtle" id="operadora" style="max-width: 90px;">
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                </select>
                                <input type="tel" class="form-control bg-light border-light-subtle" id="telefono" placeholder="1234567" required>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tipo Residencia</label>
                            <select id="tipo_residencia" class="form-select bg-light border-light-subtle">
                                <option value="Propia">Propia</option>
                                <option value="Familiar">Familiar</option>
                                <option value="Alquilada">Alquilada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Estado Civil</label>
                            <select id="estado_civil" class="form-select bg-light border-light-subtle">
                                <option value="Soltero">Soltero</option>
                                <option value="Casado">Casado</option>
                                <option value="Divorciado">Divorciado</option>
                                <option value="Viudo">Viudo</option>
                                <option value="En relación">En relación</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Categoría</label>
                            <select id="profesion" class="form-select bg-light border-light-subtle">
                                <option value="Empleado">Empleado Fijo</option>
                                <option value="Independiente">Independiente</option>
                                <option value="Estudiante (Becado)">Becado</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Pensionado">Pensionado</option>
                                <option value="Desempleado">Desempleado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cargas</label>
                            <input type="number" id="carga_familiar" class="form-control bg-light border-light-subtle" value="0" min="0">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ocupación Específica</label>
                            <input type="text" id="ocupacion" class="form-control bg-light border-light-subtle" placeholder="Ej: Vendedor de repuestos / Cajera">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tasa BCV</label>
                            <input type="text" id="tasa_bcv" class="form-control bg-light border-light-subtle" value="36.50">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-info text-uppercase">Ingreso Mensual (Bolívares)</label>
                            <div class="input-group">
                                <input type="number" id="ingreso_bs" class="form-control bg-info bg-opacity-10 border-info-subtle fw-bold">
                                <span class="input-group-text bg-info border-info-subtle text-white">
                                    $ <span id="calc_usd" class="ms-1">0.00</span>
                                </span>
                            </div>
                            <input type="hidden" id="ingresos_mensuales" value="0">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Dirección Exacta</label>
                            <textarea id="direccion" class="form-control bg-light border-light-subtle" rows="2" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow" id="guardarCliente">Registrar Cliente</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0">
                <h3 class="fw-black mb-0" style="color: #1a1a1a;">Modificar Cliente</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formModificar">
                    <input type="hidden" id="clienteId" name="id">
                    <input type="hidden" id="cedulaModificar">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Nombre</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="nombreModificar" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Apellido</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="apellidoModificar" name="apellido" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Correo</label>
                            <input type="email" class="form-control bg-light border-light-subtle" id="correoModificar" name="correo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teléfono</label>
                            <input type="text" class="form-control bg-light border-light-subtle" id="telefonoModificar" name="telefono" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Residencia</label>
                            <select id="tipo_residenciaModificar" name="tipo_residencia" class="form-select bg-light border-light-subtle">
                                <option value="Propia">Propia</option>
                                <option value="Familiar">Familiar</option>
                                <option value="Alquilada">Alquilada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ingresos ($)</label>
                            <input type="number" step="0.01" class="form-control bg-light border-light-subtle" id="ingresosModificar" name="ingresos_mensuales" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Dirección</label>
                            <textarea name="direccion" id="direccionModificar" class="form-control bg-light border-light-subtle" rows="2" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Categoría Laboral</label>
                            <select id="profesionModificar" name="profesion" class="form-select bg-light border-light-subtle">
                                <option value="Empleado">Empleado Fijo</option>
                                <option value="Independiente">Independiente</option>
                                <option value="Estudiante (Becado)">Estudiante Becado</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Pensionado">Pensionado</option>
                                <option value="Desempleado">Desempleado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Carga Familiar</label>
                            <input type="number" id="carga_familiarModificar" name="carga_familiar" class="form-control bg-light border-light-subtle" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ocupación Específica</label>
                            <input type="text" id="ocupacionModificar" name="ocupacion" class="form-control bg-light border-light-subtle">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" id="modificarDatos" class="btn btn-primary rounded-pill px-4 shadow">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalVerDatos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header bg-primary text-white border-0 p-4 rounded-top-5">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Ficha del Cliente</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-12 border-bottom pb-3 text-center">
                        <label class="text-muted small text-uppercase fw-bold d-block">Nombre Completo</label>
                        <span class="h5 fw-bold" id="VerNombre"></span> <span class="h5 fw-bold" id="VerApellido"></span>
                    </div>

                    <div class="col-6 border-end">
                        <label class="text-muted small text-uppercase fw-bold d-block">Cédula</label>
                        <span class="fw-bold" id="VerCedula"></span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Sexo / Edad</label>
                        <span class="fw-bold"><span id="VerSexo"></span> - <span id="VerFecha"></span></span>
                    </div>

                    <div class="col-6 border-end">
                        <label class="text-muted small text-uppercase fw-bold d-block">Teléfono</label>
                        <span class="fw-bold" id="VerTelefono"></span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Correo</label>
                        <span class="fw-bold small" id="VerCorreo"></span>
                    </div>

                    <div class="col-6 border-end">
                        <label class="text-muted small text-uppercase fw-bold d-block">Estado Civil</label>
                        <span class="fw-bold" id="VerEstadoCivil"></span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Carga Familiar</label>
                        <span class="fw-bold" id="VerCargas"></span>
                    </div>

                    <div class="col-6 border-end">
                        <label class="text-muted small text-uppercase fw-bold d-block">Residencia</label>
                        <span class="fw-bold" id="VerTipoResidencia"></span>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold d-block">Categoría Laboral</label>
                        <span class="fw-bold" id="VerProfesion"></span>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background-color: #e3f2fd; border-left: 5px solid #0dcaf0;">
                            <label class="text-info small text-uppercase fw-bold d-block">Ingresos Mensuales / Ocupación</label>
                            <span class="h5 fw-bold text-dark" id="VerIngresos"></span>
                            <div class="small text-muted mt-1 fw-bold" id="VerOcupacion"></div>
                        </div>
                    </div>

                    <div class="col-12 bg-light p-3 rounded-3 border">
                        <label class="text-muted small text-uppercase fw-bold d-block">Dirección Exacta</label>
                        <span id="VerDireccion" class="small"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-dark rounded-pill px-5 shadow" data-bs-dismiss="modal" id="btn_salir_ver">Cerrar</button>
            </div>
        </div>
    </div>
</div>