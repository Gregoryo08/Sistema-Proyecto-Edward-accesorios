<div class="modal fade" id="modalRegistroProducto" tabindex="-1" role="dialog" aria-labelledby="modalRegistroProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistroProductoLabel">Registrar Nuevo Producto / Inventario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistroProducto">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombre" required>
                            <small id="texto_mensaje_nombre" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Marca</label>
                            <select class="form-control" id="id_marca" required>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-control" id="id_categoria" required>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimo" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Máximo</label>
                            <input type="number" class="form-control" id="stock_maximo" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Actual</label>
                            <input type="number" class="form-control" id="stock_actual" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Precio Detal</label>
                            <input type="number" step="0.01" class="form-control" id="precio" required>
                        </div>
                    </div>

                    <div id="seccion_telefono" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #dee2e6;">
                        <h6 class="text-primary"><i class="bi bi-phone"></i> Datos específicos del Teléfono</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">IMEI</label>
                                <input type="text" class="form-control" id="imei" placeholder="Número de serie">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RAM</label>
                                <input type="text" class="form-control" id="ram" placeholder="Ej: 8GB">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Almacenamiento</label>
                                <input type="text" class="form-control" id="almacenamiento" placeholder="Ej: 128GB">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnRegistrarProducto">Guardar Producto</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModificarProducto" tabindex="-1" role="dialog" aria-labelledby="modalModificarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="modalModificarProductoLabel" style="color: black;">Modificar Datos Del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModificarProducto">
                    <input type="hidden" id="producto_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombreModificar" name="nombre" required>
                            <small id="texto_mensaje_nombre_modificar" class="text-danger" style="display:none;"></small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Marca</label>
                            <select class="form-control" id="marcaModificar" name="id_marca" required></select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-control" id="categoriaModificar" name="id_categoria" required></select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control" id="stock_minimoModificar" name="stock_minimo" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Máximo</label>
                            <input type="number" class="form-control" id="stock_maximoModificar" name="stock_maximo" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Stock Actual</label>
                            <input type="number" class="form-control" id="stock_actualModificar" name="stock_actual" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Precio Detal</label>
                            <input type="number" step="0.01" class="form-control" id="precioModificar" name="precio" required>
                        </div>
                    </div>

                    <div id="seccion_telefono_modificar" style="display: none; background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
                        <h6 class="text-dark"><i class="bi bi-phone"></i> Detalles del Equipo</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">IMEI</label>
                                <input type="text" class="form-control" id="imeiModificar">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">RAM</label>
                                <input type="text" class="form-control" id="ramModificar">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Almacenamiento</label>
                                <input type="text" class="form-control" id="almacenamientoModificar">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnModificarProducto" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalDetallesTelefono" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header  text-white">
                <h5 class="modal-title">Especificaciones Técnicas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item"><strong>IMEI:</strong> <span id="det_imei"></span></li>
                    <li class="list-group-item"><strong>Memoria RAM:</strong> <span id="det_ram"></span></li>
                    <li class="list-group-item"><strong>Almacenamiento:</strong> <span id="det_alm"></span></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>