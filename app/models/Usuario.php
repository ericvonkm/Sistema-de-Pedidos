<?php
// ============================================
// ARCHIVO: app/models/Usuario.php
// ============================================

class Usuario extends Model {
    
    public function authenticateByEmail($email, $password) {
        $sql = "SELECT * FROM usuarios_comensales 
                WHERE email = ? AND activo = 1";
        $user = $this->fetch($sql, [$email]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    public function authenticateByTelefono($telefono, $password) {
        $sql = "SELECT * FROM usuarios_comensales 
                WHERE telefono = ? AND activo = 1";
        $user = $this->fetch($sql, [$telefono]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM usuarios_comensales WHERE id = ? AND activo = 1";
        return $this->fetch($sql, [$id]);
    }
    
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM usuarios_comensales WHERE email = ?";
        $result = $this->fetch($sql, [$email]);
        return $result['count'] > 0;
    }
    
    public function telefonoExists($telefono) {
        $sql = "SELECT COUNT(*) as count FROM usuarios_comensales WHERE telefono = ?";
        $result = $this->fetch($sql, [$telefono]);
        return $result['count'] > 0;
    }
    
    public function create($data) {
        // No agregar restaurant_id automáticamente para usuarios globales
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO usuarios_comensales ($columns) VALUES ($placeholders)";
        $stmt = $this->query($sql, $data);
        
        return $this->db->lastInsertId();
    }
    
    public function updateById($id, $data) {
        return $this->update('usuarios_comensales', $data, 'id = ?', [$id]);
    }
    
    public function getDirecciones($user_id) {
        $sql = "SELECT * FROM direcciones_usuario 
                WHERE usuario_id = ? AND activo = 1 
                ORDER BY principal DESC, alias";
        return $this->fetchAll($sql, [$user_id]);
    }
    
    public function addDireccion($user_id, $data) {
        $data['usuario_id'] = $user_id;
        return $this->insert('direcciones_usuario', $data);
    }
}
