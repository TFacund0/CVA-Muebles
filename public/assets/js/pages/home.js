/**
 * CVA Muebles - Home Scripts
 * Lógica para la página de inicio (ej. Carruseles Swiper)
 */

document.addEventListener('DOMContentLoaded', function() {
    initHomeSwiper();
});

function initHomeSwiper() {
    if (typeof Swiper !== 'undefined') {
        const swiperContainer = document.querySelector('.swiper-destacados');
        if (swiperContainer) {
            new Swiper('.swiper-destacados', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 1.2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                }
            });
        }
    } else {
        console.warn('Swiper library no está cargada.');
    }
}
