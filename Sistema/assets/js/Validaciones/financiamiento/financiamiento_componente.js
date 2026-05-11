const IAEvaluador = {
    url: 'http://127.0.0.1:5000/evaluar',

    consultar: function(monto, historial) {
        if (!monto || monto <= 0) {
            $("#contenedor_ia").hide();
            return;
        }

        $("#contenedor_ia").fadeIn();
        $("#ia_cargando").show();
        $("#ia_mensaje").text("Analizando perfil crediticio...").removeClass().addClass("text-white-50 small");
        $("#ia_nivel").text("---").removeClass().addClass("badge bg-secondary");

        fetch(this.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                ingreso: parseFloat(monto), 
                historial: parseInt(historial) 
            })
        })
        .then(res => res.json())
        .then(data => {
            $("#ia_cargando").hide();
            if (data.error) {
                this.errorInterfaz(data.error);
            } else {
                this.actualizarInterfaz(data);
            }
        })
        .catch(err => {
            $("#ia_cargando").hide();
            this.errorInterfaz("El microservicio IA no responde. Verifique que el servidor Python esté activo.");
        });
    },

    actualizarInterfaz: function(data) {
        $("#ia_puntaje").text(data.puntaje_confianza + "%");
        $("#ia_cuotas").text(data.cuotas_recomendadas);
        
        let badgeClass = "";
        let mensajeClass = "";
        let icono = "";

        if (data.nivel_riesgo === 'Alto') {
            badgeClass = 'bg-success';
            mensajeClass = 'text-success';
            icono = '✓';
        } else if (data.nivel_riesgo === 'Medio') {
            badgeClass = 'bg-warning text-dark';
            mensajeClass = 'text-warning';
            icono = '⚠';
        } else {
            badgeClass = 'bg-danger';
            mensajeClass = 'text-danger';
            icono = '✖';
        }

        $("#ia_nivel").text(data.nivel_riesgo).removeClass().addClass('badge ' + badgeClass);
        
        const textoAprobado = data.aprobado === "SI" 
            ? `${icono} Recomendación: Crédito Apto. Sugerido ${data.cuotas_recomendadas} cuotas.`
            : `${icono} Recomendación: Riesgo Elevado. Solicitar mayor pago inicial.`;
            
        $("#ia_mensaje").text(textoAprobado).removeClass().addClass(mensajeClass + " small fw-bold");
    },

    errorInterfaz: function(mensaje) {
        $("#ia_puntaje").text("0%");
        $("#ia_cuotas").text("0");
        $("#ia_nivel").text("ERROR").removeClass().addClass("badge bg-danger");
        $("#ia_mensaje").text(mensaje).removeClass().addClass("text-danger small");
    }
};

$(document).on("change", "#cedula_cliente", function() {
    let cedula = $(this).val();
    if (!cedula) return;

    $.ajax({
        type: "POST",
        url: "?pagina=clientes",
        data: { id: cedula, accion: "consultar" },
        success: function(response) {
            let cliente = JSON.parse(response);
            let sueldo = cliente.ingresos_mensuales || 0;
            let score = cliente.score_credito || 5;
            IAEvaluador.consultar(sueldo, score);
        }
    });
});

$(document).on("input", "#monto_total", function() {
    let cedula = $("#cedula_cliente").val();
    if (cedula) {
        $("#cedula_cliente").trigger("change");
    } else {
        let monto = $(this).val();
        IAEvaluador.consultar(monto, 5);
    }
});