<?php
require_once 'database.php';

// Verificar se usuário está logado
function isLoggedIn() {
    return isset($_SESSION[ADMIN_SESSION_NAME]) && 
           time() - $_SESSION[ADMIN_SESSION_NAME]['last_activity'] < SESSION_TIMEOUT;
}

// Fazer login
function login($username, $password) {
    $db = getDB();
    
    $user = $db->fetchOne(
        "SELECT * FROM admin_users WHERE username = ?", 
        [$username]
    );
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION[ADMIN_SESSION_NAME] = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'last_activity' => time()
        ];
        return true;
    }
    
    return false;
}

// Fazer logout
function logout() {
    unset($_SESSION[ADMIN_SESSION_NAME]);
    session_destroy();
}

// Verificar autenticação (para páginas admin)
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'gallerydev/admin/');
        exit;
    }
    
    // Atualizar última atividade
    $_SESSION[ADMIN_SESSION_NAME]['last_activity'] = time();
}

// Criar usuário admin (apenas para instalação)
function createAdminUser($username, $password) {
    $db = getDB();
    
    // Verificar se já existe
    $existing = $db->fetchOne(
        "SELECT id FROM admin_users WHERE username = ?", 
        [$username]
    );
    
    if ($existing) {
        return false;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    return $db->insert(
        "INSERT INTO admin_users (username, password, created_at) VALUES (?, ?, NOW())",
        [$username, $hashed_password]
    );
}
?>