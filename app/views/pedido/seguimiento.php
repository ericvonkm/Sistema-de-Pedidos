<?php
// ============================================
// ARCHIVO: app/views/pedido/seguimiento.php (SEGUIMIENTO DE PEDIDO)
// ============================================

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-receipt"></i> 
                    Seguimiento de Pedido #<?= $pedido['id'] ?>
                </h4>
            </div>
            
            <div class="card-body">
                <!-- Estado del Pedido -->
                <div class="order-status mb-4">
                    <div class="row text-center">
                        <div class="col">
                            <div class="status-step <?= in_array($pedido['estado'], ['recibido', 'preparacion', 'listo', 'en_camino', 'entregado']) ? 'active' : '' ?>">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>Recibido</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="status-step <?= in_array($pedido['estado'], ['preparacion', 'listo', 'en_camino', 'entregado']) ? 'active' : '' ?>">
                                <i class="bi bi-fire"></i>
                                <div>En Preparación</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="status-step <?= in_array($pedido['estado'], ['listo', 'en_camino', 'entregado']) ? 'active' : '' ?>">
                                <i class="bi bi-check2-circle"></i>
                                <div>Listo</div>
                            </div>
                        </div>
                        <?php if ($pedido['tipo_entrega'] === 'delivery'): ?>
                        <div class="col">
                            <div class="status-step <?= in_array($pedido['estado'], ['en_camino', 'entregado']) ? 'active' : '' ?>">
                                <i class="bi bi-truck"></i>
                                <div>En Camino</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col">
                            <div class="status-step <?= $pedido['estado'] === 'entregado' ? 'active' : '' ?>">
                                <i class="bi bi-hand-thumbs-up"></i>
                                <div>Entregado</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Información del Pedido -->
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Pedido</h6>
                        <ul class="list-unstyled">
                            <li><strong>Tipo:</strong> <?= ucfirst($pedido['tipo_entrega']) ?></li>
                            <li><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['creado_en'])) ?></li>
                            <li><strong>Sucursal:</strong> <?= htmlspecialchars($pedido['sucursal_nombre']) ?></li>
                            <?php if ($pedido['repartidor_nombre']): ?>
                                <li><strong>Repartidor:</strong> <?= htmlspecialchars($pedido['repartidor_nombre']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Estado del Pago</h6>
                        <span class="badge bg-<?= $pedido['estado_pago'] === 'pagado' ? 'success' : ($pedido['estado_pago'] === 'fallido' ? 'danger' : 'warning') ?> fs-6">
                            <?= ucfirst($pedido['estado_pago']) ?>
                        </span>
                        
                        <div class="mt-3">
                            <h6>Total del Pedido</h6>
                            <div class="fs-4 text-primary fw-bold">
                                $<?= number_format($pedido['total'], 0, ',', '.') ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($pedido['direccion_entrega']): ?>
                    <div class="mt-4">
                        <h6>Dirección de Entrega</h6>
                        <p class="text-muted"><?= htmlspecialchars($pedido['direccion_entrega']) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($pedido['notas']): ?>
                    <div class="mt-4">
                        <h6>Notas del Pedido</h6>
                        <p class="text-muted"><?= htmlspecialchars($pedido['notas']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="<?= $base_url . $restaurant_id ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Menú
                    </a>
                    
                    <?php if ($pedido['estado'] === 'entregado'): ?>
                        <a href="<?= $base_url . $restaurant_id ?>/calificar?pedido=<?= $pedido['id'] ?>&token=<?= $pedido['token_seguimiento'] ?>" 
                           class="btn btn-warning">
                            <i class="bi bi-star"></i> Calificar Experiencia
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . 'views/layouts/main.php';
?>