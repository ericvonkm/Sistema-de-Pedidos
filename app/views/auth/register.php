<?php
// ============================================
// ARCHIVO: app/views/auth/register.php (PÁGINA DE REGISTRO)
// ============================================

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php if ($restaurante['logo']): ?>
                        <img src="<?= UPLOADS_URL . $restaurante['logo'] ?>" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <?php endif; ?>
                    <h4>Crear Cuenta</h4>
                    <p class="text-muted">Regístrate en <?= htmlspecialchars($restaurante['nombre']) ?></p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $base_url . $restaurant_id ?>/auth/register">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrf_token ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" required 
                               placeholder="+56912345678"
                               value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Acepto los términos y condiciones
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus"></i> Crear Cuenta
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <p>¿Ya tienes cuenta? 
                        <a href="<?= $base_url . $restaurant_id ?>/auth/login">Inicia sesión aquí</a>
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
