<?php
// Configuração do sistema administrativo
session_start();

// Detectar ambiente
$is_local = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], '.local') !== false
);

// Configurações de banco de dados
if ($is_local) {
    // Ambiente local (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'el_terreno');
    define('BASE_URL', 'http://localhost/');
} else {
    // Ambiente de produção (Hostinger)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u945783144_elterreno');
    define('DB_PASS', '#Adidas777');
    define('DB_NAME', 'u945783144_elterreno');
    define('BASE_URL', 'https://elterreno.com.br/');
}

// Configurações gerais
define('ADMIN_USER', 'plug');
define('ADMIN_PASS', '#Adidas777');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('PHOTOS_PER_PAGE', 50);
define('MAX_PHOTOS_PER_ALBUM', 500);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Conexão com banco
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Funções auxiliares
function isLoggedIn() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function sanitize($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
?>