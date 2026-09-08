document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burger');
    const navMenu = document.getElementById('nav-menu');

    if (burger && navMenu) {
        burger.addEventListener('click', () => {
            navMenu.classList.toggle('nav-active');
            burger.classList.toggle('toggle');
        });

        // Cerrar el menú móvil al hacer clic en un enlace
        document.querySelectorAll('#nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('nav-active');
                burger.classList.remove('toggle');
            });
        });
    }

    function reveal() {
        const reveals = document.querySelectorAll('.reveal');
        const windowHeight = window.innerHeight;
        const elementVisible = 100;

        reveals.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', reveal);
    reveal();

    // ============================================
    // ED-AI ASISTENTE VIRTUAL
    // ============================================
    const modal = document.getElementById('welcome-modal');
    const modalBtn = document.getElementById('modal_ia');
    const chatWidget = document.querySelector('.chat-widget');
    const chatIcon = document.getElementById('chat-icon');
    const chatWindow = document.getElementById('chat-window');
    const closeChat = document.getElementById('close-chat');

    if (modal) {
        setTimeout(() => {
            modal.classList.add('active');
        }, 1000);
    }

    if (modalBtn && modal && chatWidget) {
        modalBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            setTimeout(() => {
                chatWidget.classList.add('active');
            }, 600);
        });
    }

    if (chatIcon && chatWidget) {
        chatIcon.addEventListener('click', () => {
            chatWidget.classList.add('active');
            if (chatWindow) {
                chatWindow.style.display = 'flex';
                setTimeout(() => {
                    chatWindow.classList.add('open');
                }, 10);
            }

            const dot = document.querySelector('.notification-dot');
            if (dot) dot.style.display = 'none';
        });
    }

    if (closeChat && chatWindow) {
        closeChat.addEventListener('click', (e) => {
            e.stopPropagation();
            chatWindow.classList.remove('open');
            setTimeout(() => {
                chatWindow.style.display = 'none';
            }, 300);
        });
    }
});
