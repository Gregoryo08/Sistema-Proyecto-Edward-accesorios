<?php
// /src/app/controllers/reportarPago.php
// Controlador que carga la vista de reporte de pago

use App\Sistema\models\PedidoModel;
use App\Sistema\models\TasaCambioModel;
use App\Sistema\models\BancoReceptorModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['es_ecommerce']) || $_SESSION['es_ecommerce'] !== true) {
    header('Location: ?pagina=loginTienda');
    exit;
}

// ✅ Validar carrito
if (empty($_SESSION['carrito'])) {
    ?>
    <!DOCTYPE html><html><body>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Carrito vacío',
            text: 'No hay productos en el carrito. Agrega productos antes de reportar un pago.',
            confirmButtonText: 'Aceptar'
        }).then(() => { window.location.href = '?pagina=web_Catalogo'; });
    </script>
    </body></html>
    <?php
    exit;
}

$id_pedido = $_GET['pedido'] ?? 0;
$metodo = $_GET['metodo'] ?? 'transferencia';

$pedidoModel = new PedidoModel();
$pedido = $pedidoModel->obtenerPorIdYCliente($id_pedido, $_SESSION['cliente_cedula'] ?? '');
$total_usd = $pedido['total'] ?? 0;

$tasaModel = new TasaCambioModel();
$tasa = $tasaModel->obtener();
$total_bs = $tasaModel->convertir($total_usd);
$vigencia = $tasaModel->vigencia();
$fecha_tasa = $tasaModel->obtenerFechaActualizacion();

// ✅ BANCO RECEPTOR desde constantes (BancoReceptorModel), ya no está quemado como "Banesco"
$receptorModel = new BancoReceptorModel();
$banco_receptor = $receptorModel->obtenerNombre($metodo) ?? 'Banesco';

// ✅ RUTA CORREGIDA (nueva estructura)
require_once __DIR__ . '/../views/reportarPago.php';