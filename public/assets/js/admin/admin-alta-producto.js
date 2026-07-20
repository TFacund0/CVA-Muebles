/**
 * Alta de producto (back/products/alta_producto.php): preview de imagen y
 * cálculo de margen de ganancia en tiempo real.
 */
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('preview-image');
    const placeholder = document.getElementById('placeholder-text');

    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
        });
    }

    // Cálculo de Margen
    const costoInput = document.getElementById('costo');
    const ventaInput = document.getElementById('venta');
    const margenD = document.getElementById('margen-dinero');
    const margenP = document.getElementById('margen-porcentaje');

    function calcularMargen() {
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

    const form = document.getElementById('form-alta-producto');
    if (form) {
        form.addEventListener('reset', () => {
            setTimeout(() => {
                preview.style.display = 'none';
                placeholder.style.display = 'block';
                calcularMargen();
            }, 10);
        });
    }
});
