<?php
$id_pedido = $id_pedido ?? 0;
$metodo = $metodo ?? 'transferencia';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportar Pago | Edward Accesorios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/ecommerce/reportarPago.css">
    <link rel="stylesheet" href="assets/css/ecommerce/temaBase.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="?pagina=web_Catalogo">Edward<span class="text-primary">Accesorios</span></a>
        <span class="text-white"><i class="fas fa-check-circle"></i> Reportar Pago</span>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> Reporta tu pago paso a paso</h4>
                </div>
                <div class="card-body">
                    <!-- ✅ SOLO HTML - NADA DE JAVASCRIPT AQUÍ -->
                    <form id="form-reporte" autocomplete="off">
                        <input type="hidden" name="id_pedido" value="<?= htmlspecialchars($id_pedido) ?>">
                        <input type="hidden" name="metodo" value="<?= htmlspecialchars($metodo) ?>">
                        <input type="hidden" name="banco_receptor" value="<?= htmlspecialchars($banco_receptor ?? 'Banesco') ?>">
                        <input type="hidden" name="total_usd" id="total-usd" value="<?= htmlspecialchars($total_usd) ?>">
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-info-circle"></i>
                            Monto a pagar: <strong>$<?= number_format($total_usd, 2) ?> USD</strong>
                            → <strong>Bs. <?= number_format($total_bs, 2) ?></strong>
                            <br>Tasa: <span class="tasa-valor"><?= number_format($tasa, 2) ?></span> Bs./USD
                            <span class="tasa-fecha ms-2"><?= $fecha_tasa ?></span>
                            <?php if ($vigencia['expirada']): ?>
                                <span class="text-danger ms-2"><i class="fas fa-exclamation-triangle"></i> <?= $vigencia['mensaje'] ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- PASO 1 -->
                        <div id="paso1" class="paso">
                            <h5 class="text-primary">Paso 1 de 4</h5>
                            <label class="form-label fw-bold">📋 Referencia bancaria</label>
                            <input type="text" class="form-control form-control-lg" name="referencia" 
                                   placeholder="Ej: 123456789012" required maxlength="13" autocomplete="off">
                            <div id="msg-ref" class="text-danger small" style="display:none;"></div>
                            <small class="text-muted">Número de confirmación de la transferencia (12-13 dígitos)</small>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="validarPaso1()">
                                    Confirmar Referencia <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- PASO 2 -->
                        <div id="paso2" class="paso" style="display:none;">
                            <h5 class="text-primary">Paso 2 de 4</h5>
                            <label class="form-label fw-bold">🏦 Banco emisor</label>
                            <select class="form-select form-select-lg" name="banco_emisor" required>
                                <option value="">Seleccione su banco</option>
                            </select>
                            <div id="msg-banco" class="text-danger small" style="display:none;"></div>
                            <small class="text-muted">Banco desde donde realizaste la transferencia</small>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="validarPaso2()">
                                    Confirmar Banco <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- PASO 3 -->
                        <div id="paso3" class="paso" style="display:none;">
                            <h5 class="text-primary">Paso 3 de 4</h5>
                            <label class="form-label fw-bold">📱 Teléfono desde donde transfirió</label>
                            <input type="tel" class="form-control form-control-lg" name="telefono" 
                                   placeholder="Ej: 04121234567" required maxlength="15" autocomplete="off">
                            <div id="msg-telf" class="text-danger small" style="display:none;"></div>
                            <small class="text-muted">Número de teléfono asociado a la cuenta bancaria (mín. 10 dígitos)</small>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="validarPaso3()">
                                    Confirmar Teléfono <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- PASO 4 -->
                        <div id="paso4" class="paso" style="display:none;">
                            <h5 class="text-primary">Paso 4 de 4</h5>
                            <label class="form-label fw-bold">💵 Monto transferido</label>
                            <input type="text" class="form-control form-control-lg" name="monto" 
                                   placeholder="Ej: 1340.00" required autocomplete="off"
                                   oninput="this.value = this.value.replace(/[^0-9.,]/g, '')">
                            <div id="msg-monto" class="text-danger small" style="display:none;"></div>
                            <small class="text-muted">Monto exacto que transferiste (Ej: 1340.00)</small>
                            <div class="mt-2 p-2 rounded bg-light border d-flex justify-content-between align-items-center">
                                <span class="small text-muted"><i class="fas fa-dollar-sign"></i> Equivalente en USD:</span>
                                <strong class="text-success" id="monto-usd-equiv">$0.00 USD</strong>
                            </div>
                            <div id="msg-monto-usd" class="text-danger small fw-bold mt-1" style="display:none;">
                                <i class="fas fa-exclamation-triangle"></i> Le falta dinero para cubrir el total del pedido
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="mostrarResumen()">
                                    Revisar Todo <i class="fas fa-clipboard-check"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- RESUMEN -->
                        <div id="resumen" style="display:none;">
                            <h5 class="text-success mb-3">✅ Confirma los datos de tu pago</h5>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><strong>📋 Referencia:</strong> <span id="r_ref" class="text-primary"></span></li>
                                        <li class="mb-2"><strong>🏦 Banco emisor:</strong> <span id="r_banco" class="text-primary"></span></li>
                                        <li class="mb-2"><strong>📱 Teléfono:</strong> <span id="r_telf" class="text-primary"></span></li>
                                        <li class="mb-2"><strong>💵 Monto:</strong> <span id="r_monto" class="text-primary"></span></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">👤 Nombre del pagador</label>
                                <input type="text" class="form-control" name="nombre_pagador" 
                                       value="<?= htmlspecialchars($_SESSION['cliente_nombre'] ?? '') ?>"
                                       placeholder="Tu nombre completo" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">🪪 Cédula del pagador</label>
                                <div class="input-group">
                                    <select name="tipo_cedula_pagador" class="form-select" style="max-width: 70px;">
                                        <option value="V">V-</option>
                                        <option value="E">E-</option>
                                        <option value="J">J-</option>
                                        <option value="G">G-</option>
                                    </select>
                                    <input type="text" class="form-control" name="cedula_pagador" 
                                           value="<?= htmlspecialchars(preg_replace('/^[A-Z]-/', '', $_SESSION['cliente_cedula'] ?? '')) ?>"
                                           placeholder="12345678" autocomplete="off" maxlength="8"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                                <div id="msg-cedula-page" class="text-danger small" style="display:none;"></div>
                            </div>
                            
                            <button type="button" class="btn btn-success btn-lg w-100" id="btn-enviar" onclick="enviarReporte()">
                                <i class="fas fa-check-circle"></i> CONFIRMAR TODO Y ENVIAR
                            </button>
                            <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="siguientePaso(1)">
                                <i class="fas fa-edit"></i> Corregir datos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  INCLUIR ARCHIVOS JS EXTERNOS -->

<!--  PRIMERO: jQuery y SweetAlert -->
<script src="assets/Library/JQuery/jquery-3.7.0.min.js"></script>
<script src="assets/Library/SweetAlerts/sweetalert2.all.js"></script>

<!-- SEGUNDO: validaciones.js (TODAS las funciones) -->
<script src="assets/js/ecommerce/validaciones.js"></script>

<!--  TERCERO: reporte_pagos_online.js (ESPECÍFICO) -->
<script src="assets/js/ecommerce/reporte_pagos_online.js"></script>

<!--  CUARTO: tasaCambio.js (actualización dinámica de tasa) -->
<script src="assets/js/ecommerce/tasaCambio.js"></script>

<button id="themeToggle" class="theme-toggle-btn" title="Cambiar tema" data-icon-oscuro="fas fa-sun" data-icon-claro="fas fa-moon">
    <i class="fas fa-sun" id="themeIcon"></i>
</button>
<script src="assets/js/ecommerce/temaBoton.js"></script>






</body>
</html>