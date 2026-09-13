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
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" placeholder="Ingresa una descripción o detalles del producto">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                            <small class="text-muted">JPG, PNG, GIF o WebP. Máx. 5MB. El sistema la guardará como: <b>nombre_producto.ext</b></small>
                        </div>
                        <div class="col-md-6 mb-3 text-center">
                            <label class="form-label">Vista Previa</label>
                            <div id="previewImagen" style="border:2px dashed #ccc; border-radius:8px; padding:10px; min-height:120px; display:flex; align-items:center; justify-content:center; background:#f8f9fa;">
                                <span class="text-muted">Sin imagen seleccionada</span>
                            </div>
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
                <h5 class="modal-title" id="modalModificarProductoLabel" style="color: black;">Modificar Datos E Imagen Del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModificarProducto">
                    <input type="hidden" id="producto_id" name="id">
                    <input type="hidden" id="imagen_actualModificar" name="imagen_actual">
                    <input type="hidden" id="quitar_imagenModificar" name="quitar_imagen" value="0">

                    <div class="text-center mb-4 p-3" style="background:#f0f4f8; border-radius:12px; border:2px dashed #0077b6;">
                        <label class="form-label fw-bold mb-2" style="color:#0077b6; font-size:14px;">
                            <i class="bi bi-camera"></i> Imagen del Producto
                        </label>
                        <div id="previewImagenModificar" style="min-height:150px; display:flex; align-items:center; justify-content:center;">
                            <img src="assets/img/productos/default.jpg" style="max-height:150px; max-width:200px; border-radius:10px; border:2px solid #ddd;">
                        </div>
                        <label for="imagenModificar" class="btn btn-primary mt-3" style="cursor:pointer; padding:8px 30px;">
                            <i class="bi bi-folder2-open"></i> Seleccionar imagen del computador
                        </label>
                        <input type="file" id="imagenModificar" name="imagen" accept="image/*" style="display:none;">
                        <p class="text-muted mt-2 mb-0" style="font-size:12px;">Selecciona una foto desde tu computador. El sistema la guardará con el nombre del producto.</p>
                        <div class="mt-2" id="contenedorEliminarImagenModificar" style="display:none;">
                            <button type="button" id="btnEliminarImagenModificar" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> Eliminar imagen (volver a defecto)
                            </button>
                        </div>
                    </div>
                    
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
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcionModificar" name="descripcion">
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
                                <input type="text" class="form-control" id="imeiModificar" readonly>
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

<div class="modal fade" id="modalDetallesProducto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;"><i class="bi bi-info-circle"></i> Ver Detalles del Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="det_imagen" src="assets/img/productos/default.jpg" alt="Imagen del producto"
                            onerror="this.src='assets/img/productos/default.jpg'"
                            style="max-width:100%; max-height:160px; border-radius:10px; border:2px solid #dee2e6; object-fit:contain;">
                        <div class="mt-2">
                            <span id="det_estado" class="badge"></span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 id="det_nombre" class="mb-1 text-dark"></h4>
                        <p class="text-muted mb-2"><i class="bi bi-tag"></i> <span id="det_categoria"></span> &nbsp; <i class="bi bi-shield"></i> <span id="det_marca"></span></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Precio Detal</span>
                                <strong id="det_precio" class="text-dark"></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Stock Actual</span>
                                <strong id="det_stock_actual" class="text-dark"></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Stock Mínimo Permitido</span>
                                <span id="det_stock_min" class="badge text-dark rounded-pill"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Stock Máximo Permitido</span>
                                <span id="det_stock_max" class="badge text-dark rounded-pill"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Imagen asociada</span>
                                <code id="det_nombre_imagen" class="text-dark"></code>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-3 p-3 bg-light rounded border">
                    <h6 class="text-muted mb-1">Descripción</h6>
                    <p id="det_descripcion" class="mb-0 text-dark" style="white-space: pre-wrap; word-break: break-word;">Sin descripción registrada.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetallesTelefono" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" style="color: black;">Especificaciones Técnicas</h5>
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

<style>
 body.dark-mode .modal #seccion_telefono_modificar,
body.dark-mode .modal [id*="seccion_telefono"] {
    background-color: #242424 !important;
    border-color: #383838 !important;
    color: #f8f9fa !important;
}

body.dark-mode .modal #seccion_telefono_modificar h6,
body.dark-mode .modal [id*="seccion_telefono"] h6 {
    color: #ffda6a !important;
}

body.dark-mode .modal-content {
    background-color: #1a1a1a !important;
    border: 1px solid #333 !important;
    color: #f8f9fa !important;
}

body.dark-mode .modal-header,
body.dark-mode .modal-footer {
    border-color: #333 !important;
}


body.dark-mode .modal-title {
    color: #f8f9fa !important;
}


body.dark-mode .modal .form-control,
body.dark-mode .modal .form-select,
body.dark-mode .modal select {
    background-color: #212529 !important;
    border-color: #495057 !important;
    color: #f8f9fa !important;
}

body.dark-mode .modal .form-control:focus {
    background-color: #212529 !important;
    border-color: #38bdf8 !important;
    color: #f8f9fa !important;
    box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25) !important;
}


body.dark-mode .modal [style*="background:#f8f9fa"],
body.dark-mode .modal [style*="background:#f0f4f8"],
body.dark-mode .modal [style*="background: #f8f9fa"],
body.dark-mode .modal .bg-light {
    background-color: #242424 !important;
    border-color: #383838 !important;
    color: #f8f9fa !important;
}

body.dark-mode .modal [style*="background:#fff3cd"] {
    background-color: #332701 !important;
    border-color: #664d03 !important;
    color: #ffda6a !important;
}

body.dark-mode .modal [style*="background:#fff3cd"] h6 {
    color: #ffda6a !important;
}


body.dark-mode .modal .list-group-item {
    background-color: #1a1a1a !important;
    border-color: #333 !important;
    color: #f8f9fa !important;
}


body.dark-mode .modal .text-muted,
body.dark-mode .modal span.text-muted {
    color: #9ca3af !important;
}

body.dark-mode .modal h4,
body.dark-mode .modal strong,
body.dark-mode .modal code {
    color: #f8f9fa !important;
}


body.dark-mode .modal .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
</style>