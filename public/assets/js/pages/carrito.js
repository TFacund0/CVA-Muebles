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

    document.querySelectorAll('.js-cart-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm(this.dataset.confirm)) return;

            const url = this.dataset.url;
            const card = this.closest('.cart-item-card');

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'success') return;

                    if (data.empty) {
                        location.reload();
                        return;
                    }

                    card?.remove();
                    updateTotal();

                    const badge = document.querySelector('.navbar .navbar-cart-badge');
                    if (badge) badge.textContent = data.totalItems;
                })
                .catch(err => console.error(err));
        });
    });

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
                        // Find the item in the returned cart
                        const item = Object.values(data.cart).find(i => i.rowid === rowid);
                        if (item) {
                            // Update Qty display
                            document.getElementById(`qty-${rowid}`).textContent = item.qty;
                            // Update Item subtotal
                            document.getElementById(`subtotal-${rowid}`).textContent = formatCurrency(item.subtotal);
                            // Update Checkbox data
                            const cb = document.querySelector(`.item-checkbox[value="${rowid}"]`);
                            cb.dataset.qty = item.qty;
                            cb.dataset.subtotal = item.subtotal;

                            updateTotal();

                            // Update Navbar Badge if exists
                            const badge = document.querySelector('.navbar .badge');
                            if (badge) badge.textContent = data.totalItems;
                        } else {
                            // Item removed (resta to 0)
                            location.reload();
                        }
                    } else if (data.status === 'error') {
                        alert(data.message);
                    }
                })
                .catch(err => console.error(err));
        });
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateTotal);
    });

    updateTotal();
});
