<?php require_once('assets/comunes/menu.php'); ?>
<?php $rol_ia = $_SESSION['rol'] ?? 0; ?>
<script>window.IA_ROL = <?php echo json_encode((int)$rol_ia); ?>;</script>

<?php if (!defined('IA_SERVICIO_URL')) { require_once 'app/config/ia.php'; } ?>

<div class="ia-visor">

    <!-- ============================================================ cabecera -->
    <div class="container py-4">

        <div class="ia-cabecera mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="me-auto">
                    <span class="ia-codigo" id="ia_codigo">RCI-01</span>
                    <h3 class="fw-black mb-1 mt-2">
                        Cómo el sistema decide una recomendación
                        <span class="ia-jerga" title="Razonamiento difuso por el método de Mamdani: fuzzificación, motor de inferencia y defuzzificación.">Razonamiento difuso</span>
                    </h3>
                    <p class="mb-0 opacity-75" style="font-size: .88rem;">
                        Evalúa a un cliente que pide un teléfono a crédito. Mueve los controles y observa
                        cómo el sistema interpreta los datos, aplica las reglas del negocio y llega a un número final.
                    </p>
                </div>
                <div class="ia-estado" id="ia_estado">
                    <span class="ia-punto"></span>
                    <span id="ia_estado_texto">Conectando con el microservicio...</span>
                </div>
            </div>
        </div>

        <!-- =========================================================== controles -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-7">
                <div class="ia-tarjeta">
                    <div class="ia-etapa-titulo">
                        <span class="ia-paso">1</span>
                        <div>
                            <h3>Datos que evalúa el sistema
                                <span class="ia-jerga" title="Variables lingüísticas de entrada del sistema difuso.">Variables de entrada</span>
                            </h3>
                            <p>Lo que el sistema toma en cuenta para decidir</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="ia-control-fila">
                                <label class="ia-control-label" for="ia_ingreso">Ingresos mensuales</label>
                                <span class="ia-control-valor" id="ia_ingreso_valor">450</span>
                            </div>
                            <input type="range" class="ia-range" id="ia_ingreso"
                                   min="0" max="1000" step="5" value="450">
                            <div class="d-flex justify-content-between ia-cifra-label">
                                <span>$ 0</span><span>$ 1000</span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="ia-control-fila">
                                <label class="ia-control-label" for="ia_historial">Historial crediticio</label>
                                <span class="ia-control-valor" id="ia_historial_valor">60</span>
                            </div>
                            <input type="range" class="ia-range" id="ia_historial"
                                   min="0" max="100" step="1" value="60">
                            <div class="d-flex justify-content-between ia-cifra-label">
                                <span>0 pts</span><span>100 pts</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3 opacity-25">

                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-5">
                            <label class="ia-control-label" for="ia_cedula">Cedula del prospecto</label>
                            <input type="text" class="ia-input mt-1" id="ia_cedula"
                                   placeholder="Ej: 1712345678" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="button" class="ia-btn ia-btn-acento w-100" id="ia_cargar">
                                <i class="bi bi-person-bounding-box"></i> Evaluar cedula
                            </button>
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="button" class="ia-btn w-100" id="ia_recorrer">
                                <i class="bi bi-play-fill"></i> Recorrido automatico
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- resultado -->
            <div class="col-12 col-lg-5">
                <div class="ia-tarjeta h-100">
                    <div class="ia-etapa-titulo">
                        <span class="ia-paso"><i class="bi bi-bullseye"></i></span>
                        <div>
                            <h3>Recomendación final
                                <span class="ia-jerga" title="Salida defuzzificada por el método del centroide.">Defuzzificación</span>
                            </h3>
                            <p>El número y el nivel que entrega el sistema</p>
                        </div>
                    </div>

                    <div class="row text-center g-2">
                        <div class="col-4">
                            <div class="ia-cifra-label">Confianza</div>
                            <div class="ia-cifra" id="ia_puntaje">0%</div>
                        </div>
                        <div class="col-4">
                            <div class="ia-cifra-label">Nivel</div>
                            <div class="ia-cifra" id="ia_nivel" style="font-size:1.2rem;padding-top:.45rem;">---</div>
                        </div>
                        <div class="col-4">
                            <div class="ia-cifra-label">Cuotas</div>
                            <div class="ia-cifra" id="ia_cuotas">0</div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <span class="ia-veredicto ia-si" id="ia_veredicto">Credito recomendado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== aviso -->
        <div class="ia-aviso oculto mb-4" id="ia_aviso"></div>

        <!-- ================================================== variables linguisticas -->
        <div class="ia-tarjeta mb-4">
            <div class="ia-etapa-titulo">
                <span class="ia-paso">A</span>
                <div>
                    <h3>En qué categorías cae cada dato
                        <span class="ia-jerga" title="Conjuntos difusos: cada variable se describe con valores lingüísticos (palabras) en lugar de números.">Conjuntos difusos</span>
                    </h3>
                    <p>
                        El sistema describe cada dato con palabras del lenguaje humano (bajo, regular, alto...).
                        La barra de cada categoría muestra qué tanto encaja el dato actual con esa palabra.
                    </p>
                </div>
            </div>

            <div class="row g-3" id="ia_variables"></div>
        </div>

        <!-- ======================================================= fuzzificacion -->
        <div class="mb-4">
            <div class="ia-tarjeta mb-3">
                <div class="ia-etapa-titulo">
                    <span class="ia-paso">2</span>
                    <div>
                        <h3>Cómo interpreta los datos
                            <span class="ia-jerga" title="Fuzzificación: convierte el valor exacto de entrada en grados de pertenencia (μ) entre 0 y 1.">Fuzzificación</span>
                        </h3>
                        <p>
                            El dato exacto se traduce en qué tanto encaja con cada categoría de la variable,
                            en una escala de 0 (nada) a 1 (totalmente).
                        </p>
                    </div>
                </div>
                <div class="ia-nota-conectivo">
                    <strong>Cómo se lee la gráfica:</strong> la línea vertical marca el valor de entrada;
                    la altura de cada curva en ese punto indica qué tanto encaja el dato con esa categoría.
                    Un valor de 0.80 significa que el dato es un 80% <em>medio</em> y un 20% <em>regular</em>.
                </div>
            </div>

            <div class="row g-3" id="ia_grafica_fuzzificacion"></div>
        </div>

        <!-- ==================================================== motor de inferencia -->
        <div class="ia-tarjeta mb-4">
            <div class="ia-etapa-titulo">
                <span class="ia-paso">3</span>
                <div>
                    <h3>Cómo aplica las reglas del negocio
                        <span class="ia-jerga" title="Motor de inferencia difusa: combina los grados de pertenencia según los conectivos AND / OR.">Motor de inferencia</span>
                    </h3>
                    <p id="ia_resumen_inferencia">Reglas activadas y qué tanto se cumplen</p>
                </div>
            </div>

            <div class="ia-nota-conectivo mb-3">
                <strong>Cómo combina las condiciones:</strong>
                <span class="ia-conectivo ia-and ms-1">∧</span> <strong>Y (mínimo)</strong> exige que <em>todas</em>
                las condiciones se cumplan: se toma la más baja.
                <span class="ia-conectivo ia-or ms-2">∨</span> <strong>O (máximo)</strong> basta con que se cumpla
                <em>al menos una</em>: se toma la más alta.
                <span class="ia-jerga ms-1" title="Conectivos lógicos difusos: AND = min (t-norma), OR = max (t-conorma).">AND (min) / OR (máx)</span>
            </div>

            <div id="ia_reglas"></div>
        </div>

        <!-- ======================================================= defuzzificacion -->
        <div class="mb-4">
            <div class="ia-tarjeta mb-3">
                <div class="ia-etapa-titulo">
                    <span class="ia-paso">4</span>
                    <div>
                        <h3>Cómo llega al número final
                            <span class="ia-jerga" title="Defuzzificación por el método del centroide (Mamdani).">Defuzzificación</span>
                        </h3>
                        <p>
                            El sistema combina las reglas que se cumplieron y las resume en un solo número.
                        </p>
                    </div>
                </div>
                <div class="ia-nota-conectivo">
                    <strong>Cómo se lee la gráfica:</strong> la línea discontinua es la curva base de la categoría;
                    la zona rellena es esa curva ajustada por qué tanto se cumplieron las reglas; la línea vertical
                    marca el número final
                    <span class="ia-jerga" title="Centroide: centro de masa del área agregada (método de Mamdani).">centroide</span>.
                </div>
            </div>

            <div class="row g-3" id="ia_grafica_defuzzificacion"></div>
        </div>

    </div>
</div>

<?php require_once('assets/comunes/footer.php'); ?>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
</a>

<link rel="stylesheet" href="assets/css/ia/ia_proceso.css">
<script>window.IA_SERVICIO_URL = <?php echo json_encode(IA_SERVICIO_URL); ?>;</script>
<script src="assets/js/ia/ia_proceso.js"></script>

</body>
</html>
