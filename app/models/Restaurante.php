<?php
// ============================================
// ARCHIVO: app/models/Restaurante.php
// ============================================

class Restaurante extends Model {
    
    public function getById($id) {
        $sql = "SELECT * FROM restaurantes WHERE id = ? AND activo = 1";
        return $this->fetch($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM restaurantes WHERE activo = 1 ORDER BY nombre";
        return $this->fetchAll($sql);
    }
    
    public function getSucursales($restaurant_id) {
        $sql = "SELECT * FROM sucursales 
                WHERE restaurante_id = ? AND activo = 1 
                ORDER BY nombre";
        return $this->fetchAll($sql, [$restaurant_id]);
    }
    
    public function create($data) {
        return $this->insert('restaurantes', $data);
    }
    
    public function updateById($id, $data) {
        return $this->update('restaurantes', $data, 'id = ?', [$id]);
    }
    
    public function updateAlmacenamientoUsado($restaurant_id, $bytes) {
        $mb = $bytes / (1024 * 1024);
        $sql = "UPDATE restaurantes 
                SET almacenamiento_usado_mb = almacenamiento_usado_mb + ? 
                WHERE id = ?";
        return $this->query($sql, [$mb, $restaurant_id]);
    }
    
    public function getAlmacenamientoInfo($restaurant_id) {
        $sql = "SELECT cuota_almacenamiento_mb, almacenamiento_usado_mb,
                       (cuota_almacenamiento_mb - almacenamiento_usado_mb) as disponible_mb
                FROM restaurantes WHERE id = ?";
        return $this->fetch($sql, [$restaurant_id]);
    }
}