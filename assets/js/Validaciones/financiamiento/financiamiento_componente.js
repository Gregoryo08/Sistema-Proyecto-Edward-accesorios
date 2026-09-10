const IAEvaluador = {
    url: 'http://127.0.0.1:5000/evaluar',

    consultar: function(cedula) {
        if (!cedula) {
            $("#contenedor_ia").hide();
            return;
        }

        $("#contenedor_ia").fadeIn();
        $("#ia_cargando").show();
        $("#ia_mensaje").text("Consultando perfil en base de datos...").removeClass().addClass("text-white-50 small");
        $("#ia_nivel").text("---").removeClass().addClass("badge bg-secondary");

        fetch(this.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cedula: cedula })
        })
        .then(res => {
            if (!res.ok && res.headers.get("content-type")?.indexOf("application/json") === -1) {
                throw new Error("Error en el servidor");
            }
            return res.json();
        })
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
            this.errorInterfaz("El servidor IA no responde.");
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

$(document).on("change blur", "#cedula_persona", function() {
    let cedula = $(this).val().trim();
    if (cedula) {
        IAEvaluador.consultar(cedula);
    }
});