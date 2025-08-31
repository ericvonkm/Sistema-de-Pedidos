<?php
// ============================================
// ARCHIVO: app/controllers/PedidoController.php
// ============================================

class PedidoController extends Controller {
    
    public function index() {
        // Mostrar historial de pedidos del usuario
        if (!isset($_SESSION['user_id'])) {
            $this->redirect("/$this->restaurant_id/auth/login");
        }
        
        $pedidoModel = $this->model('Pedido');
        $pedidos = $pedidoModel->getByUsuario($_SESSION['user_id'], $this->restaurant_id);
        
        $data = [
            'pedidos' => $pedidos,
            'page_title' => 'Mis Pedidos'
        ];
        
        $this->view('pedido/index', $data);
    }
    
    public function crear() {
        if ($this->isPost()) {
            $this->procesarPedido();
        }
        
        // Mostrar formulario de pedido
        $data = [
            'page_title' => 'Realizar Pedido',
            'csrf_token' => $this->generateCsrf()
        ];
        
        $this->view('pedido/crear', $data);
    }
    
    public function seguimiento($token = null) {
        if (!$token) {
            $this->redirect("/$this->restaurant_id/");
        }
        
        $pedidoModel = $this->model('Pedido');
        $pedido = $pedidoModel->getByToken($token);
        
        if (!$pedido || $pedido['restaurante_id'] != $this->restaurant_id) {
            $this->redirect("/$this->restaurant_id/");
        }
        
        $data = [
            'pedido' => $pedido,
            'page_title' => 'Seguimiento de Pedido #' . $pedido['id']
        ];
        
        $this->view('pedido/seguimiento', $data);
    }
    
    private function procesarPedido() {
        // Lógica para procesar pedido (implementar después)
        $this->json(['status' => 'success', 'message' => 'Pedido procesado']);
    }
}
