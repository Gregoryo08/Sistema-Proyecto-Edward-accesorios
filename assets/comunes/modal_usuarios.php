<!-- Modal 1: Seguridad -->
<form id="formSeguridad">
    <input type="hidden" id="accion" name="accion">
    <div class="modal fade" id="modalSeguridad" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-lock" style="font-size: 1.5rem;"></i> Credenciales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="cedula" name="cedula" value="V-" placeholder="V-12345678">
                    <input type="password" class="form-control" id="clave" name="clave" placeholder="Clave">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="limpiarFormularioSeguridad()">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSiguiente" data-bs-target="#modalDatosPersonales" data-bs-toggle="modal">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const inputCedula = document.getElementById('cedula');

inputCedula.addEventListener('focus', function() {
    if (!this.value.startsWith('V-')) {
        this.value = 'V-' + this.value;
    }
});

inputCedula.addEventListener('keydown', function(e) {
    if ((this.selectionStart <= 2 || this.selectionEnd <= 2) && (e.key === 'Backspace' || e.key === 'Delete' || e.key === 'ArrowLeft')) {
        if (e.key === 'Backspace' || e.key === 'Delete') {
            e.preventDefault();
        }
    }
});

inputCedula.addEventListener('input', function() {
    let numeros = this.value.substring(2).replace(/[^0-9]/g, '');
    this.value = 'V-' + numeros;
});

function limpiarFormularioSeguridad() {
    document.getElementById('formSeguridad').reset();
    document.getElementById('cedula').value = 'V-';
}

function limpiarFormularioDatos() {
    document.getElementById('formDatosPersonales').reset();
}

function limpiarFormularioClasificacion() {
    document.getElementById('formClasificacion').reset();
}
</script>

<!-- Modal 2: Datos Personales -->
<form id="formDatosPersonales">
    <div class="modal fade" id="modalDatosPersonales" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-vcard" style="font-size: 1.5rem;"></i> Información Personal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-2" name="nombre" placeholder="Nombre" required>
                    <input type="text" class="form-control mb-2" name="apellido" placeholder="Apellido" required>
                    <input type="email" class="form-control mb-2" name="correo" placeholder="Correo">
                    <input type="text" class="form-control mb-2" name="telefono" placeholder="Teléfono">
                    <input type="text" class="form-control mb-2" name="direccion" placeholder="Dirección">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control mb-2" name="fecha_nacimiento">
                    <label class="form-label">Sexo</label>
                    <select class="form-select" name="sexo">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiarFormularioDatos()">Cancelar</button>
                    <button type="button" class="btn btn-secondary" data-bs-target="#modalSeguridad" data-bs-toggle="modal">Atrás</button>
                    <button type="button" class="btn btn-primary" data-bs-target="#modalClasificacion" data-bs-toggle="modal">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal 3: Clasificación -->
<form id="formClasificacion">
    <div class="modal fade" id="modalClasificacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clasificación, Rol y Cargo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select class="form-select mb-3" id="tipoUsuario" name="tipo_usuario" onchange="toggleOpciones(this.value)">
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                    </select>

                    <div id="container_rol" class="mb-3 d-none">
                        <label class="form-label">Rol</label>
                        <select class="form-select" id="id_rol" name="id_rol">
                            <option value="">Seleccione un rol</option>
                        </select>
                    </div>

                    <div id="container_cargo" class="d-none">
                        <label class="form-label">Cargo</label>
                        <select class="form-select" id="id_cargo" name="id_cargo">
                            <option value="">Seleccione un cargo</option>
                            <option value="1">Gerente</option>
                            <option value="2">Vendedor</option>
                        </select>
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

<!-- Modal: Modificar Usuario -->
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
                    <input type="text" class="form-control mb-2" id="cedula_mod" name="cedula" readonly>
                    
                    <label class="form-label">Nueva Contraseña (Opcional)</label>
                    <input type="password" class="form-control mb-2" name="clave" placeholder="Dejar en blanco para no cambiar">
                    
                    <label class="form-label">Rol</label>
                    <select class="form-select mb-2" id="id_rol_mod" name="id_rol" required></select>
                    
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control mb-2" name="nombre" required>
                    
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control mb-2" name="apellido" required>
                    
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control mb-2" name="correo">
                    
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control mb-2" name="telefono">
                    
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control mb-2" name="direccion">
                    
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control mb-2" name="fecha_nacimiento">
                    
                    <label class="form-label">Sexo</label>
                    <select class="form-select" name="sexo">
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="No especificado">No especificado</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarModificacion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</form>