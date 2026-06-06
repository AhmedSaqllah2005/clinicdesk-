<?php
class Auth {
    public static function login($user) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];
    }
    
    public static function logout() {
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        redirect('index.php?page=login');
    }
    
    public static function check() {
        return isset($_SESSION['user']);
    }
    
    public static function currentUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public static function role() {
        return $_SESSION['user']['role'] ?? '';
    }
    
    public static function userId() {
        return $_SESSION['user']['id'] ?? 0;
    }
    
    public static function requireRole(...$roles) {
        if (!self::check()) {
            redirect('index.php?page=login');
        }
        
        if (!in_array(self::role(), $roles)) {
            redirect('index.php?page=403');
        }
    }
}
