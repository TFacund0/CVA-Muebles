/**
 * CVA Muebles - Admin Products Scripts
 * Lógica para Alta y Edición de Productos (Preview de imágenes, Cálculo de Margen).
 */

document.addEventListener('DOMContentLoaded', function() {
    initProductForms();
});

function initProductForms() {
    // 1. Preview de Imagen (Alta Producto)
    const imageInputAlta = document.getElementById('image');
    const previewAlta = document.getElementById('preview-image');
    const placeholderAlta = document.getElementById('placeholder-text');

    if (imageInputAlta && previewAlta && placeholderAlta) {
        imageInputAlta.addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                previewAlta.src = URL.createObjectURL(file);
                previewAlta.style.display = 'block';
                placeholderAlta.style.display = 'none';
            }
        });
    }

    // 2. Cálculo de Margen (Alta y Edición, si aplica)
    const costoInput = document.getElementById('costo');
    const ventaInput = document.getElementById('venta');
    const margenD = document.getElementById('margen-dinero');
    const margenP = document.getElementById('margen-porcentaje');

    function calcularMargen() {
        if (!costoInput || !ventaInput || !margenD || !margenP) return;
        
        const costo = parseFloat(costoInput.value) || 0;
        const venta = parseFloat(ventaInput.value) || 0;
        
        if (venta > 0) {
            const utilidad = venta - costo;
            const porcentaje = (utilidad / venta) * 100;
            
            margenD.innerText = `$${utilidad.toLocaleString('es-AR', {minimumFractionDigits: 2})}`;
            margenP.innerText = `${porcentaje.toFixed(1)}%`;
            
            if (utilidad < 0) {
                margenD.className = 'h4 fw-bold text-danger mb-0';
                margenP.className = 'fw-bold text-danger';
            } else {
                margenD.className = 'h4 fw-bold text-success mb-0';
                margenP.className = 'fw-bold text-cva-brown';
            }
        } else {
            margenD.innerText = '$0.00';
            margenP.innerText = '0%';
        }
    }

    if (costoInput && ventaInput) {
        costoInput.addEventListener('input', calcularMargen);
        ventaInput.addEventListener('input', calcularMargen);
    }

    // Resetear form en Alta Producto
    const formAlta = document.getElementById('form-alta-producto');
    if (formAlta) {
        formAlta.addEventListener('reset', () => {
            setTimeout(() => {
                if(previewAlta && placeholderAlta) {
                    previewAlta.style.display = 'none';
                    placeholderAlta.style.display = 'block';
                }
                calcularMargen();
            }, 10);
        });
    }
}

// 3. Preview de Imagen (Edición Producto) expuesta globalmente para onchange
window.previewImage = function(event) {
    const input = event.target;
    const preview = document.getElementById('main-preview');
    const indicator = document.getElementById('new-badge-indicator');
    
    if (input.files && input.files[0] && preview) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.add('animate__animated', 'animate__pulse');
            if(indicator) indicator.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
};
