document.addEventListener('DOMContentLoaded', () => {
    const sidebarLinks = document.querySelectorAll('.admin-sidebar__menu a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            sidebarLinks.forEach(item => item.classList.remove('is-active'));
            link.classList.add('is-active');
        });
    });

    const toasts = document.querySelectorAll('.admin-toast');
    if (toasts.length) {
        setTimeout(() => {
            toasts.forEach(toast => toast.classList.add('is-hidden'));
        }, 3500);
    }

    // Slider functionality
    const sliders = document.querySelectorAll('[data-news-slider]');
    sliders.forEach(slider => {
        const track = slider.querySelector('[data-news-slider-viewport] .admin-news-slider__track');
        const slides = slider.querySelectorAll('.admin-news-slide');
        const prevBtn = slider.querySelector('[data-news-slider-prev]');
        const nextBtn = slider.querySelector('[data-news-slider-next]');

        if (!track || slides.length === 0) return;

        let currentIndex = 0;

        const updateSlider = () => {
            const translateX = -currentIndex * 100;
            track.style.transform = `translateX(${translateX}%)`;
            prevBtn.disabled = currentIndex === 0;
            nextBtn.disabled = currentIndex === slides.length - 1;
        };

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < slides.length - 1) {
                currentIndex++;
                updateSlider();
            }
        });

        updateSlider(); // Initial state
    });
});
