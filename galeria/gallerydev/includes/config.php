<?php
// Configuração automática do sistema
session_start();

// Detectar ambiente automaticamente
$is_local = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], '.local') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'xampp') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'wamp') !== false
);

// Configurações de ambiente
if ($is_local) {
    // Ambiente local (XAMPP/WAMP)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'galeria_fotos');
    define('BASE_URL', 'http://localhost/galeria/');
    define('GALLERY_PATH', '/galeria/gallerydev/');
} else {
    // Ambiente de produção (Hostinger)
    define('DB_HOST', 'localhost');      // Host padrão da Hostinger
    define('DB_USER', 'u945783144_icarovitor');     
    define('DB_PASS', 'elterrEno123@');  
    define('DB_NAME', 'u945783144_galeria_fotos');  
    define('BASE_URL', 'https://elterreno.com.br/');  
    define('GALLERY_PATH', '/galeria/gallerydev/');  // Caminho completo para produção
}

// Caminhos do sistema
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
define('UPLOAD_PATH', ROOT_PATH . GALLERY_PATH . 'uploads/');
define('THUMB_PATH', ROOT_PATH . GALLERY_PATH . 'uploads/thumbs/');
define('ALBUMS_PATH', ROOT_PATH . GALLERY_PATH . 'uploads/albums/');
define('UPLOAD_URL', BASE_URL . 'gallerydev/uploads/');
define('THUMB_URL', BASE_URL . 'gallerydev/uploads/thumbs/');
define('ALBUMS_URL', BASE_URL . 'gallerydev/uploads/albums/');

// Configurações de upload
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('THUMB_WIDTH', 300);
define('THUMB_HEIGHT', 300);
define('PHOTOS_PER_PAGE', 50);

// Configurações de segurança
define('ADMIN_SESSION_NAME', 'gallery_admin');
define('SESSION_TIMEOUT', 3600); // 1 hora

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Função para debug (apenas em ambiente local)
function debug($data) {
    if (defined('DB_HOST') && DB_HOST === 'localhost') {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
?>