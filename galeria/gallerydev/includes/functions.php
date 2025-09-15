<?php
require_once 'database.php';

// Função para listar todos os álbuns
function getAlbums($limit = null) {
    $db = getDB();
    $sql = "SELECT a.*, COUNT(f.id) as total_fotos 
            FROM albums a 
            LEFT JOIN fotos f ON a.id = f.album_id 
            GROUP BY a.id 
            ORDER BY a.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return $db->fetchAll($sql);
}

// Função para buscar álbum por ID
function getAlbumById($id) {
    $db = getDB();
    return $db->fetchOne("SELECT * FROM albums WHERE id = ?", [$id]);
}

// Função para buscar fotos de um álbum com paginação
function getAlbumPhotos($album_id, $page = 1, $per_page = PHOTOS_PER_PAGE) {
    $db = getDB();
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT * FROM fotos 
            WHERE album_id = ? 
            ORDER BY created_at ASC 
            LIMIT ? OFFSET ?";
    
    return $db->fetchAll($sql, [$album_id, $per_page, $offset]);
}

// Função para contar fotos de um álbum
function countAlbumPhotos($album_id) {
    $db = getDB();
    $result = $db->fetchOne("SELECT COUNT(*) as total FROM fotos WHERE album_id = ?", [$album_id]);
    return $result ? $result['total'] : 0;
}

// Função para criar thumbnail
function createThumbnail($source, $destination, $width = THUMB_WIDTH, $height = THUMB_HEIGHT) {
    $info = getimagesize($source);
    if (!$info) return false;
    
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    if (!$image) return false;
    
    $original_width = imagesx($image);
    $original_height = imagesy($image);
    
    // Calcular proporções
    $ratio = min($width / $original_width, $height / $original_height);
    $new_width = $original_width * $ratio;
    $new_height = $original_height * $ratio;
    
    // Criar thumbnail
    $thumbnail = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($thumbnail, 255, 255, 255);
    imagefill($thumbnail, 0, 0, $white);
    
    $x = ($width - $new_width) / 2;
    $y = ($height - $new_height) / 2;
    
    imagecopyresampled(
        $thumbnail, $image,
        $x, $y, 0, 0,
        $new_width, $new_height,
        $original_width, $original_height
    );
    
    // Salvar thumbnail
    $result = imagejpeg($thumbnail, $destination, 85);
    
    imagedestroy($image);
    imagedestroy($thumbnail);
    
    return $result;
}

// Função para sanitizar strings
function sanitize($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

// Função para formatar data
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Função para gerar slug
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Função para verificar se arquivo é imagem válida
function isValidImage($file) {
    $allowed_types = ALLOWED_TYPES;
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'image/jpeg',
        'image/png', 
        'image/gif'
    ];
    
    return in_array($mime_type, $allowed_mimes);
}

// Função para redimensionar imagem se necessário
function resizeImageIfNeeded($source, $destination, $max_width = 1920, $max_height = 1080) {
    $info = getimagesize($source);
    if (!$info) return false;
    
    $original_width = $info[0];
    $original_height = $info[1];
    $mime = $info['mime'];
    
    // Se a imagem já está no tamanho adequado, apenas copia
    if ($original_width <= $max_width && $original_height <= $max_height) {
        return copy($source, $destination);
    }
    
    // Calcular novas dimensões
    $ratio = min($max_width / $original_width, $max_height / $original_height);
    $new_width = $original_width * $ratio;
    $new_height = $original_height * $ratio;
    
    // Criar imagem redimensionada
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    if (!$image) return false;
    
    $resized = imagecreatetruecolor($new_width, $new_height);
    
    // Preservar transparência para PNG
    if ($mime === 'image/png') {
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
    }
    
    imagecopyresampled(
        $resized, $image,
        0, 0, 0, 0,
        $new_width, $new_height,
        $original_width, $original_height
    );
    
    $result = imagejpeg($resized, $destination, 90);
    
    imagedestroy($image);
    imagedestroy($resized);
    
    return $result;
}
?>