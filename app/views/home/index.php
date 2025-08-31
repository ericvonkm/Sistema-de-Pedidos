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