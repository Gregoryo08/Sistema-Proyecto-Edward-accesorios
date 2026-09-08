<?php
header('Content-Type: application/json');

use App\Sistema\models\PagoOnlineModel;
use App\Sistema\models\EnvioModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cedula = $_SESSION['username'] ?? null;
if (!$cedula) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $pagoModel = new PagoOnlineModel();
    $pagos_pendientes = $pagoModel->ejecutar('obtener_pendientes');
    if (isset($pagos_pendientes['error'])) $pagos_pendientes = [];

    $pagos_aprobados = $pagoModel->ejecutar('obtener_aprobados_sin_despacho');
    if (isset($pagos_aprobados['error'])) $pagos_aprobados = [];

    $html_pendientes = '';
    if (empty($pagos_pendientes)) {
        $html_pendientes = '<div class="alert alert-info text-center">No hay pagos pendientes por verificar.</div>';
    } else {
        foreach ($pagos_pendientes as $p) {
            $nombre = htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''));
            $totalPedido = number_format($p['total'] ?? 0, 2);
            $monto = number_format($p['monto_reportado'], 2);
            $banco = htmlspecialchars($p['banco_emisor'] ?? '');
            $ref = htmlspecialchars($p['referencia'] ?? '');
            $telefono = htmlspecialchars($p['telefono'] ?? 'N/A');
            $html_pendientes .= '
                <div class="card mb-2 card-pendiente" id="pendiente-' . $p['id_reporte'] . '">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Pedido #' . $p['id_pedido'] . '</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-user"></i> ' . $nombre . '<br>
                                    <i class="fas fa-phone"></i> ' . $telefono . '
                                </p>
                                <p class="mb-1 small"><strong>Total Pedido:</strong> $' . $totalPedido . '</p>
                                <p class="mb-1 small text-muted"><strong>Monto Reportado:</strong> Bs. ' . $monto . '</p>
                                <p class="mb-1 small"><strong>Referencia:</strong> ' . $ref . '</p>
                                <p class="mb-0 small"><strong>Banco:</strong> ' . $banco . '</p>
                            </div>
                            <div>
                                <button class="btn btn-success btn-sm mb-1" onclick="aprobarPago(' . $p['id_reporte'] . ', ' . $p['id_pedido'] . ')"><i class="fas fa-check"></i> Aprobar</button>
                                <button class="btn btn-danger btn-sm" onclick="rechazarPago(' . $p['id_reporte'] . ', ' . $p['id_pedido'] . ')"><i class="fas fa-times"></i> Rechazar</button>
                            </div>
                        </div>
                    </div>
                </div>';
        }
    }

    $html_aprobados = '';
    if (empty($pagos_aprobados)) {
        $html_aprobados = '<div class="alert alert-info text-center">No hay pagos aprobados pendientes de despacho.</div>';
    } else {
        foreach ($pagos_aprobados as $p) {
            $nombre = htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''));
            $telefono = htmlspecialchars($p['telefono'] ?? 'N/A');
            $direccion = htmlspecialchars($p['direccion_entrega'] ?? 'No especificada');
            $total = number_format($p['total'] ?? 0, 2);
            $cedula_persona = htmlspecialchars($p['cedula_persona'] ?? '');
            $nombre_escaped = htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''));
            $html_aprobados .= '
                <div class="card mb-2 card-aprobado" id="aprobado-' . $p['id_reporte'] . '">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Pedido #' . $p['id_pedido'] . '</h6>
                                <p class="mb-1 text-muted small">
                                    <i class="fas fa-user"></i> ' . $nombre . '<br>
                                    <i class="fas fa-phone"></i> ' . $telefono . '
                                </p>
                                <p class="mb-1 small"><strong>Dirección:</strong> ' . $direccion . '</p>
                                <p class="mb-1 small"><strong>Total:</strong> $' . $total . '</p>
                            </div>
                            <div>
                                <button class="btn btn-primary btn-sm" 
                                        onclick="mostrarFormularioDespacho(
                                            ' . $p['id_reporte'] . ', 
                                            ' . $p['id_pedido'] . ', 
                                            \'' . addslashes($cedula_persona) . '\', 
                                            \'' . addslashes($nombre_escaped) . '\', 
                                            \'' . addslashes($telefono) . '\', 
                                            \'' . addslashes(htmlspecialchars($p['direccion_entrega'] ?? '')) . '\'
                                        )">
                                    <i class="fas fa-truck"></i> Asignar Despacho
                                </button>
                            </div>
                        </div>
                    </div>
                </div>';
        }
    }

    echo json_encode([
        'success' => true,
        'html_pendientes' => $html_pendientes,
        'html_aprobados' => $html_aprobados
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}