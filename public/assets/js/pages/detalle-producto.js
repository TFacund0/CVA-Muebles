document.addEventListener('DOMContentLoaded', function() {
    const mainImg = document.getElementById('main-product-img');
    if (!mainImg) return;

    // Lista de todas las imágenes (Principal + Galería), tomada de los thumbnails renderizados.
    const thumbs = document.querySelectorAll('.thumb-item');
    const imagesList = Array.from(thumbs).map(el => el.dataset.img);

    let currentIndex = 0;

    function changeMainImg(src, element) {
        currentIndex = imagesList.indexOf(src);

        // Transición suave
        mainImg.style.opacity = '0.4';
        mainImg.style.transform = 'scale(0.98)';

        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
            mainImg.style.transform = 'scale(1)';
        }, 150);

        // Actualizar miniaturas
        document.querySelectorAll('.thumb-item').forEach(item => {
            item.classList.remove('active');
        });

        const thumbEls = document.querySelectorAll('.thumb-item');
        if (element) {
            element.classList.add('active');
            element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        } else if (thumbEls[currentIndex]) {
            thumbEls[currentIndex].classList.add('active');
            thumbEls[currentIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function moveGallery(step) {
        currentIndex += step;
        if (currentIndex >= imagesList.length) currentIndex = 0;
        if (currentIndex < 0) currentIndex = imagesList.length - 1;

        changeMainImg(imagesList[currentIndex]);
    }

    thumbs.forEach(el => {
        el.addEventListener('click', function() {
            changeMainImg(this.dataset.img, this);
        });
    });

    document.querySelectorAll('.js-gallery-move').forEach(btn => {
        btn.addEventListener('click', function() {
            moveGallery(parseInt(this.dataset.step, 10));
        });
    });
});
