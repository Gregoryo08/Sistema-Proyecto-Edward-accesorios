<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cedula = $_SESSION['username'] ?? null;
session_write_close();
if (!$cedula) {
    echo json_encode(['success' => false]);
    exit;
}

require_once __DIR__ . '/../config/Conexion.php';
use App\Sistema\config\Conexion;
use App\Sistema\models\Usuarios;

$obj_usuario = new Usuarios();
if (!$obj_usuario->tienePermiso('Administrar Ventas Online', 'listar')) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

try {
    $conn = Conexion::getShared('sistema_edward')->getConexion();

    $tipo = $_GET['tipo'] ?? 'resumen';

    switch ($tipo) {
        case 'top_clientes':
            $stmt = $conn->query("
                SELECT CONCAT(per.nombre, ' ', per.apellido) as cliente, SUM(p.total) as total
                FROM pedidos p
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                GROUP BY p.cedula_persona
                ORDER BY total DESC
                LIMIT 5
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categorias = [];
            $valores = [];
            foreach ($rows as $r) {
                $categorias[] = $r['cliente'];
                $valores[] = (float)$r['total'];
            }
            echo json_encode(['success' => true, 'categorias' => $categorias, 'valores' => $valores]);
            break;

        case 'top_productos':
            $stmt = $conn->query("
                SELECT pr.nombre_producto as producto, SUM(dp.cantidad) as total
                FROM detalle_pedido dp
                JOIN productos pr ON dp.id_producto = pr.id_producto
                GROUP BY dp.id_producto
                ORDER BY total DESC
                LIMIT 5
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categorias = [];
            $valores = [];
            foreach ($rows as $r) {
                $categorias[] = $r['producto'];
                $valores[] = (int)$r['total'];
            }
            echo json_encode(['success' => true, 'categorias' => $categorias, 'valores' => $valores]);
            break;

        case 'ventas_diarias':
            $stmt = $conn->query("
                SELECT DATE(p.fecha) as dia, SUM(p.total) as total
                FROM pedidos p
                WHERE p.estado = 'pagado'
                AND p.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY DATE(p.fecha)
                ORDER BY dia ASC
                LIMIT 7
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categorias = [];
            $valores = [];
            foreach ($rows as $r) {
                $categorias[] = $r['dia'];
                $valores[] = (float)$r['total'];
            }
            echo json_encode(['success' => true, 'categorias' => $categorias, 'valores' => $valores]);
            break;

        case 'ventas_mensuales':
            $stmt = $conn->query("
                SELECT WEEK(p.fecha) - WEEK(DATE_SUB(p.fecha, INTERVAL DAYOFMONTH(p.fecha)-1 DAY)) + 1 as semana, SUM(p.total) as total
                FROM pedidos p
                WHERE p.estado = 'pagado'
                AND MONTH(p.fecha) = MONTH(CURDATE())
                AND YEAR(p.fecha) = YEAR(CURDATE())
                GROUP BY semana
                ORDER BY semana ASC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categorias = [];
            $valores = [];
            foreach ($rows as $r) {
                $categorias[] = 'Sem ' . $r['semana'];
                $valores[] = (float)$r['total'];
            }
            echo json_encode(['success' => true, 'categorias' => $categorias, 'valores' => $valores]);
            break;

        case 'ventas_no_concretadas':
            $stmt = $conn->query("
                SELECT estado_verificacion, COUNT(*) as total
                FROM pago_online
                WHERE estado_verificacion IN ('rechazado', 'pendiente')
                GROUP BY estado_verificacion
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $datos = [];
            foreach ($rows as $r) {
                $label = $r['estado_verificacion'] === 'rechazado' ? 'Pago rechazado' : 'No verificado';
                $datos[] = ['name' => $label, 'y' => (int)$r['total']];
            }
            echo json_encode(['success' => true, 'datos' => $datos]);
            break;

        case 'metodos_pago':
            $stmt = $conn->query("
                SELECT metodo_pago, COUNT(*) as total
                FROM pedidos
                WHERE metodo_pago IS NOT NULL
                GROUP BY metodo_pago
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $datos = [];
            foreach ($rows as $r) {
                $datos[] = ['name' => ucfirst($r['metodo_pago']), 'y' => (int)$r['total']];
            }
            echo json_encode(['success' => true, 'datos' => $datos]);
            break;

        case 'despachos_mensuales':
            $stmt = $conn->query("
                SELECT WEEK(fecha_despacho) - WEEK(DATE_SUB(fecha_despacho, INTERVAL DAYOFMONTH(fecha_despacho)-1 DAY)) + 1 as semana, COUNT(*) as total
                FROM despachos
                WHERE MONTH(fecha_despacho) = MONTH(CURDATE())
                AND YEAR(fecha_despacho) = YEAR(CURDATE())
                GROUP BY semana
                ORDER BY semana ASC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $categorias = [];
            $valores = [];
            foreach ($rows as $r) {
                $categorias[] = 'Sem ' . $r['semana'];
                $valores[] = (int)$r['total'];
            }
            echo json_encode(['success' => true, 'categorias' => $categorias, 'valores' => $valores]);
            break;

        default:
            $stmt = $conn->query("SELECT COUNT(*) as total FROM pago_online WHERE estado_verificacion = 'pendiente'");
            $pendientes = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $conn->query("SELECT COUNT(*) as total FROM pago_online WHERE estado_verificacion = 'aprobado'");
            $aprobados = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $conn->query("SELECT COUNT(*) as total FROM despachos WHERE estado_despacho IN ('asignado', 'en_ruta')");
            $en_ruta = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $conn->query("SELECT COUNT(*) as total FROM pedidos");
            $total_pedidos = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $conn->prepare("
                SELECT po.*, p.total, p.fecha, p.direccion_entrega,
                       per.nombre, per.apellido, per.telefono, per.correo
                FROM pago_online po
                JOIN pedidos p ON po.id_pedido = p.id_pedido
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                WHERE po.estado_verificacion = 'pendiente'
                ORDER BY po.fecha_reporte ASC
            ");
            $stmt->execute();
            $pagos_pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $conn->prepare("
                SELECT po.*, p.total, p.direccion_entrega,
                       per.nombre, per.apellido, per.telefono, per.correo
                FROM pago_online po
                JOIN pedidos p ON po.id_pedido = p.id_pedido
                JOIN persona per ON p.cedula_persona = per.cedula_persona
                WHERE po.estado_verificacion = 'aprobado'
                AND NOT EXISTS (SELECT 1 FROM despachos d WHERE d.id_pedido = p.id_pedido)
                ORDER BY po.fecha_verificacion ASC
            ");
            $stmt->execute();
            $pagos_aprobados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $html_pendientes = '';
            if (empty($pagos_pendientes)) {
                $html_pendientes = '<div class="alert alert-info text-center">No hay pagos pendientes por verificar.</div>';
            } else {
                foreach ($pagos_pendientes as $p) {
                    $nombre = htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $totalPedido = number_format($p['total'] ?? 0, 2);
                    $monto = number_format($p['monto_reportado'], 2);
                    $banco = htmlspecialchars($p['banco_emisor'] ?? '', ENT_QUOTES, 'UTF-8');
                    $ref = htmlspecialchars($p['referencia'] ?? '', ENT_QUOTES, 'UTF-8');
                    $telefono = htmlspecialchars($p['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
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
                    $nombre = htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $telefono = htmlspecialchars($p['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                    $direccion = htmlspecialchars($p['direccion_entrega'] ?? 'No especificada', ENT_QUOTES, 'UTF-8');
                    $total = number_format($p['total'] ?? 0, 2);
                    $cedula_persona = htmlspecialchars($p['cedula_persona'] ?? '', ENT_QUOTES, 'UTF-8');
                    $nombre_escaped = htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellido'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $direccion_escaped = htmlspecialchars($p['direccion_entrega'] ?? '', ENT_QUOTES, 'UTF-8');
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
                                                    \'' . $cedula_persona . '\', 
                                                    \'' . $nombre_escaped . '\', 
                                                    \'' . $telefono . '\', 
                                                    \'' . $direccion_escaped . '\'
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
                'pendientes' => $pendientes['total'],
                'aprobados' => $aprobados['total'],
                'en_ruta' => $en_ruta['total'],
                'total_pedidos' => $total_pedidos['total'],
                'html_pendientes' => $html_pendientes,
                'html_aprobados' => $html_aprobados
            ]);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
