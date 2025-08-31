<?php
// ============================================
// ARCHIVO: app/controllers/AuthController.php
// ============================================

class AuthController extends Controller {
    
    public function __construct() {
        // No llamar parent::__construct() aquí porque auth no requiere restaurante validado aún
        $this->restaurant_id = $_GET['url'] ? (int)explode('/', $_GET['url'])[0] : null;
    }
    
    public function index() {
        $this->login(); // Por defecto mostrar login
    }
    
    public function login() {
        if ($this->isPost()) {
            $this->processLogin();
        }
        
        $restauranteModel = $this->model('Restaurante');
        $restaurante = $restauranteModel->getById($this->restaurant_id);
        
        if (!$restaurante) {
            $this->redirect('/error');
        }
        
        $data = [
            'restaurante' => $restaurante,
            'page_title' => 'Iniciar Sesión',
            'csrf_token' => $this->generateCsrf(),
            'error' => $_SESSION['auth_error'] ?? null
        ];
        
        unset($_SESSION['auth_error']);
        
        $this->view('auth/login', $data);
    }
    
    public function register() {
        if ($this->isPost()) {
            $this->processRegister();
        }
        
        $restauranteModel = $this->model('Restaurante');
        $restaurante = $restauranteModel->getById($this->restaurant_id);
        
        if (!$restaurante) {
            $this->redirect('/error');
        }
        
        $data = [
            'restaurante' => $restaurante,
            'page_title' => 'Crear Cuenta',
            'csrf_token' => $this->generateCsrf(),
            'error' => $_SESSION['auth_error'] ?? null
        ];
        
        unset($_SESSION['auth_error']);
        
        $this->view('auth/register', $data);
    }
    
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['current_restaurant_id']);
        
        $this->redirect("/$this->restaurant_id/auth/login");
    }
    
    private function processLogin() {
        if (!$this->validateCsrf()) {
            $_SESSION['auth_error'] = 'Token de seguridad inválido';
            return;
        }
        
        $email = $this->getPost('email');
        $password = $this->getPost('password');
        
        if (empty($email) || empty($password)) {
            $_SESSION['auth_error'] = 'Email y contraseña son requeridos';
            return;
        }
        
        $userModel = $this->model('Usuario');
        $user = $userModel->authenticateByEmail($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['current_restaurant_id'] = $this->restaurant_id;
            
            $this->redirect("/$this->restaurant_id/");
        } else {
            $_SESSION['auth_error'] = 'Email o contraseña incorrectos';
        }
    }
    
    private function processRegister() {
        if (!$this->validateCsrf()) {
            $_SESSION['auth_error'] = 'Token de seguridad inválido';
            return;
        }
        
        $nombre = $this->getPost('nombre');
        $email = $this->getPost('email');
        $telefono = $this->getPost('telefono');
        $password = $this->getPost('password');
        $password_confirm = $this->getPost('password_confirm');
        
        // Validaciones
        if (empty($nombre) || empty($email) || empty($telefono) || empty($password)) {
            $_SESSION['auth_error'] = 'Todos los campos son requeridos';
            return;
        }
        
        if ($password !== $password_confirm) {
            $_SESSION['auth_error'] = 'Las contraseñas no coinciden';
            return;
        }
        
        if (strlen($password) < 6) {
            $_SESSION['auth_error'] = 'La contraseña debe tener al menos 6 caracteres';
            return;
        }
        
        $userModel = $this->model('Usuario');
        
        // Verificar si email ya existe
        if ($userModel->emailExists($email)) {
            $_SESSION['auth_error'] = 'El email ya está registrado';
            return;
        }
        
        // Verificar si teléfono ya existe  
        if ($userModel->telefonoExists($telefono)) {
            $_SESSION['auth_error'] = 'El teléfono ya está registrado';
            return;
        }
        
        // Crear usuario
        $userData = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'password_hash' => password_hash($password, HASH_ALGO)
        ];
        
        $userId = $userModel->create($userData);
        
        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $nombre;
            $_SESSION['current_restaurant_id'] = $this->restaurant_id;
            
            $this->redirect("/$this->restaurant_id/");
        } else {
            $_SESSION['auth_error'] = 'Error al crear la cuenta';
        }
    }
}