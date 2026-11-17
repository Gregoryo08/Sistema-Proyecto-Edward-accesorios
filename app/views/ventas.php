<?php require_once('assets/comunes/menu.php'); ?>
<main class="m-4" style="padding-top: 60px;">
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-3">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar producto...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 justify-content-md-end">
                            <label for="categoriesContainer" class="text-muted small fw-bold text-nowrap mb-0">Categorías:</label>
                            <select class="form-select form-select-sm w-auto" id="categoriesContainer" style="min-width: 240px;">
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive rounded-3 border" style="max-height: 550px; overflow-y: auto;">
                <table class="table table-striped table-bordered text-center w-100 mb-0">
                    <thead class="table-dark sticky-top" style="z-index: 1;">
                        <tr>
                            <th style="width: 30%;" class="text-center align-middle">Nombre del Producto</th>
                            <th style="width: 15%;" class="text-center align-middle">Categoría</th>
                            <th style="width: 15%;" class="text-center align-middle">Ref. USD</th>
                            <th style="width: 15%;" class="text-center align-middle">Ref. Bs</th>
                            <th style="width: 15%;" class="text-center align-middle">Stock</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductos">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Agregado sticky-top para mantener visible el ticket en pantalla -->
        <div class="col-lg-5 col-xl-4 sticky-top" style="top: 80px; z-index: 10;">
            <div class="cart-container p-4 shadow-sm rounded-3 bg-white">
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3">
                    <div class="d-flex align-items-center">
                        <span class="position-relative me-3">
                            <i class="fa-solid fa-cart-shopping fa-xl text-primary"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCountBadge">0</span>
                        </span>
                        <h5 class="mb-0 fw-bold" style="font-size: 1.1rem;">Ticket de Venta</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-2 border-0 shadow-sm btn-icon-client"
                            onclick="openClientModal()" title="Seleccionar Cliente">
                            <i class="fa-solid fa-user-plus"></i>
                        </button>
                        <button class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-2 border-0 shadow-sm btn-icon-trash"
                            onclick="clearCart()" title="Vaciar Carrito">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                <div id="selectedClientContainer" class="mb-3 p-2 bg-light rounded border d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small">
                            <span class="text-muted">Cliente: <strong id="selectedClientName" class="text-dark"></strong></span>
                            <span class="text-muted ms-2">C.I: <strong id="selectedClientCed" class="text-dark"></strong></span>
                        </div>
                        <button type="button" class="btn-close small" style="font-size: 0.75rem;" onclick="removeSelectedClient()" title="Quitar cliente"></button>
                    </div>
                </div>

                <div class="cart-items pe-2" id="cartItemsList" style="height: 320px; max-height: 320px; overflow-y: auto; overflow-x: hidden;">
                    <div class="text-center text-muted h-100 d-flex flex-column justify-content-center align-items-center" id="emptyCartMessage">
                        <i class="fa-solid fa-basket-shopping fa-3x mb-3 text-black-50"></i>
                        <p class="mb-0 fw-medium">El carrito está vacío</p>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Subtotal:</span>
                        <span class="fw-medium small" id="subtotalVal">0.00 Bs</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">IVA (16%):</span>
                        <span class="fw-medium small" id="ivaVal">0.00 Bs</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="mb-0 fw-bold text-dark h4">Total:</span>
                        <div class="text-end">
                            <h3 class="mb-0 fw-extrabold text-dark" id="totalMainVal" style="font-size: 1.75rem;">0.00 Bs</h3>
                        </div>
                    </div>
                    <button class="btn btn-success btn-lg w-100 py-3 rounded-3 fw-bold shadow-sm" id="btnPagar" onclick="openPaymentModal()" disabled>
                        <i class="fa-solid fa-cash-register"></i> REALIZAR VENTA
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once('assets/comunes/modalPagos.php'); ?>
<?php require_once('assets/comunes/modalBuscarCliente.php'); ?>

<script src="assets/js/validaciones/ventas/ventas.js"></script>
<?php require_once('assets/comunes/footer.php'); ?>