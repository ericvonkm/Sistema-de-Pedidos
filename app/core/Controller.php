<?php
// ============================================
// ARCHIVO: app/core/Controller.php (CONTROLADOR BASE)
// ============================================

class Controller {
    protected $restaurant_id;

    public function __construct() {
        $this->restaurant_id = $_SESSION['current_restaurant_id'] ?? null;
        
        if (!$this->restaurant_id) {
            $this->redirect('/error');
        }
    }

    protected function model($model) {
        $modelFile = APP_PATH . 'models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            throw new Exception("Model $model not found");
        }
    }

    protected function view($view, $data = []) {
        $viewFile = APP_PATH . 'views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // Extraer datos para la vista
            extract($data);
            
            // Variables globales disponibles en todas las vistas
            $restaurant_id = $this->restaurant_id;
            $base_url = BASE_URL;
            $assets_url = ASSETS_URL;
            
            require_once $viewFile;
        } else {
            throw new Exception("View $view not found");
        }
    }

    protected function redirect($path) {
        if (strpos($path, 'http') === 0) {
            header("Location: $path");
        } else {
            header("Location: " . BASE_URL . "public" . $path);
        }
        exit;
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function getCurrentRestaurantId() {
        return $this->restaurant_id;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getPost($key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    protected function getGet($key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    protected function validateCsrf() {
        $token = $this->getPost(CSRF_TOKEN_NAME);
        return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
    }

    protected function generateCsrf() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
}
