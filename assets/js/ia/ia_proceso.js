/* ==========================================================================
   Visor del Proceso Difuso (RCI-01)
   Muestra en vivo: variables linguisticas, conectivos logicos difusos y las
   graficas de fuzzificacion, motor de inferencia y defuzzificacion.
   Sin librerias externas: las graficas se dibujan en SVG puro.
   ========================================================================== */

(function () {
    'use strict';

    const NS = 'http://www.w3.org/2000/svg';

    const COLOR_TERMINO = ['var(--ia-bajo)', 'var(--ia-medio)', 'var(--ia-alto)'];

    const IAVisualizador = {
        servicio: (
            window.IA_SERVICIO_URL ||
            document.body.getAttribute('data-servicio-ia') ||
            'http://127.0.0.1:5000'
        ).replace(/\/$/, ''),
        traza: null,
        temporizador: null,
        animacion: null,

        /* ---------------------------------------------------------- arranque */

        iniciar: function () {
            this.enlazarControles();
            this.comprobarServicio();
        },

        comprobarServicio: function () {
            const estado = document.getElementById('ia_estado');
            const texto = document.getElementById('ia_estado_texto');

            fetch(this.servicio + '/salud')
                .then(r => r.json())
                .then(datos => {
                    estado.classList.remove('ia-offline');
                    estado.classList.add('ia-online');
                    texto.textContent = 'Servicio de análisis ' + datos.servicio + ' en línea';
                    this.simular();
                })
                               .catch(() => {
                    estado.classList.remove('ia-online');
                    estado.classList.add('ia-offline');
                    texto.textContent = 'Iniciando el servicio de análisis...';
                    this.mostrarAviso('Iniciando el servicio de análisis. Espere un momento...', false);

                    // Intentar arrancarlo automáticamente solo en local
                    fetch('?pagina=ia_iniciar')
                        .then(r => r.json())
                        .catch(() => { })
                        .finally(() => {
                            // Reintentar conexión después de 4 segundos
                            setTimeout(() => {
                                this.comprobarServicio();
                            }, 4000);
                        });
                });
        },

        enlazarControles: function () {
            const ingreso = document.getElementById('ia_ingreso');
            const historial = document.getElementById('ia_historial');

            const alMover = () => {
                document.getElementById('ia_ingreso_valor').textContent = ingreso.value;
                document.getElementById('ia_historial_valor').textContent = historial.value;
                this.programarSimulacion();
            };

            ingreso.addEventListener('input', alMover);
            historial.addEventListener('input', alMover);

            document.getElementById('ia_cargar').addEventListener('click', () => {
                this.evaluarCedula();
            });

            document.getElementById('ia_cedula').addEventListener('keydown', evento => {
                if (evento.key === 'Enter') {
                    this.evaluarCedula();
                }
            });

            document.getElementById('ia_recorrer').addEventListener('click', () => {
                this.recorrer();
            });
        },

        programarSimulacion: function () {
            clearTimeout(this.temporizador);
            this.temporizador = setTimeout(() => this.simular(), 70);
        },

        /* -------------------------------------------------------- simulacion */

        simular: function () {
            const ingreso = parseFloat(document.getElementById('ia_ingreso').value);
            const historial = parseFloat(document.getElementById('ia_historial').value);

            fetch(this.servicio + '/proceso', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ingreso: ingreso, historial: historial })
            })
                .then(r => r.json())
                .then(datos => {
                    if (datos.error) {
                        this.mostrarAviso(datos.error, true);
                        return;
                    }
                    this.ocultarAviso();
                    this.traza = datos;
                    this.dibujarTodo(datos);
                })
                .catch(() => {
                    this.mostrarAviso('No se pudo consultar el servicio de análisis.', true);
                });
        },

        evaluarCedula: function () {
            const cedula = document.getElementById('ia_cedula').value.trim();

            if (!cedula) {
                this.mostrarAviso('Escribe la cedula del prospecto.', true);
                return;
            }

            const aviso = document.getElementById('ia_aviso');
            aviso.classList.remove('oculto');
            aviso.textContent = 'Consultando el perfil financiero...';

            fetch(this.servicio + '/evaluar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cedula: cedula })
            })
                .then(r => r.json())
                .then(datos => {
                    if (datos.error) {
                        this.mostrarAviso(datos.error, true);
                        return;
                    }

                    this.ocultarAviso();

                    const proceso = datos.proceso;
                    const entrada = proceso.entrada;

                    document.getElementById('ia_ingreso').value = entrada.ingreso;
                    document.getElementById('ia_historial').value = entrada.historial;
                    document.getElementById('ia_ingreso_valor').textContent = entrada.ingreso;
                    document.getElementById('ia_historial_valor').textContent = entrada.historial;

                    this.traza = proceso;
                    this.dibujarTodo(proceso);
                })
                .catch(() => {
                    this.mostrarAviso('No se pudo consultar el servicio de análisis.', true);
                });
        },

        /* ------------------------------------------------------- recorrido */

        recorrer: function () {
            const boton = document.getElementById('ia_recorrer');

            if (this.animacion) {
                clearInterval(this.animacion);
                this.animacion = null;
                boton.innerHTML = '<i class="bi bi-play-fill"></i> Recorrido automatico';
                return;
            }

            boton.innerHTML = '<i class="bi bi-stop-fill"></i> Detener recorrido';

            // Recorrido narrativo: los ingresos suben, luego el historial sube,
            // luego ambos vuelven a cero. Cada paso recalcula el proceso para
            // que se vea el cambio en vivo de las graficas.
            let fase = 0;

            this.animacion = setInterval(() => {
                const ingreso = document.getElementById('ia_ingreso');
                const historial = document.getElementById('ia_historial');

                const ing = parseFloat(ingreso.value);
                const his = parseFloat(historial.value);

                if (fase === 0) {
                    ingreso.value = Math.min(1000, ing + 25);
                    if (ingreso.value >= 1000) {
                        fase = 1;
                    }
                } else if (fase === 1) {
                    historial.value = Math.min(100, his + 2);
                    if (historial.value >= 100) {
                        fase = 2;
                    }
                } else if (fase === 2) {
                    ingreso.value = Math.max(0, ing - 25);
                    if (ingreso.value <= 0) {
                        fase = 3;
                    }
                } else {
                    historial.value = Math.max(0, his - 2);
                    if (historial.value <= 0) {
                        fase = 0;
                    }
                }

                document.getElementById('ia_ingreso_valor').textContent = ingreso.value;
                document.getElementById('ia_historial_valor').textContent = historial.value;

                this.programarSimulacion();
            }, 90);
        },

        /* ------------------------------------------------------------ aviso */

        mostrarAviso: function (texto, esError) {
            const aviso = document.getElementById('ia_aviso');
            aviso.textContent = texto;
            aviso.classList.remove('oculto');
            aviso.classList.toggle('ia-error', !!esError);
        },

        ocultarAviso: function () {
            document.getElementById('ia_aviso').classList.add('oculto');
        },

        /* --------------------------------------------------------- dibujado */

        dibujarTodo: function (traza) {
            this.dibujarResultado(traza.resultado);
            this.dibujarVariablesLinguisticas(traza.variables);
            this.dibujarFuzzificacion(traza.variables);
            this.dibujarInferencia(traza.inferencia);
            this.dibujarDefuzzificacion(traza.variables, traza.defuzzificacion);
        },

        dibujarResultado: function (resultado) {
            document.getElementById('ia_puntaje').textContent = resultado.puntaje_confianza + '%';
            document.getElementById('ia_cuotas').textContent = resultado.cuotas_recomendadas;

            const nivel = document.getElementById('ia_nivel');
            nivel.textContent = resultado.nivel_riesgo;
            nivel.className = 'ia-veredicto ' + (resultado.nivel_riesgo === 'Bajo' ? 'ia-no' : 'ia-si');

            const veredicto = document.getElementById('ia_veredicto');
            veredicto.textContent = resultado.aprobado === 'SI'
                ? 'Credito recomendado'
                : 'Credito no recomendado';
            veredicto.className = 'ia-veredicto ' + (resultado.aprobado === 'SI' ? 'ia-si' : 'ia-no');
        },

        /* --- Paso 1: variables linguisticas (los valores son palabras) --- */

        dibujarVariablesLinguisticas: function (variables) {
            const contenedor = document.getElementById('ia_variables');
            contenedor.innerHTML = '';

            variables.forEach(variable => {
                const tarjeta = document.createElement('div');
                tarjeta.className = 'col-12 col-lg-6';

                const titulo = document.createElement('div');
                titulo.className = 'd-flex align-items-baseline gap-2 mb-2';

                const nombre = document.createElement('strong');
                nombre.textContent = variable.titulo;
                nombre.style.fontSize = '.95rem';

                const rol = document.createElement('span');
                rol.className = 'ia-cifra-label';
                rol.textContent = variable.tipo === 'antecedente' ? 'Entrada' : 'Salida';

                titulo.appendChild(nombre);
                titulo.appendChild(rol);

                const descripcion = document.createElement('p');
                descripcion.className = 'ia-nota-conectivo mb-2';
                descripcion.textContent = variable.descripcion;

                const fila = document.createElement('div');
                fila.className = 'row g-2';

                variable.terminos.forEach((termino, indice) => {
                    const celda = document.createElement('div');
                    celda.className = 'col-12 col-sm-4';

                    const caja = document.createElement('div');
                    caja.className = 'ia-valor-linguistico';
                    caja.style.setProperty('--ia-color-termino', COLOR_TERMINO[indice % 3]);

                    const dominante = termino.activacion > 0 &&
                        termino.activacion === Math.max.apply(null,
                            variable.terminos.map(t => t.activacion));
                    if (dominante && termino.activacion > 0) {
                        caja.classList.add('ia-activo');
                    }

                    const palabra = document.createElement('div');
                    palabra.className = 'ia-palabra';
                    palabra.textContent = termino.etiqueta;

                    const nota = document.createElement('div');
                    nota.className = 'ia-nota';
                    nota.textContent = termino.descripcion;

                    const barra = document.createElement('span');
                    barra.className = 'ia-grado';
                    barra.style.width = Math.round(termino.activacion * 100) + '%';

                    caja.appendChild(palabra);
                    caja.appendChild(nota);
                    caja.appendChild(barra);

                    celda.appendChild(caja);
                    fila.appendChild(celda);
                });

                tarjeta.appendChild(titulo);
                tarjeta.appendChild(descripcion);
                tarjeta.appendChild(fila);

                const columna = document.createElement('div');
                columna.className = 'col-12';
                columna.appendChild(tarjeta);
                contenedor.appendChild(columna);
            });
        },

        /* --- Paso 2: fuzzificacion (grafica de las funciones de pertenencia) */

        dibujarFuzzificacion: function (variables) {
            const contenedor = document.getElementById('ia_grafica_fuzzificacion');
            contenedor.innerHTML = '';

            variables.filter(v => v.tipo === 'antecedente').forEach(variable => {
                const columna = document.createElement('div');
                columna.className = 'col-12 col-xl-6';

                const tarjeta = document.createElement('div');
                tarjeta.className = 'ia-tarjeta h-100';

                const cabecera = document.createElement('div');
                cabecera.className = 'ia-etapa-titulo';

                const paso = document.createElement('span');
                paso.className = 'ia-paso';
                paso.textContent = '2';

                const textos = document.createElement('div');
                const titulo = document.createElement('h3');
                titulo.textContent = 'Cómo interpreta ' + variable.titulo + ' ';
                titulo.appendChild(this.crearJerga(
                    'Fuzzificación',
                    'Convierte el valor exacto de entrada en grados de pertenencia (μ) entre 0 y 1.'
                ));
                const sub = document.createElement('p');
                sub.textContent = 'Qué tanto encaja el valor actual (' + variable.valor + ' ' +
                    variable.unidad + ') con cada categoría';
                textos.appendChild(titulo);
                textos.appendChild(sub);

                cabecera.appendChild(paso);
                cabecera.appendChild(textos);

                const grafica = document.createElement('div');
                grafica.className = 'ia-grafica';

                const curvas = variable.terminos.map((termino, indice) => ({
                    etiqueta: termino.etiqueta,
                    color: COLOR_TERMINO[indice % 3],
                    puntos: termino.curva,
                    relleno: false
                }));

                this.pintarGrafica(grafica, {
                    curvas: curvas,
                    marcas: [{
                        x: variable.valor,
                        color: 'var(--ia-acento)',
                        texto: 'valor = ' + variable.valor
                    }],
                    rangoX: variable.rango,
                    tituloX: variable.titulo + ' (' + variable.unidad + ')',
                    etiquetaY: 'Qué tanto encaja (0 a 1)'
                });

                const leyenda = document.createElement('div');
                leyenda.className = 'ia-leyenda';
                variable.terminos.forEach((termino, indice) => {
                    const item = document.createElement('span');
                    const barra = document.createElement('i');
                    barra.style.background = COLOR_TERMINO[indice % 3];
                    const texto = document.createElement('span');
                    texto.textContent = termino.etiqueta + ' = ' + termino.activacion.toFixed(2);
                    item.appendChild(barra);
                    item.appendChild(texto);
                    leyenda.appendChild(item);
                });

                tarjeta.appendChild(cabecera);
                tarjeta.appendChild(grafica);
                tarjeta.appendChild(leyenda);

                columna.appendChild(tarjeta);
                contenedor.appendChild(columna);
            });
        },

        /* --- Paso 3: motor de inferencia (reglas y conectivos) --- */

        dibujarInferencia: function (inferencia) {
            const resumen = document.getElementById('ia_resumen_inferencia');
            resumen.textContent = inferences(inferencia) + ' ';
            resumen.appendChild(this.crearJerga(
                'Disparo de reglas',
                'Fuerza de disparo (aggregate firing): grado con que cada regla se activa.'
            ));

            const contenedor = document.getElementById('ia_reglas');
            contenedor.innerHTML = '';

            inferencia.reglas.forEach(regla => {
                const tarjeta = document.createElement('div');
                tarjeta.className = 'ia-regla ' + (regla.disparo ? 'ia-activa' : 'ia-inactiva');

                const linea = document.createElement('div');
                linea.className = 'd-flex align-items-center gap-2 flex-wrap mb-2';

                const id = document.createElement('span');
                id.className = 'ia-cifra-label';
                id.style.fontWeight = '800';
                id.textContent = regla.id;

                const nombre = document.createElement('strong');
                nombre.style.fontSize = '.86rem';
                nombre.textContent = regla.nombre;

                const fuerza = document.createElement('span');
                fuerza.className = 'ms-auto ia-cifra-label';
                fuerza.style.fontWeight = '800';
                fuerza.textContent = 'cumplimiento ' + regla.fuerza.toFixed(2);
                fuerza.title = 'Fuerza de disparo de la regla';

                linea.appendChild(id);
                linea.appendChild(nombre);
                linea.appendChild(fuerza);

                const condiciones = document.createElement('div');
                condiciones.className = 'd-flex align-items-center gap-2 flex-wrap mb-2';

                regla.antecedentes.forEach((antecedente, indice) => {
                    if (indice > 0) {
                        const conector = document.createElement('span');
                        conector.className = 'ia-conectivo ' +
                            (regla.conectivo === 'AND' ? 'ia-and' : 'ia-or');
                        conector.textContent = regla.simbolo;
                        conector.title = regla.texto_conectivo;
                        condiciones.appendChild(conector);
                    }

                    const condicion = document.createElement('span');
                    condicion.className = 'ia-condicion';

                    const variable = document.createElement('span');
                    variable.textContent = antecedente.etiqueta;

                    const grado = document.createElement('span');
                    grado.className = 'ia-grado-mini';
                    grado.textContent = antecedente.grado.toFixed(2);

                    condicion.appendChild(variable);
                    condicion.appendChild(grado);
                    condiciones.appendChild(condicion);
                });

                const salida = document.createElement('div');
                salida.className = 'd-flex align-items-center gap-2 flex-wrap mb-2';

                const flecha = document.createElement('span');
                flecha.className = 'ia-cifra-label';
                flecha.textContent = '=>';

                const concluso = document.createElement('span');
                concluso.className = 'ia-condicion';

                const nombres = regla.consecuentes.map(c =>
                    this.tituloVariable(c.variable) + ' = ' + c.etiqueta +
                    ' (' + c.activacion.toFixed(2) + ')');
                concluso.textContent = nombres.join('   |   ');

                salida.appendChild(flecha);
                salida.appendChild(concluso);

                const barra = document.createElement('div');
                barra.className = 'ia-fuerza-barra';
                const relleno = document.createElement('span');
                relleno.style.width = Math.round(regla.fuerza * 100) + '%';
                barra.appendChild(relleno);

                const descripcion = document.createElement('p');
                descripcion.className = 'ia-nota-conectivo mt-2 mb-0';
                descripcion.textContent = regla.descripcion;

                tarjeta.appendChild(linea);
                tarjeta.appendChild(condiciones);
                tarjeta.appendChild(salida);
                tarjeta.appendChild(barra);
                tarjeta.appendChild(descripcion);

                contenedor.appendChild(tarjeta);
            });
        },

        /* --- Paso 4: defuzzificacion (salida crisp sobre la curva recortada) */

        dibujarDefuzzificacion: function (variables, defuzzificacion) {
            const contenedor = document.getElementById('ia_grafica_defuzzificacion');
            contenedor.innerHTML = '';

            defuzzificacion.forEach(salida => {
                const variable = variables.filter(v => v.clave === salida.variable)[0];
                if (!variable) {
                    return;
                }

                const columna = document.createElement('div');
                columna.className = 'col-12 col-xl-6';

                const tarjeta = document.createElement('div');
                tarjeta.className = 'ia-tarjeta h-100';

                const cabecera = document.createElement('div');
                cabecera.className = 'ia-etapa-titulo';

                const paso = document.createElement('span');
                paso.className = 'ia-paso';
                paso.textContent = '4';

                const textos = document.createElement('div');
                const titulo = document.createElement('h3');
                titulo.textContent = 'Número final de ' + salida.titulo + ' ';
                titulo.appendChild(this.crearJerga(
                    'Defuzzificación',
                    'Método ' + salida.metodo.toLowerCase() +
                    ': agrega las reglas activadas y extrae un único valor.'
                ));
                const sub = document.createElement('p');
                sub.textContent = 'El sistema resume las reglas activadas en un solo valor: ' +
                    salida.salida_crisp + ' ' + salida.unidad;
                textos.appendChild(titulo);
                textos.appendChild(sub);

                cabecera.appendChild(paso);
                cabecera.appendChild(textos);

                const grafica = document.createElement('div');
                grafica.className = 'ia-grafica';

                const curvas = [];

                variable.terminos.forEach((termino, indice) => {
                    curvas.push({
                        etiqueta: termino.etiqueta,
                        color: COLOR_TERMINO[indice % 3],
                        puntos: termino.curva,
                        relleno: false,
                        original: true
                    });
                    curvas.push({
                        etiqueta: termino.etiqueta + ' recortada',
                        color: COLOR_TERMINO[indice % 3],
                        puntos: termino.curva_recortada,
                        relleno: true,
                        opacidad: 0.28
                    });
                });

                this.pintarGrafica(grafica, {
                    curvas: curvas,
                    marcas: [{
                        x: salida.salida_crisp,
                        color: 'var(--ia-acento)',
                        texto: 'número final = ' + salida.salida_crisp
                    }],
                    rangoX: variable.rango,
                    tituloX: salida.titulo + ' (' + salida.unidad + ')',
                    etiquetaY: 'Qué tanto encaja (0 a 1)'
                });

                const leyenda = document.createElement('div');
                leyenda.className = 'ia-leyenda';

                const original = document.createElement('span');
                const barraOriginal = document.createElement('i');
                barraOriginal.style.background = 'var(--ia-texto-suave)';
                const textoOriginal = document.createElement('span');
                textoOriginal.textContent = 'curva base';
                original.appendChild(barraOriginal);
                original.appendChild(textoOriginal);
                leyenda.appendChild(original);

                const recortada = document.createElement('span');
                const barraRecortada = document.createElement('i');
                barraRecortada.style.background = 'var(--ia-alto)';
                const textoRecortada = document.createElement('span');
                textoRecortada.textContent = 'curva ajustada por las reglas';
                recortada.appendChild(barraRecortada);
                recortada.appendChild(textoRecortada);
                leyenda.appendChild(recortada);

                tarjeta.appendChild(cabecera);
                tarjeta.appendChild(grafica);
                tarjeta.appendChild(leyenda);

                columna.appendChild(tarjeta);
                contenedor.appendChild(columna);
            });
        },

        /* ---------------------------------------------------- motor de SVG */

        pintarGrafica: function (contenedor, opciones) {
            const ancho = 720;
            const alto = 320;
            const margen = { arriba: 26, derecha: 20, abajo: 52, izquierda: 56 };
            const areaAncho = ancho - margen.izquierda - margen.derecha;
            const areaAlto = alto - margen.arriba - margen.abajo;

            const rangoX = opciones.rangoX;
            const xMin = rangoX[0];
            const xMax = rangoX[1];

            const svg = this.crearElemento('svg', {
                'viewBox': '0 0 ' + ancho + ' ' + alto,
                'class': 'ia-grafica',
                'role': 'img'
            });

            const escalaX = valor => margen.izquierda +
                ((valor - xMin) / (xMax - xMin)) * areaAncho;
            const escalaY = grado => margen.arriba + (1 - grado) * areaAlto;

            // Rejilla horizontal y eje de grados de pertenencia
            [0, 0.25, 0.5, 0.75, 1].forEach(grado => {
                const y = escalaY(grado);
                svg.appendChild(this.crearElemento('line', {
                    'x1': margen.izquierda, 'y1': y,
                    'x2': margen.izquierda + areaAncho, 'y2': y,
                    'class': grado === 0 ? 'ia-eje' : 'ia-rejilla'
                }));

                const etiqueta = this.crearElemento('text', {
                    'x': margen.izquierda - 10, 'y': y + 3.5,
                    'class': 'ia-texto-eje', 'text-anchor': 'end'
                });
                etiqueta.textContent = grado.toFixed(2);
                svg.appendChild(etiqueta);
            });

            // Eje horizontal y marcas
            const marcas = this.marcasEje(xMin, xMax, 6);
            marcas.forEach(valor => {
                const x = escalaX(valor);
                svg.appendChild(this.crearElemento('line', {
                    'x1': x, 'y1': margen.arriba,
                    'x2': x, 'y2': margen.arriba + areaAlto,
                    'class': 'ia-rejilla'
                }));

                const etiqueta = this.crearElemento('text', {
                    'x': x, 'y': margen.arriba + areaAlto + 16,
                    'class': 'ia-texto-eje', 'text-anchor': 'middle'
                });
                etiqueta.textContent = this.formatearNumero(valor);
                svg.appendChild(etiqueta);
            });

            // Titulos de los ejes
            const tituloX = this.crearElemento('text', {
                'x': margen.izquierda + areaAncho / 2,
                'y': alto - 12,
                'class': 'ia-texto-eje', 'text-anchor': 'middle'
            });
            tituloX.textContent = opciones.tituloX;
            svg.appendChild(tituloX);

            const tituloY = this.crearElemento('text', {
                'x': 14,
                'y': margen.arriba + areaAlto / 2,
                'class': 'ia-texto-eje', 'text-anchor': 'middle',
                'transform': 'rotate(-90 14 ' + (margen.arriba + areaAlto / 2) + ')'
            });
            tituloY.textContent = opciones.etiquetaY;
            svg.appendChild(tituloY);

            // Marcas verticales (valor crisp de entrada / salida)
            (opciones.marcas || []).forEach(marca => {
                if (marca.x === null || marca.x === undefined) {
                    return;
                }
                const x = escalaX(marca.x);
                svg.appendChild(this.crearElemento('line', {
                    'x1': x, 'y1': margen.arriba - 6,
                    'x2': x, 'y2': margen.arriba + areaAlto,
                    'class': 'ia-marca', 'stroke': marca.color
                }));

                const etiqueta = this.crearElemento('text', {
                    'x': x,
                    'y': margen.arriba - 10,
                    'class': 'ia-texto-eje',
                    'text-anchor': 'middle',
                    'fill': marca.color
                });
                etiqueta.textContent = marca.texto;
                svg.appendChild(etiqueta);
            });

            // Curvas: primero las rellenas, encima las lineas
            const rellenos = opciones.curvas.filter(c => c.relleno);
            const lineas = opciones.curvas.filter(c => !c.relleno);

            rellenos.forEach(curva => {
                const d = this.trazarCurva(curva.puntos, escalaX, escalaY, margen.arriba + areaAlto);
                svg.appendChild(this.crearElemento('path', {
                    'd': d,
                    'fill': curva.color,
                    'fill-opacity': curva.opacidad || 0.25,
                    'stroke': 'none'
                }));
            });

            lineas.forEach(curva => {
                const d = this.trazarCurva(curva.puntos, escalaX, escalaY, null);
                svg.appendChild(this.crearElemento('path', {
                    'd': d,
                    'class': 'ia-curva' + (curva.original ? ' ia-curva-original' : ''),
                    'stroke': curva.color
                }));
            });

            contenedor.innerHTML = '';
            contenedor.appendChild(svg);
        },

        trazarCurva: function (puntos, escalaX, escalaY, base) {
            if (!puntos || puntos.length === 0) {
                return '';
            }

            let d = '';
            puntos.forEach((punto, indice) => {
                const x = escalaX(punto[0]);
                const y = escalaY(punto[1]);
                d += (indice === 0 ? 'M' : 'L') + x.toFixed(2) + ' ' + y.toFixed(2) + ' ';
            });

            if (base !== null) {
                const ultimo = puntos[puntos.length - 1];
                const primero = puntos[0];
                d += 'L' + escalaX(ultimo[0]).toFixed(2) + ' ' + base + ' ';
                d += 'L' + escalaX(primero[0]).toFixed(2) + ' ' + base + ' Z';
            }

            return d;
        },

        crearElemento: function (etiqueta, atributos) {
            const nodo = document.createElementNS(NS, etiqueta);
            for (const clave in atributos) {
                if (Object.prototype.hasOwnProperty.call(atributos, clave)) {
                    nodo.setAttribute(clave, atributos[clave]);
                }
            }
            return nodo;
        },

        marcasEje: function (minimo, maximo, cantidad) {
            const marcas = [];
            const paso = (maximo - minimo) / (cantidad - 1);
            for (let i = 0; i < cantidad; i++) {
                marcas.push(minimo + paso * i);
            }
            return marcas;
        },

        formatearNumero: function (valor) {
            if (Math.abs(valor - Math.round(valor)) < 0.001) {
                return String(Math.round(valor));
            }
            return valor.toFixed(1);
        },

        /* ------------------------------------------------- etiquetas jerga */

        // Insignia con el termino tecnico (para el publico academico). El texto
        // principal del panel va en lenguaje neutral; la insignia conserva la
        // jerga formal y su title la explica.
        crearJerga: function (texto, explicacion) {
            const insignia = document.createElement('span');
            insignia.className = 'ia-jerga';
            insignia.textContent = texto;
            if (explicacion) {
                insignia.title = explicacion;
            }
            return insignia;
        },

        // Nombre legible de una variable a partir de su clave interna.
        tituloVariable: function (clave) {
            const variables = (this.traza && this.traza.variables) || [];
            const encontrada = variables.filter(v => v.clave === clave)[0];
            return encontrada ? encontrada.titulo : clave;
        }
    };

    function inferences(inferencia) {
        return inferencia.reglas_disparadas + ' de ' + inferencia.reglas_total +
            ' reglas aplicadas · cumplimiento máximo ' + inferencia.fuerza_maxima.toFixed(2);
    }

        function aplicarRestriccionesIA() {
        const rol = parseInt(window.IA_ROL || 0, 10);

        // Rol 2: solo puede visualizar, NO puede evaluar por cédula
        if (rol === 2) {
            const btnCargar = document.getElementById('ia_cargar');
            const inputCedula = document.getElementById('ia_cedula');

            if (btnCargar) {
                btnCargar.disabled = true;
                btnCargar.classList.add('ia-btn-disabled');
                btnCargar.setAttribute('title', 'Sin permiso para evaluar por cédula');
                btnCargar.innerHTML = '<i class="bi bi-lock-fill"></i> Evaluar cédula (Restringido)';
            }

            if (inputCedula) {
                inputCedula.disabled = true;
                inputCedula.placeholder = 'Evaluación por cédula no permitida para tu rol';
                inputCedula.setAttribute('title', 'Sin permiso para evaluar por cédula');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        aplicarRestriccionesIA();
        IAVisualizador.iniciar();
    });
})();
