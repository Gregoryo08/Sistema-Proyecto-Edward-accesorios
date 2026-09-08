// /src/assets/js/ecommerce/temaBoton.js
// =============================================
// TEMA DIA/NOCHE - BOTON (sol/luna)
// Usado en catálogo y login/registro ecommerce.
// El botón #themeToggle lleva data-icon-oscuro y
// data-icon-claro con las clases de los iconos.
// =============================================
(function() {
    'use strict';

    var KEY = 'edward_theme';
    var btn = document.getElementById('themeToggle');
    var icon = document.getElementById('themeIcon');
    var claseOscuro = (btn && btn.getAttribute('data-icon-oscuro')) || 'fas fa-sun';
    var claseClaro = (btn && btn.getAttribute('data-icon-claro')) || 'fas fa-moon';

    function aplicar(oscuro) {
        document.body.classList.toggle('light-mode', !oscuro);
        if (icon) {
            icon.className = oscuro ? claseOscuro : claseClaro;
        }
        localStorage.setItem(KEY, oscuro ? 'dark' : 'light');
    }

    function init() {
        var guardado = localStorage.getItem(KEY);
        if (guardado === 'light') {
            aplicar(false);
        } else if (guardado === 'dark') {
            aplicar(true);
        } else {
            aplicar(true);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        init();
        if (btn) {
            btn.addEventListener('click', function() {
                aplicar(document.body.classList.contains('light-mode'));
            });
        }
    });
})();
