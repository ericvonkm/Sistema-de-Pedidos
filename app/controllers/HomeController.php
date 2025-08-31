<?php
// ============================================
// ARCHIVO: app/controllers/HomeController.php
// ============================================

class HomeController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $restauranteModel = $this->model('Restaurante');
        $productoModel = $this->model('Producto');
        
        // Obtener información del restaurante
        $restaurante = $restauranteModel->getById($this->restaurant_id);
        
        if (!$restaurante) {
            $this->redirect('/error');
        }
        
        // Obtener menú activo
        $categorias = $productoModel->getCategorias($this->restaurant_id);
        $productos = $productoModel->getMenuCompleto($this->restaurant_id);
        
        $data = [
            'restaurante' => $restaurante,
            'categorias' => $categorias,
            'productos' => $productos,
            'page_title' => $restaurante['nombre'],
            'csrf_token' => $this->generateCsrf()
        ];
        
        $this->view('home/index', $data);
    }
    
    public function menu() {
        $this->index(); // Redirigir al índice por ahora
    }
}
