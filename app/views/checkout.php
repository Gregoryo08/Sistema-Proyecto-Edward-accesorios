<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/ecommerce/temaBase.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="?pagina=web_Catalogo">Edward<span class="text-primary">Accesorios</span></a>
        <a href="?pagina=carrito" class="btn btn-outline-light"><i class="fas fa-arrow-left"></i> Volver al carrito</a>
    </div>
</nav>

<div class="container my-5">
    <h2>Finalizar compra</h2>
    <div id="resumen-carrito" class="alert alert-secondary"></div>

    <div class="row">
        <div class="col-md-6">
            <h4>Datos del cliente</h4>
            <input type="text" id="nombre" class="form-control mb-2" placeholder="Nombre completo" 
                   value="<?= htmlspecialchars($cliente_nombre ?? '') ?>" required>
            <input type="email" id="email" class="form-control mb-2" placeholder="Correo electrónico" 
                   value="<?= htmlspecialchars($cliente_correo ?? '') ?>" required>
            <input type="tel" id="telefono" class="form-control mb-2" placeholder="Teléfono" 
                   value="<?= htmlspecialchars($cliente_telefono ?? '') ?>" required>
            <input type="hidden" name="cedula_persona" id="cedula_persona" 
                   value="<?= htmlspecialchars($cedula_persona_checkout ?? '') ?>">
        </div>
        <div class="col-md-6">
            <h4>Forma de entrega</h4>
            <button id="btn-retiro" class="btn btn-outline-primary w-100 mb-2">📦 Retiro Personal (Gratis)</button>
            <button id="btn-delivery" class="btn btn-outline-primary w-100">🚚 Delivery ($10)</button>
            <div id="delivery-form" style="display:none;" class="mt-3">
                <input type="text" id="direccion" class="form-control mb-2" placeholder="Dirección completa">
            </div>
        </div>
    </div>

    <!-- Método de pago -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h4>Método de pago</h4>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="metodo_pago" id="metodo_transferencia" value="transferencia" checked>
                <label class="form-check-label" for="metodo_transferencia">Transferencia Bancaria</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="metodo_pago" id="metodo_pago_movil" value="pago_movil">
                <label class="form-check-label" for="metodo_pago_movil">Pago Móvil</label>
            </div>
        </div>
    </div>

    <div class="mt-4 p-3 bg-light rounded">
        <h4>Total a pagar: <span id="total-final" class="text-success">$0.00</span></h4>
        <button id="btn-confirmar" class="btn btn-success btn-lg mt-2">✅ Confirmar compra</button>
    </div>
</div>

<!--  SOLO INCLUIR JS EXTERNOS -->
<script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>
<script src="assets/js/ecommerce/validaciones.js"></script>
<script src="assets/js/ecommerce/checkout.js"></script>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="fas fa-sun" data-icon-claro="fas fa-moon">
    <i class="fas fa-sun" id="themeIcon"></i>
</button>
<script src="assets/js/ecommerce/temaBoton.js"></script>

</body>
</html>