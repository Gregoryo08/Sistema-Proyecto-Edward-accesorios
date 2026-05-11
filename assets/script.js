const navSlide = () => {
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-links');
    const navLinks = document.querySelectorAll('.nav-links li');

    burger.addEventListener('click', () => {
       
        nav.classList.toggle('nav-active');
        
        
        burger.classList.toggle('toggle');
    });

  
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            nav.classList.remove('nav-active');
            burger.classList.remove('toggle');
        });
    });
}

const scrollAppear = () => {
    const reveals = document.querySelectorAll('.reveal');
    
    reveals.forEach(reveal => {
        const windowHeight = window.innerHeight;
        const revealTop = reveal.getBoundingClientRect().top;
        const revealPoint = 150;

        if (revealTop < windowHeight - revealPoint) {
            reveal.classList.add('active');
        }
    });
}


window.addEventListener('scroll', scrollAppear);
window.addEventListener('load', () => {
    navSlide();
    scrollAppear(); 
});








// ia





document.addEventListener('DOMContentLoaded', () => {
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

    if (modalBtn) {
        modalBtn.addEventListener('click', () => {
            modal.classList.remove('active');
            setTimeout(() => {
                chatWidget.classList.add('active');
            }, 600);
        });
    }

    if (chatIcon) {
        chatIcon.addEventListener('click', () => {
            chatWindow.style.display = 'flex';
            setTimeout(() => {
                chatWindow.classList.add('open');
            }, 10);
            
            const dot = document.querySelector('.notification-dot');
            if (dot) dot.style.display = 'none';
        });
    }

    if (closeChat) {
        closeChat.addEventListener('click', (e) => {
            e.stopPropagation();
            chatWindow.classList.remove('open');
            setTimeout(() => {
                chatWindow.style.display = 'none';
            }, 300);
        });
    }
});