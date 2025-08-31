<?php
// ============================================
// ARCHIVO: app/models/Producto.php
// ============================================

class Producto extends Model {
    
    public function getCategorias($restaurant_id) {
        $sql = "SELECT * FROM categorias 
                WHERE restaurante_id = ? AND activo = 1 
                ORDER BY orden, nombre";
        return $this->fetchAll($sql, [$restaurant_id]);
    }
    
    public function getMenuCompleto($restaurant_id, $sucursal_id = null) {
        $sql = "SELECT 
                    p.id, p.nombre, p.descripcion, p.imagen, p.precio_base,
                    c.nombre as categoria_nombre, c.id as categoria_id,
                    pr.id as presentacion_id, pr.nombre as presentacion_nombre, 
                    pr.precio as presentacion_precio,
                    GROUP_CONCAT(e.nombre) as etiquetas
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN presentaciones pr ON p.id = pr.producto_id AND pr.disponible = 1
                LEFT JOIN producto_etiquetas pe ON p.id = pe.producto_id
                LEFT JOIN etiquetas e ON pe.etiqueta_id = e.id AND e.activo = 1
                WHERE p.restaurante_id = ? AND p.activo = 1 AND c.activo = 1";
        
        if ($sucursal_id) {
            $sql .= " AND EXISTS (SELECT 1 FROM menu_sucursal ms 
                                 WHERE ms.producto_id = p.id 
                                 AND ms.presentacion_id = pr.id 
                                 AND ms.sucursal_id = ? 
                                 AND ms.disponible = 1)";
            $params = [$restaurant_id, $sucursal_id];
        } else {
            $params = [$restaurant_id];
        }
        
        $sql .= " GROUP BY p.id, pr.id 
                 ORDER BY c.orden, c.nombre, p.nombre, pr.orden";
        
        return $this->fetchAll($sql, $params);
    }
    
    public function getById($id, $restaurant_id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = ? AND p.restaurante_id = ? AND p.activo = 1";
        return $this->fetch($sql, [$id, $restaurant_id]);
    }
    
    public function getPresentaciones($producto_id) {
        $sql = "SELECT * FROM presentaciones 
                WHERE producto_id = ? AND disponible = 1 
                ORDER BY orden, precio";
        return $this->fetchAll($sql, [$producto_id]);
    }
    
    public function getAdicionales($producto_id, $presentacion_id = null) {
        $sql = "SELECT a.*, pa.grupo, pa.orden
                FROM adicionales a
                JOIN producto_adicionales pa ON a.id = pa.adicional_id
                WHERE pa.producto_id = ? AND a.activo = 1";
        
        $params = [$producto_id];
        
        if ($presentacion_id) {
            $sql .= " AND (pa.presentacion_id IS NULL OR pa.presentacion_id = ?)";
            $params[] = $presentacion_id;
        }
        
        $sql .= " ORDER BY pa.grupo, pa.orden, a.nombre";
        
        return $this->fetchAll($sql, $params);
    }
    
    public function getEtiquetas($producto_id) {
        $sql = "SELECT e.*
                FROM etiquetas e
                JOIN producto_etiquetas pe ON e.id = pe.etiqueta_id
                WHERE pe.producto_id = ? AND e.activo = 1";
        return $this->fetchAll($sql, [$producto_id]);
    }
}