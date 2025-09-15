<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once '../../galeria/gallerydev/includes/config.php';
include_once '../../galeria/gallerydev/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $album_id = intval($_POST['album_id'] ?? 0);
    
    if ($album_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID do álbum inválido']);
        exit;
    }
    
    // Verificar se o álbum existe
    $stmt = $pdo->prepare("SELECT * FROM albuns WHERE id = ?");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$album) {
        echo json_encode(['success' => false, 'message' => 'Álbum não encontrado']);
        exit;
    }
    
    if (!isset($_FILES['fotos']) || empty($_FILES['fotos']['name'][0])) {
        echo json_encode(['success' => false, 'message' => 'Nenhuma foto selecionada']);
        exit;
    }
    
    $uploadedFiles = [];
    $errors = [];
    $albumPath = __DIR__ . '/../uploads/' . $album['pasta'];
    
    // Verificar se a pasta do álbum existe
    if (!file_exists($albumPath)) {
        mkdir($albumPath, 0777, true);
    }
    
    $files = $_FILES['fotos'];
    $fileCount = count($files['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = $files['name'][$i];
        $fileTmp = $files['tmp_name'][$i];
        $fileSize = $files['size'][$i];
        $fileError = $files['error'][$i];
        $fileType = $files['type'][$i];
        
        if ($fileError !== UPLOAD_ERR_OK) {
            $errors[] = "Erro no upload de $fileName";
            continue;
        }
        
        // Verificar tipo de arquivo
        if (!isValidImageType(['type' => $fileType, 'name' => $fileName, 'size' => $fileSize])) {
            $errors[] = "$fileName não é um tipo de imagem válido";
            continue;
        }
        
        // Gerar nome único para o arquivo
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
        $targetPath = $albumPath . '/' . $uniqueName;
        $relativePath = 'uploads/' . $album['pasta'] . '/' . $uniqueName;
        
        // Mover arquivo
        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Inserir no banco
            $stmt = $pdo->prepare("
                INSERT INTO fotos (album_id, nome_arquivo, nome_original, caminho, tamanho) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$album_id, $uniqueName, $fileName, $relativePath, $fileSize]);
            
            $uploadedFiles[] = [
                'nome_original' => $fileName,
                'nome_arquivo' => $uniqueName,
                'caminho' => $relativePath,
                'tamanho' => $fileSize
            ];
        } else {
            $errors[] = "Erro ao salvar $fileName";
        }
    }
    
    $response = [
        'success' => count($uploadedFiles) > 0,
        'message' => count($uploadedFiles) . ' fotos enviadas com sucesso',
        'uploaded_files' => $uploadedFiles
    ];
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] .= '. ' . count($errors) . ' erros encontrados';
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>