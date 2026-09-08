// /src/assets/js/ecommerce/temas.js
// =============================================
// SISTEMA DE TEMAS DIA/NOCHE
// =============================================

(function() {
    'use strict';

    const STORAGE_KEY = 'edward_theme';
    const DARK_CLASS = 'dark-mode';

    function aplicarTema(esOscuro) {
        if (esOscuro) {
            document.body.classList.add(DARK_CLASS);
            localStorage.setItem(STORAGE_KEY, 'dark');
        } else {
            document.body.classList.remove(DARK_CLASS);
            localStorage.setItem(STORAGE_KEY, 'light');
        }
        actualizarToggle(esOscuro);
    }

    function actualizarToggle(esOscuro) {
        const toggle = document.getElementById('themeToggle');
        if (toggle) {
            toggle.checked = esOscuro;
        }
    }

    function toggleTheme() {
        const esOscuro = document.body.classList.contains(DARK_CLASS);
        aplicarTema(!esOscuro);
    }

    function detectarPreferenciaSistema() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return true;
        }
        return false;
    }

    function initTheme() {
        const guardado = localStorage.getItem(STORAGE_KEY);

        if (guardado === 'dark') {
            aplicarTema(true);
            return;
        }
        if (guardado === 'light') {
            aplicarTema(false);
            return;
        }

        const sistemaOscuro = detectarPreferenciaSistema();
        aplicarTema(sistemaOscuro);
    }

    function setupToggle() {
        const toggle = document.getElementById('themeToggle');
        if (toggle) {
            toggle.addEventListener('change', function() {
                aplicarTema(this.checked);
            });
        }
    }

    function escucharCambiosSistema() {
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addEventListener('change', function(e) {
                if (!localStorage.getItem(STORAGE_KEY)) {
                    aplicarTema(e.matches);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        setupToggle();
        escucharCambiosSistema();
        console.log('Sistema de temas inicializado');
    });

    window.toggleTheme = toggleTheme;

})();
