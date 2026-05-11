<div class="modal fade" id="modalRegistroTurno" tabindex="-1" role="dialog" aria-labelledby="modalRegistroClienteLabel" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content"> <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroClienteLabel">
                    <i class="bi bi-calendar-plus-fill me-2"></i> Registro del Turno
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4"> <form id="formRegistroTurno" class="row g-4" style="height: 60vh; overflow-y: auto;"> <div class="col-12 text-center mb-4 pt-3 pb-4 border-bottom"> <label for="fecha" class="form-label fs-5 fw-bold">Fecha del Turno <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg mx-auto" style="max-width: 300px;">
                            <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                            <input type="date" class="form-control text-center" style="text-align: center;" id="fecha" required>
                        </div>
                        <small class="form-text text-muted mt-2">Selecciona la fecha para el turno.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="hora_entrada" class="form-label">Hora de Entrada</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="time" class="form-control" id="hora_entrada" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="hora_salida" class="form-label">Hora de Salida</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
                            <input type="time" class="form-control" id="hora_salida" required>
                        </div>
                        <div class="invalid-feedback text-danger">
                            La hora de salida debe ser posterior a la hora de entrada.
                        </div>
                    </div>

                    <div class="col-12 mb-4 pt-3 pb-2 border-top"> <label for="Listaempleado" class="form-label fs-5 fw-bold">
                            <i class="bi bi-people-fill me-2"></i> Selecciona Empleados del Turno <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" class="form-control" id="empleados" required>

                        <select class="form-select form-select-lg" id="Listaempleado" name="Listaempleado" required aria-describedby="EmpleadoAyuda">
                                    <option value="">Seleccione Un Empleado</option>
                                </select>
                        <small class="form-text text-muted mt-2">Utiliza la barra de búsqueda para encontrar y asignar empleados al turno.</small>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="TablaEmpleados" class="table table-striped table-hover table-bordered text-center align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" class="text-nowrap">Cédula</th>
                                        <th scope="col">Nombre y Apellido</th>
                                        <th scope="col">Cargo</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-muted">
                                        <td colspan="4">No hay empleados asignados aún.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mb-4 pt-3 pb-2 border-top"> <label for="observaciones" class="form-label fs-5 fw-bold">
                            <i class="bi bi-chat-dots-fill me-2"></i> Observaciones del turno
                        </label>
                        <input type="hidden" id="obs"> <div class="input-group"> <textarea name="observaciones" id="observaciones" class="form-control" placeholder="Ingresa una observación" style="width: 85%; resize: vertical;"></textarea>
                            <button class="btn btn-success" id="agregarObservacion">Agregar</button> </div>

                        <div class="mensaje mt-2">
                            <p id="texto_mensaje_obs" class="text-danger small"></p>
                        </div>
                        
                        <span id="dataServices">
                            <div class="table-responsive" id="DOM_table">
                                <div class="table-container" >
                                    <table id="TablaObs" class="table table-striped table-hover table-bordered text-center align-middle">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col" style="width: 10%;">N°</th>
                                                <th scope="col">Observaciones</th>
                                                <th scope="col" style="width: 20%;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-muted">
                                                <td colspan="3">No hay observaciones registradas.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="registrar" style="display: none;">
                    <i class="bi bi-save me-2"></i> Registrar
                </button>
            </div>
        </div>
    </div>
</div>


















<div class="modal fade" id="modalmodificarTurno" tabindex="-1" role="dialog" aria-labelledby="modalmodificarTurnoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalmodificarTurnoLabel">
                    <i class="bi bi-calendar-plus-fill me-2"></i> Modificar Datos Del Turno
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formModificarTurno" class="row g-4" style="height: 60vh; overflow-y: auto;">
                    <input type="hidden" id="id_turno_modificar" name="id_turno">
                    <div class="col-12 text-center mb-4 pt-3 pb-4 border-bottom">
                        <label for="fecha_mod" class="form-label fs-5 fw-bold">Fecha del Turno <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg mx-auto" style="max-width: 300px;">
                            <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                            <input type="date" class="form-control text-center" style="text-align: center;" id="fecha_mod" required>
                        </div>
                        <small class="form-text text-muted mt-2">Selecciona la fecha para el turno.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="hora_entrada_mod" class="form-label">Hora de Entrada</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="time" class="form-control" id="hora_entrada_mod" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="hora_salida_mod" class="form-label">Hora de Salida</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
                            <input type="time" class="form-control" id="hora_salida_mod" required>
                        </div>
                        <div class="invalid-feedback text-danger">
                            La hora de salida debe ser posterior a la hora de entrada.
                        </div>
                    </div>

                    <div class="col-12 mb-4 pt-3 pb-2 border-top">
                        <label for="Listaempleado_mod" class="form-label fs-5 fw-bold">
                            <i class="bi bi-people-fill me-2"></i> Selecciona Empleados del Turno <span class="text-danger">*</span>
                        </label>
                        <input type="hidden" class="form-control" id="empleados_mod" >

                        <select class="form-select form-select-lg" id="Listaempleado_mod" name="Listaempleado"  aria-describedby="EmpleadoAyuda">
                            <option value="">Seleccione Un Empleado</option>
                        </select>
                        <small class="form-text text-muted mt-2">Utiliza la barra de búsqueda para encontrar y asignar empleados al turno.</small>
                    </div>

                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="TablaEmpleados_mod" class="table table-striped table-hover table-bordered text-center align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col" class="text-nowrap">Cédula</th>
                                        <th scope="col">Nombre y Apellido</th>
                                        <th scope="col">Cargo</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-muted">
                                        <td colspan="4">No hay empleados asignados aún.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mb-4 pt-3 pb-2 border-top">
                        <label for="observaciones_mod" class="form-label fs-5 fw-bold">
                            <i class="bi bi-chat-dots-fill me-2"></i> Observaciones del turno
                        </label>
                        <input type="hidden" id="obs_mod">
                        <div class="input-group">
                            <textarea name="observaciones" id="observaciones_mod" class="form-control" placeholder="Ingresa una observación" style="width: 85%; resize: vertical;"></textarea>
                            <button type="button" class="btn btn-success" id="agregarObservacionMod">Agregar</button>
                        </div>

                        <div class="mensaje mt-2">
                            <p id="texto_mensaje_obs_mod" class="text-danger small"></p>
                        </div>
                        
                        <span id="dataServices_mod">
                            <div class="table-responsive" id="DOM_table_mod">
                                <div class="table-container">
                                    <table id="TablaObs_mod" class="table table-striped table-hover table-bordered text-center align-middle">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col" style="width: 10%;">N°</th>
                                                <th scope="col">Observaciones</th>
                                                <th scope="col" style="width: 20%;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-muted">
                                                <td colspan="3">No hay observaciones registradas.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_cancel_mod">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn_modificar">
                    <i class="bi bi-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>














































<div class="modal fade" id="modalConsultaTurno" tabindex="-1" aria-labelledby="modalConfirmarRegistroLabel" aria-hidden="false">
    <div class="modal-dialog" style="min-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarRegistroLabel">Detalles del Turno</h5>

            </div>

            <div class="modal-body confirmRegister">
                <div class="contData" style="width: 100%;">
                    <div class="data" style="width: 33%;"><b>Fecha del Turno: </b><br><span id="dataFecha"></span></div>

                    <div class="data" style="width: 33%;"><b>Hora de Entrada: </b><br><span id="dataHoraE"></span></div>
                    
                    <div class="data" style="width: 33%;"><b>Hora de Salida: </b><br><span id="dataHoraS"></span></div>

                    <div class="data" style="width: 100%;"><b>Observaciones del Turno: </b><br>
                        <span id="dataServices">
                            <div class="table-responsive" id="DOM_table_info">
                                <div class="table-container">

                                    <table id="tablaInfoTurnoObs" class="table table-striped table-bordered text-center">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Num°</th>
                                                <th>Descripciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </span>
                    </div>

                    <div class="data" style="width: 100%;">
                    <b>Personal del Turno: </b><br>
                        <span id="dataServices">
                            <div class="table-responsive" id="DOM_table_info">
                                <div class="table-container">

                                    <table id="tablaInfoTurno" class="table table-striped table-bordered text-center">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Cedulas</th>
                                                <th>Nombres y Apellidos</th>
                                                <th>Cargos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn_salir_ver" data-bs-dismiss="modal">Salir</button>
            </div>
        </div>
    </div>
</div>