<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Verificar se ID do álbum foi fornecido
$album_id = $_GET['id'] ?? null;
if (!$album_id || !is_numeric($album_id)) {
    header('Location: ' . BASE_URL);
    exit;
}

// Buscar dados do álbum
$album = getAlbumById($album_id);
if (!$album) {
    header('Location: ' . BASE_URL);
    exit;
}

// Paginação
$page = max(1, intval($_GET['page'] ?? 1));
$total_photos = countAlbumPhotos($album_id);
$total_pages = ceil($total_photos / PHOTOS_PER_PAGE);
$photos = getAlbumPhotos($album_id, $page);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($album['nome']) ?> - Galeria de Fotos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .album-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
        }
        .photo-item {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            aspect-ratio: 1;
        }
        .photo-item:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .photo-item:hover img {
            transform: scale(1.1);
        }
        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .photo-item:hover .photo-overlay {
            opacity: 1;
        }
        .pagination-custom .page-link {
            border: none;
            margin: 0 2px;
            border-radius: 8px;
            color: #667eea;
        }
        .pagination-custom .page-item.active .page-link {
            background: #667eea;
            color: white;
        }
        .album-stats {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
                <i class="fas fa-camera me-2"></i>Galeria
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= BASE_URL ?>">Início</a>
                <a class="nav-link" href="<?= BASE_URL ?>ultimos.php">Últimos</a>
                <a class="nav-link" href="<?= BASE_URL ?>gallerydev/admin/">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Album Header -->
    <section class="album-header" style="margin-top: 76px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= BASE_URL ?>" class="text-white-50">
                                    <i class="fas fa-home"></i> Início
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white">
                                <?= sanitize($album['nome']) ?>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-bold mb-3"><?= sanitize($album['nome']) ?></h1>
                    <div class="album-stats">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h4 mb-1"><?= $total_photos ?></div>
                                <small>Fotos</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-1"><?= formatDate($album['data_evento']) ?></div>
                                <small>Data do Evento</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-1"><?= sanitize($album['edicao']) ?></div>
                                <small>Edição</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Photos Grid -->
    <div class="container">
        <?php if (empty($photos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Nenhuma foto encontrada</h4>
                <p class="text-muted">Este álbum ainda não possui fotos.</p>
                <a href="<?= BASE_URL ?>gallerydev/admin/" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Adicionar Fotos
                </a>
            </div>
        <?php else: ?>
            <!-- Info da página atual -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-images me-2"></i>
                    Página <?= $page ?> de <?= $total_pages ?> 
                    <small class="text-muted">(<?= count($photos) ?> fotos)</small>
                </h5>
                <a href="<?= BASE_URL ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <!-- Grid de Fotos -->
            <div class="photo-grid">
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-item">
                        <a href="<?= UPLOAD_URL . $photo['nome_arquivo'] ?>" 
                           data-lightbox="album-<?= $album_id ?>" 
                           data-title="<?= sanitize($album['nome']) ?>">
                            <img src="<?= THUMB_URL . $photo['nome_arquivo'] ?>" 
                                 alt="Foto do álbum <?= sanitize($album['nome']) ?>"
                                 loading="lazy">
                            <div class="photo-overlay">
                                <i class="fas fa-search-plus fa-2x text-white"></i>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Paginação do álbum">
                    <ul class="pagination pagination-custom justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?= $album_id ?>&page=<?= $page - 1 ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?id=<?= $album_id ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?= $album_id ?>&page=<?= $page + 1 ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-camera me-2"></i>
                Galeria de Fotos &copy; <?= date('Y') ?>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lightbox !== 'undefined') {
                lightbox.option({
                    'resizeDuration': 200,
                    'wrapAround': true,
                    'albumLabel': 'Foto %1 de %2'
                });
            }
        });
    </script>
</body>
</html>

<?php if ($album['foto_capa'] && file_exists(THUMB_PATH . $album['foto_capa'])): ?>
    <img src="<?= THUMB_URL . $album['foto_capa'] ?>" class="img-fluid rounded mb-3" alt="Capa do Álbum">
<?php else: ?>
    <img src="assets/img/placeholder_album.png" class="img-fluid rounded mb-3" alt="Sem capa">
<?php endif; ?>