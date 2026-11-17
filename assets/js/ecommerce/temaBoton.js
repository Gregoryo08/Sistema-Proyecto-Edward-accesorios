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
        var valor = oscuro ? 'dark' : 'light';
        localStorage.setItem(KEY, valor);
        localStorage.setItem('theme', valor);
    }

    function temaGuardado() {
        var v = localStorage.getItem(KEY) || localStorage.getItem('theme');
        if (v === 'light') return false;
        if (v === 'dark') return true;
        return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    function init() {
        aplicar(temaGuardado());
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
