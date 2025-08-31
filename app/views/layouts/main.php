<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Sistema de Pedidos' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ASSETS_URL ?>css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url . $restaurant_id ?>">
                <?= htmlspecialchars($restaurante['nombre'] ?? 'Restaurante') ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base_url . $restaurant_id ?>">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url . $restaurant_id ?>/pedido">
                                <i class="bi bi-bag"></i> Mis Pedidos
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= $base_url . $restaurant_id ?>/auth/logout">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url . $restaurant_id ?>/auth/login">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $base_url . $restaurant_id ?>/auth/register">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Carrito -->
                    <li class="nav-item">
                        <button class="nav-link btn btn-link position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                            <i class="bi bi-cart"></i> Carrito
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                                0
                            </span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container my-4">
        <?= $content ?? '' ?>
    </main>

    <!-- Carrito Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Tu Pedido</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cart-items">
                <p class="text-muted">Tu carrito está vacío</p>
            </div>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="cart-total mb-3">
                <strong>Total: $<span id="cart-total">0</span></strong>
            </div>
            <button class="btn btn-primary w-100" id="proceed-checkout" disabled>
                Proceder al Pago
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Carrito mejorado usando Bootstrap Offcanvas
        class Cart {
            constructor() {
                this.items = JSON.parse(localStorage.getItem('cart_items')) || [];
                this.init();
            }

            init() {
                this.bindEvents();
                this.updateUI();
            }

            bindEvents() {
                // Agregar al carrito
                document.addEventListener('click', (e) => {
                    if (e.target.closest('.add-to-cart')) {
                        e.preventDefault();
                        this.addItem(e.target.closest('.add-to-cart'));
                    }
                });

                // Proceder al checkout
                const checkoutBtn = document.getElementById('proceed-checkout');
                if (checkoutBtn) {
                    checkoutBtn.addEventListener('click', () => {
                        this.checkout();
                    });
                }
            }

            addItem(button) {
                const productId = button.dataset.productId;
                const presentationId = button.dataset.presentationId || '';
                const name = button.dataset.name;
                const price = parseFloat(button.dataset.price);

                const existingItem = this.items.find(item => 
                    item.productId === productId && item.presentationId === presentationId
                );

                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    this.items.push({
                        productId,
                        presentationId,
                        name,
                        price,
                        quantity: 1
                    });
                }

                this.updateUI();
                this.saveToStorage();
                
                // Mostrar carrito
                const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
                cartOffcanvas.show();

                // Feedback visual
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check"></i> Agregado';
                button.classList.replace('btn-primary', 'btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.replace('btn-success', 'btn-primary');
                }, 1500);
            }

            removeItem(index) {
                this.items.splice(index, 1);
                this.updateUI();
                this.saveToStorage();
            }

            updateQuantity(index, quantity) {
                if (quantity <= 0) {
                    this.removeItem(index);
                } else {
                    this.items[index].quantity = quantity;
                    this.updateUI();
                    this.saveToStorage();
                }
            }

            updateUI() {
                const cartCount = document.getElementById('cart-count');
                const cartItems = document.getElementById('cart-items');
                const cartTotal = document.getElementById('cart-total');
                const checkoutBtn = document.getElementById('proceed-checkout');

                const totalItems = this.items.reduce((sum, item) => sum + item.quantity, 0);
                const totalAmount = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

                if (cartCount) cartCount.textContent = totalItems;
                if (cartTotal) cartTotal.textContent = totalAmount.toLocaleString('es-CL');

                if (cartItems) {
                    if (this.items.length === 0) {
                        cartItems.innerHTML = '<p class="text-muted">Tu carrito está vacío</p>';
                        if (checkoutBtn) checkoutBtn.disabled = true;
                    } else {
                        cartItems.innerHTML = this.items.map((item, index) => `
                            <div class="cart-item mb-3 p-2 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${item.name}</h6>
                                        <small class="text-muted">$${item.price.toLocaleString('es-CL')} c/u</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" onclick="cart.removeItem(${index})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <div class="d-flex align-items-center mt-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="cart.updateQuantity(${index}, ${item.quantity - 1})">-</button>
                                    <span class="mx-3 fw-bold">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="cart.updateQuantity(${index}, ${item.quantity + 1})">+</button>
                                    <span class="ms-auto fw-bold">$${(item.price * item.quantity).toLocaleString('es-CL')}</span>
                                </div>
                            </div>
                        `).join('');
                        if (checkoutBtn) checkoutBtn.disabled = false;
                    }
                }
            }

            checkout() {
                if (this.items.length === 0) return;
                
                const restaurant_id = window.location.pathname.split('/')[1] || window.location.pathname.split('/')[2];
                window.location.href = `${window.location.origin}/app/${restaurant_id}/pedido/crear`;
            }

            saveToStorage() {
                localStorage.setItem('cart_items', JSON.stringify(this.items));
            }

            clear() {
                this.items = [];
                this.updateUI();
                this.saveToStorage();
            }
        }

        // Inicializar carrito
        let cart = new Cart();
        
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
    </script>
</body>
</html>