<!-- ============================================ -->
<!-- MODAL CARRITO DE COMPRAS (HTML puro)         -->
<!-- Los estilos: assets/css/ecommerce/carritoModal.css -->
<!-- La lógica:   assets/js/ecommerce/carritoModal.js  -->
<!-- ============================================ -->
<div class="cart-modal-overlay" id="cartModalOverlay">
    <div class="cart-modal" role="dialog" aria-modal="true" aria-labelledby="cartModalTitle">

        <!-- Encabezado del Modal -->
        <div class="cart-modal-header">
            <h3 id="cartModalTitle" class="cart-modal-title">
                <i class="fas fa-shopping-cart"></i> Mi Carrito
            </h3>
            <button type="button" class="cart-modal-close" id="closeCartBtn" aria-label="Cerrar carrito">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cuerpo del Modal (Listado de Productos) -->
        <div class="cart-modal-body" id="cartItemsContainer">
            <!-- Se llena dinámicamente mediante JavaScript -->
        </div>

        <!-- Pie del Modal (Resumen y Botones) -->
        <div class="cart-modal-footer">
            <div class="cart-total-row">
                <span>Total a pagar:</span>
                <span class="cart-total-price" id="cartTotalPrice">$0.00</span>
            </div>

            <div class="cart-modal-actions">
                <button type="button" class="btn-cart-secondary" id="btnClearCart">
                    <i class="fas fa-trash-alt"></i> Vaciar
                </button>
                <a href="?pagina=carrito" class="btn-cart-primary" id="btnCheckout">
                    <i class="fas fa-check-circle"></i> Procesar Compra
                </a>
            </div>
        </div>

    </div>
</div>
