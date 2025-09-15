<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');

include_once '../../galeria/gallerydev/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $album_id = intval($input['album_id'] ?? $_POST['album_id'] ?? 0);
    
    if ($album_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID do álbum inválido']);
        exit;
    }
    
    // Buscar dados do álbum
    $stmt = $pdo->prepare("SELECT * FROM albuns WHERE id = ?");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$album) {
        echo json_encode(['success' => false, 'message' => 'Álbum não encontrado']);
        exit;
    }
    
    // Deletar fotos do banco
    $stmt = $pdo->prepare("DELETE FROM fotos WHERE album_id = ?");
    $stmt->execute([$album_id]);
    
    // Deletar álbum do banco
    $stmt = $pdo->prepare("DELETE FROM albuns WHERE id = ?");
    $result = $stmt->execute([$album_id]);
    
    if ($result) {
        // Tentar deletar pasta física (opcional)
        $albumPath = __DIR__ . '/../uploads/' . $album['pasta'];
        if (file_exists($albumPath)) {
            // Deletar arquivos da pasta
            $files = glob($albumPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($albumPath);
        }
        
        echo json_encode(['success' => true, 'message' => 'Álbum deletado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao deletar álbum']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>