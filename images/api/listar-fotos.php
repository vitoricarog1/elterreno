<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir os álbuns como array PHP
$albuns = include __DIR__ . "/listar-albuns.php"; // listar-albuns.php deve retornar $albuns

try {
    // Receber parâmetros
    $album_id = intval($_GET['album_id'] ?? 0);
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = max(1, intval($_GET['per_page'] ?? 50));

    if ($album_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID do álbum inválido']);
        exit;
    }

    // Contar total de fotos
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM fotos WHERE album_id = ?");
    $countStmt->execute([$album_id]);
    $total_fotos = (int)$countStmt->fetchColumn();

    $total_pages = ceil($total_fotos / $per_page);
    $offset = ($page - 1) * $per_page;

    // Buscar todas as fotos para navegação no modal
    $allStmt = $pdo->prepare("SELECT * FROM fotos WHERE album_id = ? ORDER BY created_at ASC");
    $allStmt->execute([$album_id]);
    $all_photos = $allStmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar fotos da página atual usando bindValue apenas para LIMIT e OFFSET
    $stmt = $pdo->prepare("
        SELECT * FROM fotos 
        WHERE album_id = :album_id
        ORDER BY created_at ASC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':album_id', $album_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar JSON único
    echo json_encode([
        'success' => true,
        'fotos' => $fotos,
        'all_photos' => $all_photos,
        'total_photos' => $total_fotos,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'per_page' => $per_page,
        'albuns' => $albuns
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    exit;
}
?>
