<?php
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            throw new RuntimeException("Database connection failed.");
        }
        
        $this->conn->set_charset("utf8mb4");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function query($sql, $types = "", $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            throw new RuntimeException("Query preparation failed.");
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        
        if ($stmt->field_count > 0) {
            return $stmt->get_result();
        }
        
        return true;
    }
    
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    // =========================================================================
    // Transaction Support
    // =========================================================================

    public function beginTransaction() {
        $this->conn->autocommit(false);
    }

    public function commit() {
        $this->conn->commit();
        $this->conn->autocommit(true);
    }

    public function rollback() {
        $this->conn->rollback();
        $this->conn->autocommit(true);
    }

    private function __clone() {}
}