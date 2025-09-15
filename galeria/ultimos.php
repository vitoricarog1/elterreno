<?php
require_once 'gallerydev/includes/config.php';
require_once 'gallerydev/includes/functions.php';

// Buscar os 3 últimos álbuns
$albums = getAlbums(3);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Últimos Álbuns - Galeria de Fotos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            padding: ;
            background-color: #f8f9fa;
        }
        .album-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            overflow: hidden;
        }
        .album-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .album-cover {
            height: 300px;
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
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
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
            padding: 2rem;
        }
        .album-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #2c3e50;
            font-size: 1.3rem;
        }
        .album-meta {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .photo-count {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
        }
        .recent-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff6b6b;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <!-- <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
                <i class="fas fa-camera me-2"></i>Galeria
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= BASE_URL ?>">Início</a>
                <a class="nav-link active" href="<?= BASE_URL ?>ultimos.php">Últimos</a>
                <a class="nav-link" href="<?= BASE_URL ?>gallerydev/admin/">Admin</a>
            </div>
        </div>
    </nav>-->

    <!-- Hero Section 
    <section class="hero-section" style="margin-top: 76px;">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-clock me-3"></i>Últimos Álbuns
            </h1>
            <p class="lead">Confira os álbuns mais recentes adicionados à galeria</p>
        </div>
    </section>-->

    <!-- Recent Albums -->
    <div class="container mb-5">
        <?php if (empty($albums)): ?>
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhum álbum recente</h4>
                            <p class="text-muted">Ainda não há álbuns criados.</p>
                            <a href="<?= BASE_URL ?>gallerydev/admin/" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Criar Primeiro Álbum
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($albums as $index => $album): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card album-card h-100 shadow">
                            <div class="album-cover position-relative">
                                <?php if ($index === 0): ?>
                                    <div class="recent-badge">
                                        <i class="fas fa-star me-1"></i>Mais Recente
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($album['foto_capa'] && file_exists(THUMB_PATH . $album['foto_capa'])): ?>
                                    <img src="<?= THUMB_URL . $album['foto_capa'] ?>" alt="<?= sanitize($album['nome']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-camera placeholder-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="album-info">
                                <h5 class="album-title"><?= sanitize($album['nome']) ?></h5>
                                <div class="album-meta">
                                    <div class="mb-2">
                                        <i class="fas fa-calendar me-2"></i><?= formatDate($album['data_evento']) ?>
                                    </div>
                                    <div class="mb-3">
                                        <i class="fas fa-tag me-2"></i><?= sanitize($album['edicao']) ?>
                                    </div>
                                    <div class="mb-3">
                                        <span class="photo-count">
                                            <i class="fas fa-images me-1"></i><?= $album['total_fotos'] ?> fotos
                                        </span>
                                    </div>
                                </div>
                                <a href="<?= BASE_URL ?>gallerydev/album.php?id=<?= $album['id'] ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-eye me-2"></i>Ver Álbum
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="<?= BASE_URL ?>" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-th me-2"></i>Ver Todos os Álbuns
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-camera me-2"></i>
                Galeria de Fotos &copy; <?= date('Y') ?> - Últimos Álbuns
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>