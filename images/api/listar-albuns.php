<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include_once __DIR__ . '/../../galeria/gallerydev/includes/config.php';

try {
    // Buscar todos os álbuns com total de fotos
    $stmt = $pdo->query("
        SELECT a.*, 
               COUNT(f.id) AS total_fotos,
               MIN(f.created_at) AS primeira_foto_data
        FROM albuns a
        LEFT JOIN fotos f ON a.id = f.album_id
        GROUP BY a.id
        ORDER BY a.created_at DESC
    ");
    
    $albuns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar a primeira foto de cada álbum
    foreach ($albuns as &$album) {
        if ($album['total_fotos'] > 0) {
            $fotoStmt = $pdo->prepare("SELECT * FROM fotos WHERE album_id = ? ORDER BY created_at ASC LIMIT 1");
            $fotoStmt->execute([$album['id']]);
            $primeira_foto = $fotoStmt->fetch(PDO::FETCH_ASSOC);
            $album['primeira_foto'] = $primeira_foto ? $primeira_foto : null;
        } else {
            $album['primeira_foto'] = null;
        }
    }

    echo json_encode([
        'success' => true,
        'albuns' => $albuns
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>
