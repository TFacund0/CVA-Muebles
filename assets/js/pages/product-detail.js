/**
 * CVA Muebles - Product Detail Scripts
 * Lógica para la galería de imágenes interactiva del detalle de producto.
 */

document.addEventListener('DOMContentLoaded', function() {
    initProductGallery();
});

function initProductGallery() {
    const mainImg = document.getElementById('main-product-img');
    const thumbItems = document.querySelectorAll('.thumb-item');
    const arrowLeft = document.querySelector('.gallery-arrow.arrow-left');
    const arrowRight = document.querySelector('.gallery-arrow.arrow-right');
    
    if (!mainImg || thumbItems.length === 0) return;

    // Construir la lista de imágenes a partir de las miniaturas
    const imagesList = Array.from(thumbItems).map(item => {
        const img = item.querySelector('img');
        return img ? img.src : '';
    }).filter(src => src !== '');

    let currentIndex = 0;

    // Función principal para cambiar la imagen
    window.changeMainImg = function(src, element) {
        currentIndex = imagesList.indexOf(src);
        if (currentIndex === -1) currentIndex = 0;
        
        // Transición suave
        mainImg.style.opacity = '0.4';
        mainImg.style.transform = 'scale(0.98)';
        
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
            mainImg.style.transform = 'scale(1)';
        }, 150);

        // Actualizar estado 'active' en miniaturas
        thumbItems.forEach(item => item.classList.remove('active'));
        
        if (element) {
            element.classList.add('active');
            element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else if (thumbItems[currentIndex]) {
            thumbItems[currentIndex].classList.add('active');
            thumbItems[currentIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    };

    // Función para navegación con flechas
    window.moveGallery = function(step) {
        currentIndex += step;
        if (currentIndex >= imagesList.length) currentIndex = 0;
        if (currentIndex < 0) currentIndex = imagesList.length - 1;
        
        changeMainImg(imagesList[currentIndex]);
    };

    // Asignar eventos de click a las miniaturas para quitar el onclick inline del HTML
    thumbItems.forEach(item => {
        item.addEventListener('click', function() {
            const img = this.querySelector('img');
            if (img) {
                changeMainImg(img.src, this);
            }
        });
    });

    // Asignar eventos a las flechas si existen
    if (arrowLeft) {
        arrowLeft.addEventListener('click', () => moveGallery(-1));
    }
    if (arrowRight) {
        arrowRight.addEventListener('click', () => moveGallery(1));
    }
}
