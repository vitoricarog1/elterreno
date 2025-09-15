<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

$success_message = '';
$error_message = '';

// Processar ações
if ($_POST['action'] ?? '' === 'delete') {
    $album_id = intval($_POST['album_id'] ?? 0);
    if ($album_id > 0) {
        $db = getDB();
        
        // Buscar fotos do álbum para deletar arquivos
        $photos = $db->fetchAll("SELECT nome_arquivo FROM fotos WHERE album_id = ?", [$album_id]);
        
        // Deletar arquivos físicos
        foreach ($photos as $photo) {
            $file_path = UPLOAD_PATH . 'albums/' . $photo['nome_arquivo'];
            $thumb_path = THUMB_PATH . $photo['nome_arquivo'];
            
            if (file_exists($file_path)) unlink($file_path);
            if (file_exists($thumb_path)) unlink($thumb_path);
        }
        
        // Deletar do banco (CASCADE vai deletar as fotos automaticamente)
        if ($db->query("DELETE FROM albums WHERE id = ?", [$album_id])) {
            $success_message = 'Álbum deletado com sucesso!';
        } else {
            $error_message = 'Erro ao deletar álbum.';
        }
    }
}

// Buscar todos os álbuns
$albums = getAlbums();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Álbuns - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .album-row {
            transition: background-color 0.3s ease;
        }
        .album-row:hover {
            background-color: rgba(0,0,0,0.02);
        }
    </style>
</head>
<body>
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="sidebar p-3">
                <div class="text-center mb-4">
                    <i class="fas fa-camera fa-2x mb-2"></i>
                    <h5>Admin Panel</h5>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="criar-album.php">
                        <i class="fas fa-plus me-2"></i>Criar Álbum
                    </a>
                    <a class="nav-link active" href="gerenciar-albums.php">
                        <i class="fas fa-folder me-2"></i>Gerenciar Álbuns
                    </a>
                    <a class="nav-link" href="upload-fotos.php">
                        <i class="fas fa-upload me-2"></i>Upload Fotos
                    </a>
                    <hr class="my-3">
                    <a class="nav-link" href="<?= BASE_URL ?>" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Galeria
                    </a>
                    <a class="nav-link" href="?action=logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-folder me-2"></i>Gerenciar Álbuns</h2>
                    <a href="criar-album.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Novo Álbum
                    </a>
                </div>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $success_message ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Todos os Álbuns
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($albums)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum álbum encontrado</h4>
                                <p class="text-muted">Crie seu primeiro álbum para começar.</p>
                                <a href="criar-album.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Criar Álbum
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome do Álbum</th>
                                            <th>Data do Evento</th>
                                            <th>Edição</th>
                                            <th>Total de Fotos</th>
                                            <th>Criado em</th>
                                            <th width="150">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($albums as $album): ?>
                                            <div class="card mb-4 shadow-sm">
                                                <div class="row g-0">
                                                    <div class="col-md-4">
                                                        <?php if (!empty($album['foto_capa'])): ?>
                                                            <img src="<?= UPLOAD_URL ?>albums/<?= $album['foto_capa'] ?>" class="img-fluid rounded-start" alt="Capa do Álbum">
                                                        <?php else: ?>
                                                            <img src="../assets/img/placeholder_album.png" class="img-fluid rounded-start" alt="Sem capa">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <h5 class="card-title fw-bold"><?= $album['nome'] ?></h5>
                                                            <p class="card-text mb-1"><i class="fas fa-calendar me-2"></i><?= formatDate($album['data_evento']) ?></p>
                                                            <p class="card-text"><i class="fas fa-bookmark me-2"></i><?= $album['edicao'] ?></p>
                                                            <a href="album.php?id=<?= $album['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye me-1"></i>Ver Álbum</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja deletar o álbum <strong id="albumName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Esta ação não pode ser desfeita. 
                        Todas as fotos do álbum também serão removidas.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="album_id" id="deleteAlbumId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Deletar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(albumId, albumName) {
            document.getElementById('deleteAlbumId').value = albumId;
            document.getElementById('albumName').textContent = albumName;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>

<?php
// Processar logout
if ($_GET['action'] ?? '' === 'logout') {
    logout();
    header('Location: index.php');
    exit;
}
?>