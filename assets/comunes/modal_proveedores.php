<div class="modal fade" id="modalRegistroProveedor" tabindex="-1" aria-labelledby="modalRegistroProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroProveedorLabel">Registrar Nuevo Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formularioRegistroProveedor">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="proveedor" class="form-label">Nombre del Proveedor</label>
                            <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                            <div id="error_proveedor" class="text-danger small" style="display: none;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rif" class="form-label">Rif</label>
                            <div class="input-group">
                                <select class="form-select" id="prefijo" style="max-width: 35%;">
                                    <option value="J-">J-</option>
                                    <option value="G-">G-</option>
                                </select>
                                <input type="text" class="form-control" id="rif" name="rif" required>
                            </div>
                            <div id="error_rif" class="text-danger small" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <div class="input-group">
                                <select class="form-select" id="codTelefono" style="max-width: 35%;">
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                </select>
                                <input type="tel" class="form-control" id="telefono" name="telefono" required>
                            </div>
                            <div id="error_telefono" class="text-danger small" style="display: none;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo">
                            <div id="error_correo" class="text-danger small" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <textarea class="form-control" id="ubicacion" name="ubicacion" rows="2"></textarea>
                        <div id="error_ubicacion" class="text-danger small" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_registrar">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarProveedor" tabindex="-1" aria-labelledby="modalModificarProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModificarProveedorLabel">Modificar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formularioModificarProveedor">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="proveedor_modificar" class="form-label">Nombre del Proveedor</label>
                            <input type="text" class="form-control" id="proveedor_modificar" name="proveedor" required>
                            <div id="error_proveedor_modificar" class="text-danger small" style="display: none;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rif_modificar" class="form-label">Rif</label>
                            <input type="text" class="form-control" id="rif_modificar" name="rif" readonly>
                            <div id="error_rif_modificar" class="text-danger small" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono_modificar" class="form-label">Teléfono</label>
                            <div class="input-group">
                                <select class="form-select" id="codTelefonoM" style="max-width: 35%;">
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0412">0412</option>
                                    <option value="0416">0416</option>
                                </select>
                                <input type="tel" class="form-control" id="telefono_modificar" name="telefono" required>
                            </div>
                            <div id="error_telefono_modificar" class="text-danger small" style="display: none;"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="correo_modificar" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_modificar" name="correo">
                            <div id="error_correo_modificar" class="text-danger small" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion_modificar" class="form-label">Ubicación</label>
                        <textarea class="form-control" id="ubicacion_modificar" name="ubicacion" rows="2"></textarea>
                        <div id="error_ubicacion_modificar" class="text-danger small" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_modificar">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConsultarProveedor" tabindex="-1" aria-labelledby="modalConsultarProveedorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConsultarProveedorLabel">Datos del Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Rif</label>
                    <p id="consulta_rif"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre del Proveedor</label>
                    <p id="consulta_proveedor"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Teléfono</label>
                    <p id="consulta_telefono"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Correo Electrónico</label>
                    <p id="consulta_correo"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Ubicación</label>
                    <p id="consulta_ubicacion"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>