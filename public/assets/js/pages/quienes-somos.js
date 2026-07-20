document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-bg-image').forEach(function(el) {
        if (el.dataset.bg) {
            el.style.backgroundImage = "url('" + el.dataset.bg + "')";
        }
    });
});
