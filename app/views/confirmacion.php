<?php
require_once('assets/comunes/modal_confirmacion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Reportado | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/ecommerce/confirmacion.css">
    <link rel="stylesheet" href="assets/css/ecommerce/temaBase.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow">
                <div class="card-body py-5">
                    <i class="fas fa-check-circle text-success check-icon"></i>
                    <h2 class="mt-4">¡Pago reportado con éxito!</h2>
                    <p class="lead text-muted">Tu pago está siendo verificado por nuestro equipo.</p>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-bell"></i> Recibirás una notificación cuando tu pago sea confirmado y tu pedido esté listo para despacho.
                    </div>
                    <div class="alert alert-secondary small">
                        <i class="fas fa-dollar-sign"></i> Tasa de cambio: Bs. <?= number_format($tasa, 2) ?> / USD
                        <br><small class="text-muted">Actualizada: <?= $tasa_fecha ?></small>
                        <?php if ($vigencia['expirada']): ?>
                            <br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?= $vigencia['mensaje'] ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted small">Si tienes dudas, contáctanos por WhatsApp</p>
                    <div class="d-grid gap-2">
                        <a href="?pagina=web_Catalogo" class="btn btn-primary btn-lg">
                            <i class="fas fa-store"></i> Seguir comprando
                        </a>
                        <a href="?pagina=misPedidos" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> Ver mis pedidos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/ecommerce/tasaCambio.js"></script>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="fas fa-sun" data-icon-claro="fas fa-moon">
    <i class="fas fa-sun" id="themeIcon"></i>
</button>
<script src="assets/js/ecommerce/temaBoton.js"></script>
</body>
</html>
