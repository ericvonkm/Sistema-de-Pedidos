<?php
// ============================================
// ARCHIVO: app/core/Model.php (MODELO BASE)
// ============================================

class Model {
    protected $db;
    protected $restaurant_id;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->restaurant_id = $_SESSION['current_restaurant_id'] ?? null;
    }

    protected function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            throw new Exception("Error en la base de datos");
        }
    }

    protected function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function insert($table, $data) {
        // Agregar restaurant_id automáticamente si la tabla lo requiere
        $tablesWithRestaurantId = [
            'sucursales', 'usuarios_admin', 'etiquetas', 'categorias', 
            'productos', 'adicionales', 'pedidos', 'webhook_logs'
        ];
        
        if (in_array($table, $tablesWithRestaurantId) && $this->restaurant_id) {
            $data['restaurante_id'] = $this->restaurant_id;
        }

        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->query($sql, $data);
        
        return $this->db->lastInsertId();
    }

    protected function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "$key = :$key";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE $table SET $setClause WHERE $where";
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params)->rowCount();
    }

    protected function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }

    protected function getCurrentRestaurantId() {
        return $this->restaurant_id;
    }
}