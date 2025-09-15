<?php
require_once 'gallerydev/includes/config.php';
require_once 'gallerydev/includes/functions.php';

// Buscar todos os álbuns
$albums = getAlbums();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria - El Terreno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .album-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            overflow: hidden;
        }
        .album-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .album-cover {
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }
        .album-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }
        .album-cover .placeholder-icon {
            z-index: 1;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
        }
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .album-info {
            padding: 1.5rem;
        }
        .album-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        .album-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .photo-count {
            background: #e9ecef;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
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

    <!-- Hero Section -->
    <section class="hero-section" style="margin-top: 76px;">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-images me-3"></i>Galeria de Fotos
            </h1>
            <p class="lead">Explore nossos álbuns de momentos especiais</p>
        </div>
    </section>

    <!-- Albums Grid -->
    <div class="container mb-5">
        <?php if (empty($albums)): ?>
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhum álbum encontrado</h4>
                            <p class="text-muted">Ainda não há álbuns criados.</p>
                            <a href="<?= BASE_URL ?>gallerydev/admin/" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Criar Primeiro Álbum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($albums as $album): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <?php if ($album['foto_capa'] && file_exists(THUMB_PATH . $album['foto_capa'])): ?>
                                <img src="<?= THUMB_URL . $album['foto_capa'] ?>" class="card-img-top" alt="<?= sanitize($album['nome']) ?>">
                            <?php else: ?>
                                <img src="gallerydev/assets/img/placeholder_album.png" class="card-img-top" alt="Sem capa">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title fw-bold"><?= sanitize($album['nome']) ?></h5>
                                <p class="card-text mb-1"><i class="fas fa-calendar me-2"></i><?= formatDate($album['data_evento']) ?></p>
                                <p class="card-text"><i class="fas fa-bookmark me-2"></i><?= sanitize($album['edicao']) ?></p>
                                <a href="gallerydev/album.php?id=<?= $album['id'] ?>" class="btn btn-primary btn-sm mt-2"><i class="fas fa-eye me-1"></i>Ver Álbum</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
</body>
</html>