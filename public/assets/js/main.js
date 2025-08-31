// Funciones globales del sistema
document.addEventListener('DOMContentLoaded', function() {
    
    // Filtros de menú
        const filterButtons = document.querySelectorAll('[data-filter]');
        const productos = document.querySelectorAll('.producto-item');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.dataset.filter;
                
                // Actualizar botones activos
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                // Filtrar productos
                productos.forEach(producto => {
                    if (filter === 'all' || producto.dataset.categoria === filter) {
                        producto.style.display = 'block';
                    } else {
                        producto.style.display = 'none';
                    }
                });
            });
        });
    
    // Búsqueda en menú
        const searchInput = document.getElementById('search-menu');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                
                productos.forEach(producto => {
                    const productName = producto.querySelector('.card-title')?.textContent.toLowerCase() || '';
                    const productDesc = producto.querySelector('.card-text')?.textContent.toLowerCase() || '';
                    
                    if (productName.includes(searchTerm) || productDesc.includes(searchTerm)) {
                        producto.style.display = 'block';
                    } else {
                        producto.style.display = 'none';
                    }
                });
            });
        }
    
    // Auto-refresh para seguimiento de pedidos
    if (window.location.pathname.includes('/seguimiento/')) {
        setInterval(() => {
            const currentUrl = window.location.href;
            fetch(currentUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Actualizar solo el estado si cambió
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newStatus = doc.querySelector('.order-status');
                const currentStatus = document.querySelector('.order-status');
                
                if (newStatus && currentStatus && newStatus.innerHTML !== currentStatus.innerHTML) {
                    currentStatus.innerHTML = newStatus.innerHTML;
                    
                    // Mostrar notificación
                    showNotification('Estado del pedido actualizado', 'success');
                }
            })
            .catch(console.error);
        }, 30000); // Actualizar cada 30 segundos
    }
});

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const alertClass = `alert-${type}`;
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Función para formatear números como precio chileno
function formatPrice(amount) {
    return new Intl.NumberFormat('es-CL', {
        style: 'currency',
        currency: 'CLP',
        minimumFractionDigits: 0
    }).format(amount);
}

// Validaciones de formularios
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}
