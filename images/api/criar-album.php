<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include_once '../../galeria/gallerydev/includes/config.php';
include_once '../../galeria/gallerydev/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $nome = trim($input['nome'] ?? '');
    $descricao = trim($input['descricao'] ?? '');
    $data = $input['data'] ?? '';
    
    if (empty($nome) || empty($data)) {
        echo json_encode(['success' => false, 'message' => 'Nome e data são obrigatórios']);
        exit;
    }
    
    // Criar pasta do álbum
    $pasta = createAlbumFolder($nome, $descricao);
    
    // Inserir no banco
    $stmt = $pdo->prepare("INSERT INTO albuns (nome, descricao, data, pasta) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$nome, $descricao, $data, $pasta]);
    
    if ($result) {
        $albumId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true, 
            'message' => 'Álbum criado com sucesso!',
            'album_id' => $albumId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar álbum']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>