const App = (() => {
    const state = {
        header: null,
        navToggle: null,
        navMenu: null,
        megaToggles: null,
        parallaxSections: [],
        lazyItems: [],
        toastStack: null,
        serviceData: null,
        modal: null,
        mapInstance: null,
        backToTop: null,
        closeMegaMenus: null,
        cartSidebar: null,
        cartSidebarPanel: null,
        cartSidebarToggles: null,
        iconPickers: null,
        closeNavMenu: null,
        topbarSearch: null,
        topbarSearchToggle: null,
        topbarSearchClose: null,
        topbarSearchInput: null,
        topbarSearchForm: null,
        chatbotRoot: null,
        chatbotToggle: null,
        chatbotPanel: null,
        chatbotMessages: null,
        chatbotForm: null,
        chatbotInput: null,
        chatbotFaqList: null,
        chatbotFaqs: [],
        chatbotTyping: null,
    };

    function init() {
        cacheDom();
        initPreloader();
        initNavbar();
        initTopbarSearch();
        initMegaMenu();
        initScrollLinks();
        initReveal();
        initHeroTimeline();
        initLazyMedia();
        initParallax();
        initRipple();
        initCardTilt();
        initModals();
        initReviewsSlider();
        initMap();
        initContactForm();
        initToasts();
        initBackToTop();
        initCartSidebar();
        initAnnouncementIconPicker();
        initAdminNewsSlider();
        initChatbot();
    }

    function initTopbarSearch() {
        const header = state.header;
        const toggle = state.topbarSearchToggle;
        const closeBtn = state.topbarSearchClose;
        const wrapper = state.topbarSearch;
        const input = state.topbarSearchInput;
        const form = state.topbarSearchForm;
        if (!header || !toggle || !wrapper) {
            return;
        }

        const setOpen = open => {
            header.classList.toggle('is-search-open', open);
            document.body.classList.toggle('topbar-search-open', open);
            toggle.setAttribute('aria-expanded', String(open));
            if (open) {
                state.closeNavMenu?.();
                state.closeMegaMenus?.();
                setTimeout(() => input?.focus(), 80);
            } else {
                input?.blur();
            }
        };

        toggle.addEventListener('click', () => {
            const wantsOpen = !header.classList.contains('is-search-open');
            setOpen(wantsOpen);
        });

        closeBtn?.addEventListener('click', () => setOpen(false));

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && header.classList.contains('is-search-open')) {
                setOpen(false);
            }
        });

        document.addEventListener('click', event => {
            if (!header.classList.contains('is-search-open')) return;
            if (wrapper.contains(event.target) || toggle.contains(event.target)) return;
            setOpen(false);
        });

        form?.addEventListener('submit', () => setOpen(false));
    }

    function cacheDom() {
        state.header = document.getElementById('site-header');
        state.navToggle = document.querySelector('.nav-toggle');
        state.navMenu = document.getElementById('nav-menu');
        state.megaToggles = document.querySelectorAll('[data-mega-toggle]');
        state.parallaxSections = document.querySelectorAll('[data-parallax]');
        state.lazyItems = document.querySelectorAll('[data-src]');
        state.toastStack = document.getElementById('ap-toast-stack');
        state.modal = document.getElementById('service-modal');
        state.backToTop = document.getElementById('back-to-top');
        state.cartSidebar = document.getElementById('cart-sidebar');
        state.cartSidebarPanel = state.cartSidebar?.querySelector('.cart-sidebar__panel') ?? null;
        state.cartSidebarToggles = document.querySelectorAll('[data-cart-sidebar-open]');
        state.iconPickers = document.querySelectorAll('[data-announcement-icon-picker]');
        state.topbarSearch = document.querySelector('[data-topbar-search]');
        state.topbarSearchToggle = document.querySelector('[data-topbar-search-toggle]');
        state.topbarSearchClose = document.querySelector('[data-topbar-search-close]');
        state.topbarSearchInput = document.querySelector('[data-topbar-search-input]');
        state.topbarSearchForm = document.querySelector('[data-topbar-search-form]');
        state.chatbotRoot = document.querySelector('[data-chatbot]');
        state.chatbotToggle = state.chatbotRoot?.querySelector('[data-chatbot-toggle]') ?? null;
        state.chatbotPanel = document.getElementById('ap-chatbot-panel');
        state.chatbotMessages = state.chatbotRoot?.querySelector('[data-chatbot-messages]') ?? null;
        state.chatbotForm = state.chatbotRoot?.querySelector('[data-chatbot-form]') ?? null;
        state.chatbotInput = state.chatbotRoot?.querySelector('[data-chatbot-input]') ?? null;
        state.chatbotFaqList = state.chatbotRoot?.querySelector('[data-chatbot-faq-list]') ?? null;
    }

    /* Preloader */
    function initPreloader() {
        const preloadEl = document.getElementById('ap-preloader');
        if (!preloadEl) return;
        window.addEventListener('load', () => {
            requestAnimationFrame(() => {
                preloadEl.classList.add('is-hidden');
                setTimeout(() => preloadEl.remove(), 800);
            });
        });
    }

    function initCartSidebar() {
        const sidebar = state.cartSidebar;
        if (!sidebar) return;
        const toggles = state.cartSidebarToggles || [];
        const closers = sidebar.querySelectorAll('[data-cart-sidebar-close]');
        const panel = state.cartSidebarPanel;

        const setOpen = open => {
            sidebar.classList.toggle('is-open', open);
            sidebar.setAttribute('aria-hidden', open ? 'false' : 'true');
            document.body.classList.toggle('cart-sidebar-open', open);
            toggles.forEach?.(btn => btn.setAttribute('aria-expanded', open ? 'true' : 'false'));
            if (open) {
                panel?.focus({ preventScroll: true });
            }
        };

        // Check URL parameter to keep sidebar open after reload
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('cart_open') === '1') {
            setOpen(true);
        }

        toggles.forEach?.(btn => {
            btn.addEventListener('click', () => {
                const open = !sidebar.classList.contains('is-open');
                setOpen(open);
            });
        });

        closers.forEach(btn => btn.addEventListener('click', () => setOpen(false)));

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && sidebar.classList.contains('is-open')) {
                setOpen(false);
            }
        });
    }

    function initAnnouncementIconPicker() {
        const pickers = state.iconPickers;
        if (!pickers || !pickers.length) return;
        pickers.forEach(picker => {
            const select = picker.querySelector('[data-icon-select]');
            const button = picker.querySelector('[data-insert-icon]');
            const targetSelector = picker.getAttribute('data-target') || '#announcement_message';
            const target = document.querySelector(targetSelector);
            if (!select || !button || !target) {
                return;
            }
            const insertIcon = () => {
                const symbol = select.value;
                if (!symbol) return;
                const snippet = `<span data-icon="${symbol}" aria-hidden="true">${symbol}</span> `;
                insertTextAtCursor(target, snippet);
                target.focus();
            };
            button.addEventListener('click', event => {
                event.preventDefault();
                insertIcon();
            });
        });
    }

    function initAdminNewsSlider() {
        const sliders = document.querySelectorAll('[data-news-slider]');
        if (!sliders.length) {
            return;
        }

        sliders.forEach(slider => {
            const viewport = slider.querySelector('[data-news-slider-viewport]');
            const prevBtn = slider.querySelector('[data-news-slider-prev]');
            const nextBtn = slider.querySelector('[data-news-slider-next]');
            if (!viewport || !prevBtn || !nextBtn) {
                return;
            }

            const scrollByStep = direction => {
                const step = viewport.clientWidth;
                viewport.scrollBy({ left: step * direction, behavior: 'smooth' });
            };

            const updateNav = () => {
                const maxScroll = viewport.scrollWidth - viewport.clientWidth;
                const hasOverflow = maxScroll > 1;
                slider.classList.toggle('is-scrollable', hasOverflow);
                if (!hasOverflow) {
                    prevBtn.disabled = true;
                    nextBtn.disabled = true;
                    return;
                }
                const tolerance = 4;
                prevBtn.disabled = viewport.scrollLeft <= tolerance;
                nextBtn.disabled = viewport.scrollLeft >= maxScroll - tolerance;
            };

            prevBtn.addEventListener('click', () => scrollByStep(-1));
            nextBtn.addEventListener('click', () => scrollByStep(1));
            viewport.addEventListener('scroll', () => requestAnimationFrame(updateNav), { passive: true });
            window.addEventListener('resize', () => requestAnimationFrame(updateNav));

            updateNav();
        });
    }

    function insertTextAtCursor(field, text) {
        const start = field.selectionStart ?? field.value.length;
        const end = field.selectionEnd ?? field.value.length;
        const before = field.value.slice(0, start);
        const after = field.value.slice(end);
        field.value = before + text + after;
        const newPos = start + text.length;
        if (typeof field.selectionStart === 'number') {
            field.selectionStart = field.selectionEnd = newPos;
        }
        field.dispatchEvent(new Event('input', { bubbles: true }));
    }

    /* Navbar + progress */
    function initNavbar() {
        const updateHeader = () => {
            if (!state.header) return;
            const sticky = window.scrollY > 40;
            state.header.classList.toggle('is-sticky', sticky);
        };

        const updateProgress = () => {
            if (!state.header) return;
            const doc = document.documentElement;
            const max = doc.scrollHeight - window.innerHeight;
            const progress = max > 0 ? window.scrollY / max : 0;
            state.header.style.setProperty('--progress', progress.toFixed(3));
        };

        updateHeader();
        updateProgress();
        window.addEventListener('scroll', () => {
            updateHeader();
            updateProgress();
            handleParallax();
            toggleBackToTop();
        });

        if (state.navToggle && state.navMenu) {
            const setNavOpen = open => {
                state.navMenu.classList.toggle('open', open);
                state.navToggle.classList.toggle('open', open);
                state.navToggle.setAttribute('aria-expanded', String(open));
                document.body.classList.toggle('nav-open', open);
                if (!open) {
                    state.closeMegaMenus?.();
                }
            };

            state.closeNavMenu = () => setNavOpen(false);

            state.navToggle.addEventListener('click', () => {
                const open = !state.navMenu.classList.contains('open');
                setNavOpen(open);
            });

            state.navMenu.addEventListener('click', event => {
                if (event.target.closest('a')) {
                    setNavOpen(false);
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 992 && state.navMenu.classList.contains('open')) {
                    setNavOpen(false);
                }
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    setNavOpen(false);
                }
            });
        }
    }

    /* Smooth scroll */
    function initScrollLinks() {
        document.querySelectorAll('[data-scroll]').forEach(link => {
            link.addEventListener('click', event => {
                const url = new URL(link.href, window.location.href);
                const targetId = url.hash;
                if (!targetId) return;
                const page = new URL(window.location.href).searchParams.get('page') || 'home';
                const targetPage = url.searchParams.get('page') || page;
                if (page !== targetPage) {
                    return;
                }
                const target = document.querySelector(targetId);
                if (!target) return;
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                state.navMenu?.classList.remove('open');
                state.navToggle?.classList.remove('open');
                state.navToggle?.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('nav-open');
                state.closeMegaMenus?.();
            });
        });
    }

    /* Mega menu */
    function initMegaMenu() {
        const toggles = state.megaToggles;
        if (!toggles || !toggles.length) return;

        const closeAll = () => {
            toggles.forEach(toggle => {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.closest('.nav-item--mega')?.classList.remove('is-open');
            });
        };

        const desktopHover = () => window.matchMedia('(hover: hover)').matches && window.innerWidth > 992;

        toggles.forEach(toggle => {
            const item = toggle.closest('.nav-item--mega');
            if (!item) return;

            item.addEventListener('mouseenter', () => {
                if (!desktopHover()) return;
                closeAll();
                toggle.setAttribute('aria-expanded', 'true');
                item.classList.add('is-open');
            });

            item.addEventListener('mouseleave', () => {
                if (!desktopHover()) return;
                toggle.setAttribute('aria-expanded', 'false');
                item.classList.remove('is-open');
            });

            toggle.addEventListener('click', event => {
                if (desktopHover()) return;
                event.preventDefault();
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                closeAll();
                if (!expanded) {
                    toggle.setAttribute('aria-expanded', 'true');
                    item.classList.add('is-open');
                }
            });
        });

        document.addEventListener('click', event => {
            if (!event.target.closest('.nav-item--mega')) {
                closeAll();
            }
        });

        state.closeMegaMenus = closeAll;
    }

    /* Reveal animations */
    function initReveal() {
        const items = document.querySelectorAll('[data-reveal]');
        if (!items.length) return;
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2, rootMargin: '0px 0px -10% 0px' });
        items.forEach(item => observer.observe(item));
    }

    /* Hero timeline */
    function initHeroTimeline() {
        const hero = document.getElementById('hero');
        if (!hero) return;
        const steps = [
            hero.querySelector('[data-animate="hero-text"]'),
            hero.querySelector('[data-animate="hero-panel"]'),
            hero.querySelector('[data-animate="hero-visual"]')
        ].filter(Boolean);

        const play = () => {
            steps.forEach((el, index) => {
                setTimeout(() => el.classList.add('is-played'), index * 220);
            });
        };

        const observer = new IntersectionObserver(entries => {
            if (entries[0]?.isIntersecting) {
                play();
                observer.disconnect();
            }
        }, { threshold: 0.6 });
        observer.observe(hero);
    }

    /* Lazy media */
    function initLazyMedia() {
        if (!state.lazyItems.length) return;
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const src = el.getAttribute('data-src');
                if (src) {
                    el.src = src;
                    el.addEventListener('load', () => el.classList.add('is-loaded'), { once: true });
                    el.removeAttribute('data-src');
                }
                observer.unobserve(el);
            });
        }, { threshold: 0.3 });
        state.lazyItems.forEach(item => observer.observe(item));
    }

    /* Parallax */
    function initParallax() {
        handleParallax();
    }

    function handleParallax() {
        if (!state.parallaxSections.length) return;
        const scrollTop = window.scrollY;
        state.parallaxSections.forEach(section => {
            section.style.backgroundPositionY = `${scrollTop * 0.3}px`;
        });
    }

    /* Ripple */
    function initRipple() {
        document.body.addEventListener('pointerdown', event => {
            const target = event.target.closest('[data-ripple]');
            if (!target) return;
            const rect = target.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'ripple-wave';
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${event.clientY - rect.top - size / 2}px`;
            target.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    }

    /* Card tilt */
    function initCardTilt() {
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('pointermove', event => {
                if (event.pointerType && event.pointerType !== 'mouse') return;
                const rect = card.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width - 0.5) * 6;
                const y = ((event.clientY - rect.top) / rect.height - 0.5) * -6;
                card.style.transform = `rotateX(${y}deg) rotateY(${x}deg)`;
            });
            card.addEventListener('pointerleave', () => {
                card.style.transform = '';
            });
        });
    }

    /* Modals + JSON */
    function initModals() {
        if (!state.modal) return;
        document.addEventListener('click', async event => {
            const trigger = event.target.closest('[data-service-key]');
            const button = event.target.closest('.service-more');
            const modalOpen = event.target.closest('[data-modal-open]');
            if (trigger) {
                const key = trigger.getAttribute('data-service-key');
                await openServiceModal(key);
            } else if (button) {
                const parent = button.closest('[data-service-key]');
                if (!parent) return;
                const key = parent.getAttribute('data-service-key');
                await openServiceModal(key);
            } else if (modalOpen) {
                const modalType = modalOpen.getAttribute('data-modal-open');
                if (modalType === 'login') {
                    openLoginModal();
                }
            }
            if (event.target.matches('[data-modal-close]')) {
                closeModal();
            }
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
        // Close modal on overlay click for all modals
        document.addEventListener('click', event => {
            if (event.target.classList.contains('ap-modal')) {
                closeModal();
            }
        });
    }

    async function openServiceModal(key) {
        if (!key || !state.modal) return;
        const data = await loadServiceData();
        const info = data[key];
        if (!info) return;
        state.modal.querySelector('#service-modal-title').textContent = info.title;
        state.modal.querySelector('#service-modal-body').textContent = info.body;
        state.modal.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modals = document.querySelectorAll('.ap-modal');
        modals.forEach(modal => modal.classList.remove('is-visible'));
        document.body.style.overflow = '';
    }

    function openLoginModal() {
        const loginModal = document.getElementById('login-modal');
        if (!loginModal) return;
        loginModal.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
    }

    async function loadServiceData() {
        if (state.serviceData) return state.serviceData;
        try {
            const response = await fetch('assets/data/services.json');
            state.serviceData = await response.json();
        } catch (error) {
            showToast('Impossibile caricare i dettagli servizio', 'error');
            state.serviceData = {};
        }
        return state.serviceData;
    }

    /* Reviews slider */
    function initReviewsSlider() {
        const sliders = document.querySelectorAll('[data-slider]');
        if (!sliders.length) return;

        sliders.forEach(slider => {
            const track = slider.querySelector('.reviews-track');
            const slides = track ? [...track.children] : [];
            const dotsContainer = slider.querySelector('[data-slider-dots]');
            const prev = slider.querySelector('[data-slider-prev]');
            const next = slider.querySelector('[data-slider-next]');
            if (!track || !slides.length || !dotsContainer) return;
            let index = 0;
            let interval;

            const buildDots = () => {
                slides.forEach((_, i) => {
                    const dot = document.createElement('button');
                    dot.addEventListener('click', () => {
                        index = i;
                        update();
                        restartAuto();
                    });
                    dotsContainer.appendChild(dot);
                });
            };

            const update = () => {
                track.style.transform = `translateX(-${index * 100}%)`;
                [...dotsContainer.children].forEach((dot, i) => {
                    dot.classList.toggle('is-active', i === index);
                });
            };

            const nextSlide = () => {
                index = (index + 1) % slides.length;
                update();
            };

            const prevSlide = () => {
                index = (index - 1 + slides.length) % slides.length;
                update();
            };

            const restartAuto = () => {
                clearInterval(interval);
                interval = setInterval(nextSlide, 6000);
            };

            buildDots();
            update();
            interval = setInterval(nextSlide, 6000);
            prev?.addEventListener('click', () => { prevSlide(); restartAuto(); });
            next?.addEventListener('click', () => { nextSlide(); restartAuto(); });
            slider.addEventListener('pointerenter', () => clearInterval(interval));
            slider.addEventListener('pointerleave', restartAuto);
        });
    }

    /* MapLibre */
    function initMap() {
        const mapContainer = document.querySelector('[data-map]');
        if (!mapContainer || typeof maplibregl === 'undefined') return;
        const lat = parseFloat(mapContainer.dataset.mapLat) || 41.9109;
        const lng = parseFloat(mapContainer.dataset.mapLng) || 12.4768;
        state.mapInstance = new maplibregl.Map({
            container: mapContainer,
            style: 'https://demotiles.maplibre.org/style.json',
            center: [lng, lat],
            zoom: 13
        });
        state.mapInstance.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'top-right');
        state.mapInstance.on('load', () => {
            const markerEl = document.createElement('div');
            markerEl.className = 'map-marker';
            new maplibregl.Marker({ element: markerEl }).setLngLat([lng, lat]).addTo(state.mapInstance);
        });
    }

    /* Contact form */
    function initContactForm() {
        const form = document.querySelector('[data-contact-form]');
        if (!form) return;
        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (!validateForm(form)) {
                showToast('Controlla i campi obbligatori', 'error');
                return;
            }
            const feedback = form.querySelector('[data-form-feedback]');
            if (feedback) {
                feedback.textContent = 'Invio in corso…';
            }
            const formData = new FormData(form);
            try {
                const formAction = `${window.location.origin}${window.location.pathname}${window.location.search}`;
                const response = await fetch(formAction, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                const data = await response.json();
                if (feedback) {
                    feedback.textContent = data.message || '';
                }
                if (data.csrf_token) {
                    const tokenField = form.querySelector('[name="csrf_token"]');
                    if (tokenField) {
                        tokenField.value = data.csrf_token;
                        tokenField.defaultValue = data.csrf_token;
                    }
                }
                if (data.success) {
                    form.reset();
                    showToast('Richiesta inviata', 'success');
                } else {
                    showToast(data.message || 'Errore durante l\'invio', 'error');
                }
            } catch (error) {
                if (feedback) {
                    feedback.textContent = 'Errore di rete.';
                }
                showToast('Errore di rete', 'error');
            }
        });
    }

    function validateForm(form) {
        const fields = form.elements;
        const nameField = fields.namedItem('name');
        const emailField = fields.namedItem('email');
        const messageField = fields.namedItem('message');
        const honeypotField = fields.namedItem('company_website');
        const name = nameField?.value.trim() || '';
        const email = emailField?.value.trim() || '';
        const message = messageField?.value.trim() || '';
        const honeypot = honeypotField?.value || '';
        if (honeypot) return false;
        if (!name || name.length < 2) return false;
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return false;
        if (message.length < 5) return false;
        return true;
    }

    /* Toasts */
    function initToasts() {
        const feedback = window.__AP_PAGE_META__?.formFeedback;
        if (feedback?.message) {
            showToast(feedback.message, feedback.success ? 'success' : 'error');
        }
        const serverToasts = window.__AP_PAGE_META__?.toasts || [];
        serverToasts.forEach(entry => {
            if (entry?.message) {
                showToast(entry.message, entry.type || 'info');
            }
        });
    }

    function showToast(message, type = 'info') {
        if (!state.toastStack || !message) return;
        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;

        const body = document.createElement('div');
        body.className = 'toast__body';
        body.textContent = message;

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'toast__close';
        closeBtn.setAttribute('aria-label', 'Chiudi');
        closeBtn.textContent = '×';

        const progress = document.createElement('div');
        progress.className = 'toast__progress';
        const bar = document.createElement('span');
        bar.style.animationDuration = '4s';
        progress.appendChild(bar);

        closeBtn.addEventListener('click', () => toast.remove());

        toast.append(body, closeBtn, progress);
        state.toastStack.appendChild(toast);
        setTimeout(() => toast.remove(), 4500);
    }

    /* Back to top */
    function initBackToTop() {
        if (!state.backToTop) return;
        state.backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        toggleBackToTop();
    }

    function toggleBackToTop() {
        if (!state.backToTop) return;
        state.backToTop.classList.toggle('is-visible', window.scrollY > 500);
    }

    /* Chatbot */
    function initChatbot() {
        const root = state.chatbotRoot;
        if (!root) return;
        const payload = window.__AP_CHATBOT__?.faqs;
        if (!Array.isArray(payload) || !payload.length) {
            root.classList.add('is-disabled');
            return;
        }

        state.chatbotFaqs = payload.map(faq => ({
            ...faq,
            questionNorm: normalizeText(faq.question),
            answerNorm: normalizeText(faq.answer),
            keywordsNorm: (faq.keywords || []).map(normalizeText),
        }));

        const toggle = state.chatbotToggle;
        const closeBtn = root.querySelector('[data-chatbot-close]');
        const panel = state.chatbotPanel;
        const form = state.chatbotForm;
        const input = state.chatbotInput;
        const faqList = state.chatbotFaqList;
        const filters = root.querySelectorAll('[data-chatbot-filter]');
        const shortcuts = root.querySelectorAll('[data-chatbot-question]');
        const messages = state.chatbotMessages;
        if (!toggle || !panel || !form || !input || !messages) {
            return;
        }

        const setOpen = open => {
            root.classList.toggle('is-open', open);
            panel.setAttribute('aria-hidden', open ? 'false' : 'true');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            const lockScroll = window.innerWidth <= 600;
            document.body.classList.toggle('chatbot-open', open && lockScroll);
            if (open) {
                requestAnimationFrame(() => input.focus());
            }
        };

        toggle.addEventListener('click', () => {
            const wantsOpen = !root.classList.contains('is-open');
            setOpen(wantsOpen);
        });

        closeBtn?.addEventListener('click', () => setOpen(false));

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && root.classList.contains('is-open')) {
                setOpen(false);
            }
        });

        document.addEventListener('click', event => {
            if (!root.contains(event.target) && root.classList.contains('is-open')) {
                setOpen(false);
            }
        });

        form.addEventListener('submit', event => {
            event.preventDefault();
            const question = input.value.trim();
            if (!question) return;
            handleChatbotQuestion(question);
            input.value = '';
        });

        faqList?.addEventListener('click', event => {
            const button = event.target.closest('[data-chatbot-faq]');
            if (!button) return;
            const question = button.getAttribute('data-question');
            if (question) {
                handleChatbotQuestion(question);
            }
        });

        shortcuts.forEach(button => {
            button.addEventListener('click', () => {
                const question = button.getAttribute('data-chatbot-question');
                if (question) {
                    handleChatbotQuestion(question);
                }
            });
        });

        filters.forEach(button => {
            button.addEventListener('click', () => {
                filters.forEach(ctrl => ctrl.classList.toggle('is-active', ctrl === button));
                const category = button.getAttribute('data-chatbot-filter') || 'all';
                filterChatbotFaqList(category);
            });
        });

        filterChatbotFaqList('all');
        scrollChatbotToBottom();
    }

    function handleChatbotQuestion(rawQuestion) {
        const question = rawQuestion.trim();
        if (!question) return;
        appendChatbotMessage(question, 'user');
        const typing = showChatbotTyping();
        const delay = 450 + Math.random() * 400;
        setTimeout(() => {
            hideChatbotTyping(typing);
            const match = findBestFaq(question);
            if (match) {
                appendChatbotMessage(match.answer, 'bot', { category: match.category });
            } else {
                appendChatbotMessage('Non ho trovato una risposta precisa, ma il nostro team può aiutarti via modulo di contatto o telefono.', 'bot');
            }
        }, delay);
    }

    function filterChatbotFaqList(category) {
        const list = state.chatbotFaqList;
        if (!list) return;
        const target = category === 'all' ? null : category;
        list.querySelectorAll('[data-chatbot-faq]').forEach(item => {
            if (!target) {
                item.hidden = false;
                return;
            }
            item.hidden = item.getAttribute('data-category') !== target;
        });
    }

    function appendChatbotMessage(text, role = 'bot', meta = {}) {
        const messages = state.chatbotMessages;
        if (!messages) return;
        const wrapper = document.createElement('div');
        wrapper.className = `ap-chatbot__message ap-chatbot__message--${role}`;
        const body = document.createElement('p');
        body.textContent = text;
        wrapper.appendChild(body);
        if (meta?.category) {
            const small = document.createElement('small');
            small.textContent = `Categoria: ${meta.category}`;
            wrapper.appendChild(small);
        }
        messages.appendChild(wrapper);
        scrollChatbotToBottom();
    }

    function showChatbotTyping() {
        const messages = state.chatbotMessages;
        if (!messages) return null;
        const indicator = document.createElement('div');
        indicator.className = 'ap-chatbot__message ap-chatbot__message--bot is-typing';
        indicator.innerHTML = '<span class="ap-chatbot__typing-dot"></span><span class="ap-chatbot__typing-dot"></span><span class="ap-chatbot__typing-dot"></span>';
        messages.appendChild(indicator);
        scrollChatbotToBottom();
        state.chatbotTyping = indicator;
        return indicator;
    }

    function hideChatbotTyping(indicator) {
        const target = indicator || state.chatbotTyping;
        target?.remove();
        state.chatbotTyping = null;
    }

    function scrollChatbotToBottom() {
        const messages = state.chatbotMessages;
        if (!messages) return;
        messages.scrollTop = messages.scrollHeight;
    }

    function findBestFaq(question) {
        if (!state.chatbotFaqs?.length) return null;
        const normalized = normalizeText(question);
        if (!normalized) return null;
        let best = null;
        state.chatbotFaqs.forEach(faq => {
            const score = scoreFaqEntry(faq, normalized);
            if (!best || score > best.score) {
                best = { faq, score };
            }
        });
        if (!best || best.score < 25) {
            return null;
        }
        return best.faq;
    }

    function scoreFaqEntry(faq, query) {
        if (!query) return 0;
        let score = 0;
        if (faq.questionNorm === query) score += 60;
        if (faq.questionNorm.includes(query)) score += 35;
        const queryWords = query.split(' ').filter(word => word.length > 3);
        const questionWords = new Set(faq.questionNorm.split(' ').filter(word => word.length > 3));
        const wordMatches = queryWords.filter(word => questionWords.has(word)).length;
        score += wordMatches * 6;
        const keywordMatches = (faq.keywordsNorm || []).filter(keyword => keyword && query.includes(keyword)).length;
        score += Math.min(keywordMatches * 12, 36);
        if (faq.answerNorm.includes(query)) score += 10;
        score += stringSimilarity(faq.questionNorm, query) * 35;
        return score;
    }

    function stringSimilarity(a, b) {
        if (!a || !b) return 0;
        const distance = levenshtein(a, b);
        const maxLen = Math.max(a.length, b.length) || 1;
        return 1 - distance / maxLen;
    }

    function levenshtein(a, b) {
        const rows = b.length + 1;
        const cols = a.length + 1;
        const matrix = Array.from({ length: rows }, () => new Array(cols).fill(0));
        for (let i = 0; i < rows; i += 1) {
            matrix[i][0] = i;
        }
        for (let j = 0; j < cols; j += 1) {
            matrix[0][j] = j;
        }
        for (let i = 1; i < rows; i += 1) {
            for (let j = 1; j < cols; j += 1) {
                if (b.charAt(i - 1) === a.charAt(j - 1)) {
                    matrix[i][j] = matrix[i - 1][j - 1];
                } else {
                    const insertion = matrix[i][j - 1] + 1;
                    const deletion = matrix[i - 1][j] + 1;
                    const substitution = matrix[i - 1][j - 1] + 1;
                    matrix[i][j] = Math.min(insertion, deletion, substitution);
                }
            }
        }
        return matrix[rows - 1][cols - 1];
    }

    function normalizeText(value) {
        if (!value) return '';
        return value
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    return { init };
})();

// Cookie Banner Management
const CookieBanner = (() => {
    const COOKIE_NAME = 'ap_cookie_consent';
    const COOKIE_VALUE_ACCEPT = 'accepted';
    const COOKIE_VALUE_REJECT = 'rejected';
    const COOKIE_EXPIRY_DAYS = 365;

    let banner = null;
    let acceptBtn = null;
    let rejectBtn = null;

    function init() {
        banner = document.querySelector('.cookie-banner');
        if (!banner) return;

        acceptBtn = banner.querySelector('.ap-btn--primary');
        rejectBtn = banner.querySelector('.ap-btn--ghost');

        if (!getCookieConsent()) {
            showBanner();
        }

        bindEvents();
    }

    function bindEvents() {
        if (acceptBtn) {
            acceptBtn.addEventListener('click', handleAccept);
        }
        if (rejectBtn) {
            rejectBtn.addEventListener('click', handleReject);
        }
    }

    function showBanner() {
        if (banner) {
            banner.classList.add('show');
        }
    }

    function hideBanner() {
        if (banner) {
            banner.classList.remove('show');
        }
    }

    function handleAccept() {
        setCookieConsent(COOKIE_VALUE_ACCEPT);
        hideBanner();
        // Enable GA if accepted
        if (window.gtag) {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    }

    function handleReject() {
        setCookieConsent(COOKIE_VALUE_REJECT);
        hideBanner();
        // Disable GA if rejected
        if (window.gtag) {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    }

    function setCookieConsent(value) {
        const expiryDate = new Date();
        expiryDate.setTime(expiryDate.getTime() + (COOKIE_EXPIRY_DAYS * 24 * 60 * 60 * 1000));
        document.cookie = `${COOKIE_NAME}=${value}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
    }

    function getCookieConsent() {
        const name = COOKIE_NAME + '=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookies = decodedCookie.split(';');
        for (let cookie of cookies) {
            cookie = cookie.trim();
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length);
            }
        }
        return null;
    }

    // Public methods for onclick handlers
    function accept() {
        handleAccept();
    }

    function reject() {
        handleReject();
    }

    return { init, accept, reject };
})();

document.addEventListener('DOMContentLoaded', () => {
    App.init();
    CookieBanner.init();
});
