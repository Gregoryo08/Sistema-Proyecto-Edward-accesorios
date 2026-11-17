<div class="modal fade" id="modalRegistrarServicio" data-bs-keyboard="true" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0 rounded-top-5">
                <div>
                    <h3 class="fw-black mb-0" style="color: dark;">Registrar Servicio</h3>
                    <p style="color: dark;">Complete los datos para abrir una nueva orden de servicio</p>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioRegistrarServicio">
                    <div class="row g-3">
                        <div class="col-md-12 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cliente</label>
                            <select name="cedula_persona" id="reg_cedula" class="form-select" required>
                                <option value="" selected disabled>Buscar cliente por cédula o nombre...</option>
                            </select>
                            <div id="error_cedula_persona" class="msg-error">Seleccione un cliente</div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Marca del Equipo</label>
                            <select name="marca" id="reg_marca" class="form-select" required>
                                <option value="" selected disabled>Seleccionar marca...</option>
                            </select>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Especialidad</label>
                            <select name="especialidad" id="reg_especialidad" class="form-select" required>
                                <option value="" selected disabled>Seleccionar especialidad...</option>
                            </select>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Modelo / Equipo</label>
                            <input type="text" name="equipo" id="reg_equipo" class="form-control" placeholder="Ej: iPhone 13, Laptop HP..." required>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-uppercase fw-bold text-muted">Teléfono de Contacto</label>
                            <input type="text" name="telefono" id="reg_telefono" class="form-control solo-numeros" placeholder="Ej: 04120000000" maxlength="11" required>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Diagnóstico Inicial / Falla</label>
                            <textarea name="falla" id="reg_falla" class="form-control" rows="3" placeholder="Describa el problema reportado por el cliente..." required></textarea>
                            <div class="msg-error"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow" id="btnGuardarRegistro">Registrar Servicio</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalModificarServicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 pb-0 rounded-top-5">
                <div class="text-dark">
                    <h3 class="fw-black mb-0">Modificar Servicio</h3>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formularioModificarServicio">
                    <input type="hidden" name="id" id="mod_id_servicio">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Equipo / Modelo</label>
                            <input type="text" name="equipo" id="mod_equipo" class="form-control" required>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" id="mod_estado" class="form-select" required>
                                <option value="" selected disabled>Seleccione un estado...</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Reparado">Reparado</option>
                                <option value="Entregado">Entregado</option>
                                <option value="Cobrado">Cobrado</option>
                            </select>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Falla Inicial</label>
                            <textarea name="falla" id="mod_falla" class="form-control" rows="2" required></textarea>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Diagnóstico Técnico</label>
                            <textarea name="diagnostico" id="mod_diagnostico" class="form-control" rows="3" required></textarea>
                            <div class="msg-error"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Agregar Producto</label>
                            <select id="selectAgregarProducto" class="form-select mb-2">
                                <option value="">Seleccione un producto...</option>
                            </select>
                            <div class="msg-error"></div>
                            <table class="table table-sm" id="tablaProductosModificar">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Monto Total ($)</label>
                            <input type="number" name="monto" id="mod_monto" class="form-control" step="0.01" required>
                            <div class="msg-error"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn rounded-pill px-4 text-white" id="btnGuardarModificacion">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="modalAgregarProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Productos al Servicio</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idServicioActual">
                <div class="row">
                    <div class="col-md-8">
                        <select id="selectProductos" class="form-control"></select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="cantidadProducto" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success" id="btnAgregarALista">Añadir</button>
                    </div>
                </div>
                <table class="table mt-3">
                    <thead><tr><th>Producto</th><th>Cant.</th><th>Acción</th></tr></thead>
                    <tbody id="listaProductosTemporal"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnGuardarCarrito">Guardar Todo</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalCobro" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Procesar Cobro</h5></div>
            <div class="modal-body">
                <p>Precio de repuestos: <span id="precioRepuestos">0</span></p>
              
                <input type="hidden" id="idServicioCobro">
                
                <div class="form-group">
                    <label>Monto Total a Cobrar:</label>
                    <input type="text" id="montoTotalCobro" class="form-control" inputmode="decimal" required>
                    <div class="msg-error"></div>
                </div>
                
                
                <div class="form-group">
                    <label>Diagnóstico:</label>
                    <textarea id="diagnostico_cobro" class="form-control" rows="2" required></textarea>
                    <div class="msg-error"></div>
                </div>
                <div class="form-group">
                    <label>Nota del Técnico:</label>
                    <textarea id="nota_tecnico_cobro" class="form-control" rows="2" required></textarea>
                    <div class="msg-error"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnConfirmarCobro">Confirmar Cobro</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalVerServicio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-5">
            <div class="modal-header border-0 p-4 rounded-top-5">
                <h3 class="fw-black mb-0 text-black">Detalles del Servicio</h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="cuerpoVerServicio">
                <div id="infoGeneral"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="contenidoTablaProductos">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    #modalRegistrarServicio .msg-error,
    #modalModificarServicio .msg-error,
    #modalAgregarProducto .msg-error,
    #modalCobro .msg-error {
        display: none;
    }
</style>