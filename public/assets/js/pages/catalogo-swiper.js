(function() {
    function initSwiper() {
        const container = document.querySelector('.swiper-destacados');
        if (!container) return;

        if (typeof Swiper !== 'undefined') {
            const slideCount = container.querySelectorAll('.swiper-slide').length;
            // El modo loop de Swiper necesita más slides que columnas visibles
            // en el breakpoint más ancho (3); si hay menos, lo desactivamos
            // para que no rompa la navegación con pocos productos cargados.
            const canLoop = slideCount > 3;

            const swiper = new Swiper('.swiper-destacados', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: canLoop,
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
        } else {
            console.error('Swiper is not defined');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSwiper);
    } else {
        initSwiper();
    }
})();
