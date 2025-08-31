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