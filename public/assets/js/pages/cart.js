/**
 * CVA Muebles - Cart Scripts
 * Maneja la lógica interactiva del carrito de compras: actualización de cantidades (AJAX)
 * y cálculo dinámico de subtotales.
 */

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const summarySubtotal = document.querySelector('.cart-total-line span:last-child');
    const summaryTotal = document.querySelector('.amount');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.subtotal);
            }
        });
        const formatted = formatCurrency(total);
        if (summarySubtotal) summarySubtotal.textContent = formatted;
        if (summaryTotal) summaryTotal.textContent = formatted;
    }

    // AJAX Quantity Updates
    document.querySelectorAll('.ajax-qty').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.dataset.url;
            const rowid = this.dataset.rowid;

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Buscar el ítem en el carrito devuelto
                    const item = Object.values(data.cart).find(i => i.rowid === rowid);
                    if (item) {
                        // Actualizar display de cantidad
                        document.getElementById(`qty-${rowid}`).textContent = item.qty;
                        // Actualizar subtotal del ítem
                        document.getElementById(`subtotal-${rowid}`).textContent = formatCurrency(item.subtotal);
                        // Actualizar data del checkbox
                        const cb = document.querySelector(`.item-checkbox[value="${rowid}"]`);
                        if (cb) {
                            cb.dataset.qty = item.qty;
                            cb.dataset.subtotal = item.subtotal;
                        }

                        updateTotal();

                        // Actualizar badge del navbar si existe
                        const badge = document.querySelector('.navbar .badge');
                        if (badge) badge.textContent = data.totalItems;
                    } else {
                        // Ítem eliminado (restado a 0)
                        location.reload();
                    }
                } else if (data.status === 'error') {
                    alert(data.message);
                }
            })
            .catch(err => console.error('Error actualizando cantidad:', err));
        });
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateTotal);
    });

    updateTotal();
});
