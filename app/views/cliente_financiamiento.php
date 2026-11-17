<?php
require_once('assets/comunes/menu_cliente.php');
require_once('assets/comunes/modal_pagocuotas.php');
?>

<main class="main mt-4" id="main">
    <section id="hero" class="hero section" style="height: auto;">
        <div class="container">
            <h2 class="text-center mb-4">Mis Financiamientos</h2>

            <div class="table-responsive">
                <div class="table-container">
                    <table id="tabla_mis_financiamientos" class="table w-100">
                        <thead style="background: transparent !important;">
                            <tr>
                                <th>Producto</th>
                                <th>Monto Total</th>
                                <th>Saldo Pendiente</th>
                                <th>Próximo Pago</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once('assets/comunes/footer.php'); ?>






<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="assets/js/validaciones/financiamiento/cliente_financiamiento.js"></script>
</body>
</html>