<?php
// ============================================
// ARCHIVO: app/core/App.php (NÚCLEO DE LA APLICACIÓN)
// ============================================

class App {
    private $restaurant_id;
    private $controller = DEFAULT_CONTROLLER;
    private $method = DEFAULT_METHOD;
    private $params = [];

    public function __construct() {
        $this->parseUrl();
        $this->validateRestaurant();
        $this->loadController();
    }

    private function parseUrl() {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $segments = explode('/', $url);

        // Estructura: /restaurant_id/controller/method/param1/param2/...
        if (!empty($segments[0]) && is_numeric($segments[0])) {
            $this->restaurant_id = (int)$segments[0];
            
            if (isset($segments[1]) && !empty($segments[1])) {
                $this->controller = ucfirst(strtolower($segments[1]));
            }
            
            if (isset($segments[2]) && !empty($segments[2])) {
                $this->method = strtolower($segments[2]);
            }
            
            $this->params = array_slice($segments, 3);
        } else {
            $this->redirectToError('URL inválida');
        }
    }

    private function validateRestaurant() {
        if (!$this->restaurant_id) {
            $this->redirectToError('ID de restaurante requerido');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, activo FROM restaurantes WHERE id = ?");
        $stmt->execute([$this->restaurant_id]);
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$restaurant) {
            $this->redirectToError('Restaurante no encontrado');
        }

        if (!$restaurant['activo']) {
            $this->redirectToError('Restaurante no disponible');
        }

        // Almacenar ID del restaurante en sesión
        $_SESSION['current_restaurant_id'] = $this->restaurant_id;
    }

    private function loadController() {
        $controllerName = $this->controller . 'Controller';
        $controllerFile = APP_PATH . 'controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                if (method_exists($controller, $this->method)) {
                    call_user_func_array([$controller, $this->method], $this->params);
                } else {
                    $this->redirectToError('Método no encontrado: ' . $this->method);
                }
            } else {
                $this->redirectToError('Controlador no encontrado: ' . $controllerName);
            }
        } else {
            $this->redirectToError('Archivo de controlador no encontrado');
        }
    }

    private function redirectToError($message) {
        http_response_code(404);
        echo "Error 404: " . htmlspecialchars($message);
        exit;
    }
}