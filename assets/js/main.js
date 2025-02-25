// Inizializza Swiper
const swiper = new Swiper(".mySwiper", {
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
});

// Inizializza Rellax
const rellax = new Rellax('.rellax', {
    speed: -2,
    center: true,
    wrapper: null,
    round: true,
    vertical: true,
    horizontal: false
});

// Inizializza AOS
AOS.init({
    duration: 800,
    once: true,
});

// Animazione GSAP per la navbar
gsap.from(".navbar", {
    opacity: 0,
    y: -50,
    duration: 1,
    delay: 0.5,
});

// Animazioni GSAP per le sezioni
gsap.utils.toArray(".section").forEach(section => {
    gsap.from(section, {
        opacity: 0,
        y: 50,
        duration: 1,
        scrollTrigger: {
            trigger: section,
            start: "top 80%",
        },
    });
});

// Scroll fluido
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Navbar burger menu
document.addEventListener('DOMContentLoaded', () => {
    const burger = document.querySelector('.navbar-burger');
    const menu = document.querySelector('#navMenu');
    burger.addEventListener('click', () => {
        menu.classList.toggle('is-active');
    });
});

// Gestione dell'attivazione del menu: mantieni is-active dopo il click
document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.navbar-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            navItems.forEach(i => i.classList.remove('is-active'));
            this.classList.add('is-active');
        });
    });
});

// Funzione per impostare il link attivo in base all'URL (hash)
function setActiveLink() {
    const currentHash = window.location.hash;
    const navItems = document.querySelectorAll('.navbar-item');
    navItems.forEach(item => {
        // Se il valore href corrisponde allo hash corrente, aggiungi is-active
        if(item.getAttribute('href') === currentHash) {
            item.classList.add('is-active');
        } else {
            item.classList.remove('is-active');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Imposta subito in base all'URL
    setActiveLink();

    // Gestione del click sul menu
    const navItems = document.querySelectorAll('.navbar-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Se si naviga a una sezione, aggiorna lo hash
            // (in questo modo setActiveLink verrà richiamato anche su hashchange)
            navItems.forEach(i => i.classList.remove('is-active'));
            this.classList.add('is-active');
        });
    });
});

// Aggiorna al cambiamento dell'hash per mantenere attivo il link
window.addEventListener('hashchange', setActiveLink);

// Gestione del form della newsletter
document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const message = document.getElementById('newsletterMessage');
    message.classList.remove('is-hidden');
    setTimeout(() => {
        message.classList.add('is-hidden');
    }, 3000);
    this.reset();
});

// Gestione del cookie banner
const cookieBanner = document.getElementById('cookieBanner');
const acceptCookies = document.getElementById('acceptCookies');
const rejectCookies = document.getElementById('rejectCookies');

if (!localStorage.getItem('cookiesAccepted')) {
    cookieBanner.classList.add('is-active');
}

acceptCookies.addEventListener('click', () => {
    localStorage.setItem('cookiesAccepted', 'true');
    cookieBanner.classList.remove('is-active');
});

rejectCookies.addEventListener('click', () => {
    localStorage.setItem('cookiesAccepted', 'false');
    cookieBanner.classList.remove('is-active');
});

// Aggiungi funzione throttle
function throttle(func, limit) {
  let inThrottle;
  return function() {
    const args = arguments;
    const context = this;
    if (!inThrottle) {
      func.apply(context, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  }
}

// Gestione del pulsante "Torna su"
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', throttle(() => {
    if (window.scrollY > 300) {
        backToTop.style.display = 'flex';
    } else {
        backToTop.style.display = 'none';
    }
}, 200));

backToTop.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Gestione della sezione FAQ
document.addEventListener('DOMContentLoaded', () => {
    const faqMessages = document.querySelectorAll('.accordion .message');
    faqMessages.forEach(message => {
        message.querySelector('.message-header').addEventListener('click', () => {
            message.classList.toggle('is-active');
        });
    });
});

// Rimuovi il blocco che nasconde il preloader
/*
window.addEventListener('load', () => {
    const preloader = document.getElementById('preloader');
    if(preloader) {
        preloader.classList.add('hidden');
    }
});
*/

/*
// Modalità Light/Dark toggle
const themeToggle = document.getElementById('theme-toggle');
themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('light-theme');
    // Aggiorna il testo del bottone in base al tema attivo
    if(document.body.classList.contains('light-theme')) {
        themeToggle.textContent = 'Modo Dark';
        // Persisti preferenza, se necessario:
        localStorage.setItem('theme', 'light');
    } else {
        themeToggle.textContent = 'Modo Light';
        localStorage.setItem('theme', 'dark');
    }
});

// Imposta il tema salvato in localStorage al caricamento
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if(savedTheme === 'light') {
        document.body.classList.add('light-theme');
        themeToggle.textContent = 'Modo Dark';
    }
});
*/

// Migliorare le transizioni tra sezioni con GSAP se necessario (esempio base):
gsap.utils.toArray(".section").forEach(section => {
    gsap.from(section, {
        opacity: 0,
        y: 50,
        duration: 1.2,
        ease: "power3.out",
        scrollTrigger: {
            trigger: section,
            start: "top 80%"
        }
    });
});