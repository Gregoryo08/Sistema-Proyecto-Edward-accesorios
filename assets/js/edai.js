/* ED-AI Asistente (Intranet) - FAQ local con control de usuarios simultaneos */
(function () {
    const fab = document.getElementById('edaiFab');
    const windowEl = document.getElementById('edaiWindow');
    const closeBtn = document.getElementById('edaiClose');
    const body = document.getElementById('edaiBody');
    const input = document.getElementById('edaiInput');
    const sendBtn = document.getElementById('edaiSend');
    const status = document.getElementById('edaiStatus');
    const aviso = document.getElementById('edaiDisponible');

    const CONTEXTO = (document.body.getAttribute('data-edai-contexto') || 'intranet').trim();
    let abierto = false;

    function esc(t) {
        const div = document.createElement('div');
        div.textContent = t;
        return div.innerHTML;
    }

    function agregarMensaje(texto, esUsuario) {
        const p = document.createElement('p');
        p.className = 'edai-msg ' + (esUsuario ? 'edai-user' : 'edai-bot');
        p.innerHTML = esUsuario ? esc(texto) : texto;
        body.appendChild(p);
        body.scrollTop = body.scrollHeight;
    }

    function registrarSlot(abrirDespues) {
        fetch('?pagina=edai&action=registrar&contexto=' + CONTEXTO)
            .then(r => r.json())
            .then(data => {
                if (data.lleno) {
                    status.textContent = 'lleno ahora';
                    status.style.color = '#fca5a5';
                    if (aviso) {
                        aviso.textContent = data.message;
                        aviso.style.display = 'block';
                        setTimeout(() => { aviso.style.display = 'none'; }, 6000);
                    }
                    return;
                }
                status.textContent = 'disponible (' + data.activos + '/' + data.max + ')';
                status.style.color = '#bbf7d0';
                if (abrirDespues) {
                    abrir();
                }
            })
            .catch(() => {
                status.textContent = 'disponible';
                if (abrirDespues) abrir();
            });
    }

    function abrir() {
        abierto = true;
        windowEl.classList.add('open');
        registrarSlot(false);
        input.focus();
    }

    function cerrar() {
        abierto = false;
        windowEl.classList.remove('open');
        fetch('?pagina=edai&action=liberar&contexto=' + CONTEXTO).catch(() => {});
    }

    function enviar() {
        const mensaje = input.value.trim();
        if (!mensaje) return;
        agregarMensaje(mensaje, true);
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        const form = new URLSearchParams();
        form.append('mensaje', mensaje);
        form.append('contexto', CONTEXTO);

        fetch('?pagina=edai&action=enviar&contexto=' + CONTEXTO, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: form.toString()
        })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    agregarMensaje(data.respuesta, false);
                } else {
                    agregarMensaje(data.message || 'El asistente está lleno ahora mismo. Intenta en unos minutos.', false);
                }
            })
            .catch(() => {
                agregarMensaje('Error de conexión. Inténtalo de nuevo.', false);
            })
            .finally(() => {
                input.disabled = false;
                sendBtn.disabled = false;
                input.focus();
            });
    }

    if (fab) fab.addEventListener('click', () => { abierto ? cerrar() : abrir(); });
    if (closeBtn) closeBtn.addEventListener('click', cerrar);
    if (sendBtn) sendBtn.addEventListener('click', enviar);
    if (input) {
        input.addEventListener('keydown', e => { if (e.key === 'Enter') enviar(); });
    }
})();
