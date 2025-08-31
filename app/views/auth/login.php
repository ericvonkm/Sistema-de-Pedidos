<?php
// ============================================
// ARCHIVO: app/views/layouts/main.php (LAYOUT PRINCIPAL)
// ============================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Sistema de Pedidos' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- CSS Personalizado -->
    <link href="<?= $assets_url ?>css/style.css" rel="stylesheet">
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
                        <a class="nav-link position-relative" href="#" id="cart-toggle">
                            <i class="bi bi-cart"></i> Carrito
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                                0
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container my-4">
        <?= $content ?? '' ?>
    </main>

    <!-- Carrito flotante -->
    <div id="cart-sidebar" class="cart-sidebar">
        <div class="cart-header">
            <h5>Tu Pedido</h5>
            <button type="button" class="btn-close" id="cart-close"></button>
        </div>
        <div class="cart-body" id="cart-items">
            <p class="text-muted">Tu carrito está vacío</p>
        </div>
        <div class="cart-footer">
            <div class="cart-total mb-3">
                <strong>Total: $<span id="cart-total">0</span></strong>
            </div>
            <button class="btn btn-primary w-100" id="proceed-checkout" disabled>
                Proceder al Pago
            </button>
        </div>
    </div>

    <!-- Overlay del carrito -->
    <div id="cart-overlay" class="cart-overlay"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $assets_url ?>js/cart.js"></script>
    <script src="<?= $assets_url ?>js/main.js"></script>
</body>
</html>

<?php
// ============================================
// ARCHIVO: app/views/home/index.php (PÁGINA PRINCIPAL - MENÚ)
// ============================================

ob_start();
?>

<!-- Hero Section -->
<div class="hero-section bg-light rounded p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold"><?= htmlspecialchars($restaurante['nombre']) ?></h1>
            <p class="lead"><?= htmlspecialchars($restaurante['descripcion'] ?? 'Bienvenido a nuestro restaurante') ?></p>
        </div>
        <div class="col-md-4 text-center">
            <?php if ($restaurante['logo']): ?>
                <img src="<?= UPLOADS_URL . $restaurante['logo'] ?>" alt="Logo" class="img-fluid rounded" style="max-height: 150px;">
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filtros de Menú -->
<div class="menu-filters mb-4">
    <div class="row">
        <div class="col-md-6">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" data-filter="all">
                    Todos
                </button>
                <?php foreach ($categorias as $categoria): ?>
                    <button type="button" class="btn btn-outline-primary" data-filter="categoria-<?= $categoria['id'] ?>">
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Buscar productos..." id="search-menu">
        </div>
    </div>
</div>

<!-- Menú de Productos -->
<div class="menu-grid">
    <?php 
    $currentCategory = null;
    foreach ($productos as $producto): 
        if ($currentCategory !== $producto['categoria_id']):
            if ($currentCategory !== null): ?>
                </div> <!-- Cerrar categoría anterior -->
            <?php endif; ?>
            
            <div class="categoria-section mb-5" data-categoria="<?= $producto['categoria_id'] ?>">
                <h3 class="categoria-title border-bottom pb-2 mb-4">
                    <?= htmlspecialchars($producto['categoria_nombre']) ?>
                </h3>
                <div class="row">
        <?php 
            $currentCategory = $producto['categoria_id'];
        endif; 
        ?>
        
        <!-- Tarjeta de Producto -->
        <div class="col-lg-4 col-md-6 mb-4 producto-item" data-categoria="categoria-<?= $producto['categoria_id'] ?>">
            <div class="card h-100 shadow-sm">
                <?php if ($producto['imagen']): ?>
                    <img src="<?= UPLOADS_URL . $producto['imagen'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                <?php endif; ?>
                
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($producto['descripcion']) ?></p>
                    
                    <!-- Etiquetas -->
                    <?php if ($producto['etiquetas']): ?>
                        <div class="mb-2">
                            <?php foreach (explode(',', $producto['etiquetas']) as $etiqueta): ?>
                                <span class="badge bg-success me-1"><?= htmlspecialchars(trim($etiqueta)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Presentaciones y Precios -->
                    <div class="presentaciones mb-3">
                        <?php if ($producto['presentacion_id']): ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold"><?= htmlspecialchars($producto['presentacion_nombre']) ?></span>
                                <span class="price text-primary fs-5">$<?= number_format($producto['presentacion_precio'], 0, ',', '.') ?></span>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Precio base</span>
                                <span class="price text-primary fs-5">$<?= number_format($producto['precio_base'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <button class="btn btn-primary w-100 add-to-cart" 
                            data-product-id="<?= $producto['id'] ?>"
                            data-presentation-id="<?= $producto['presentacion_id'] ?? '' ?>"
                            data-name="<?= htmlspecialchars($producto['nombre']) ?>"
                            data-price="<?= $producto['presentacion_precio'] ?? $producto['precio_base'] ?>">
                        <i class="bi bi-cart-plus"></i> Agregar al Carrito
                    </button>
                </div>
            </div>
        </div>
        
    <?php endforeach; ?>
    <?php if ($currentCategory !== null): ?>
        </div> <!-- Cerrar última categoría -->
    </div>
    <?php endif; ?>
</div>

<?php if (empty($productos)): ?>
    <div class="text-center py-5">
        <i class="bi bi-exclamation-circle display-1 text-muted"></i>
        <h3 class="mt-3">Menú no disponible</h3>
        <p class="text-muted">Este restaurante aún no ha configurado su menú.</p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include APP_PATH . 'views/layouts/main.php';
?>

<?php
// ============================================
// ARCHIVO: app/views/auth/login.php (PÁGINA DE LOGIN)
// ============================================

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php if ($restaurante['logo']): ?>
                        <img src="<?= UPLOADS_URL . $restaurante['logo'] ?>" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <?php endif; ?>
                    <h4>Iniciar Sesión</h4>
                    <p class="text-muted">Accede a tu cuenta en <?= htmlspecialchars($restaurante['nombre']) ?></p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $base_url . $restaurant_id ?>/auth/login">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">
                            Recordarme
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <p>¿No tienes cuenta? 
                        <a href="<?= $base_url . $restaurant_id ?>/auth/register">Regístrate aquí</a>
                    </p>
                </div>
                
                <div class="text-center mt-3">
                    <a href="<?= $base_url . $restaurant_id ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Continuar como Invitado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . 'views/layouts/main.php';
?>