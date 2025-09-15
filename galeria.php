<?php
require_once 'admin/config.php';

$albumId = isset($_GET['album']) ? (int)$_GET['album'] : null;
$album = null;
$fotos = [];
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = PHOTOS_PER_PAGE;
$offset = ($page - 1) * $per_page;

if ($albumId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND ativo = 1");
        $stmt->execute([$albumId]);
        $album = $stmt->fetch();
        
        if ($album) {
            // Contar total de fotos
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM fotos WHERE album_id = ? AND ativo = 1");
            $count_stmt->execute([$album_id]);
            $total_fotos = $count_stmt->fetchColumn();
            $total_pages = ceil($total_fotos / $per_page);
            
            // Buscar fotos da página atual
            $stmt = $pdo->prepare("
                SELECT * FROM fotos 
                WHERE album_id = ? AND ativo = 1 
                ORDER BY ordem, created_at 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$album_id, $per_page, $offset]);
            $fotos = $stmt->fetchAll();
            
            // Buscar todas as fotos para o modal
            $all_stmt = $pdo->prepare("
                SELECT * FROM fotos 
                WHERE album_id = ? AND ativo = 1 
                ORDER BY ordem, created_at
            ");
            $all_stmt->execute([$album_id]);
            $all_fotos = $all_stmt->fetchAll();
        }
    } catch (Exception $e) {
        $album = null;
        $fotos = [];
    }
} else {
    // Listar todos os álbuns
    try {
        $albums = $pdo->query("
            SELECT a.*, COUNT(f.id) as total_fotos 
            FROM albums a 
            LEFT JOIN fotos f ON a.id = f.album_id AND f.ativo = 1
            WHERE a.ativo = 1 
            GROUP BY a.id 
            ORDER BY a.created_at DESC
        ")->fetchAll();
    } catch (Exception $e) {
        $albums = [];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $album ? 'Álbum: ' . htmlspecialchars($album['nome']) . ' - El Terreno - Cataguases-MG' : 'Galeria - El Terreno - Cataguases-MG'; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@1,200&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Luxurious+Roman&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:400,700%7COpen+Sans:400,600,700%7CSource+Code+Pro:300,400,500,600,700,900%7CNothing+You+Could+Do%7CPoppins:400,500">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles-v2.css">
    
    <!-- Menu CSS -->
    <link rel="stylesheet" href="menu-styles.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
</head>
<body style="background-color: #232323;">
    
    <div class="page">
    
        <!--  MENU  -->
        <nav class="navbar navbar-expand-lg navbar-dark" id="headerNav" style="display: flex; align-items: center;padding:0rem; justify-content: space-between;; width: 100%; transition: all .5s;">
            <div class="container-fluid">
                
                <!--MENU MOBILE-->
                <div class="mobile-header">
                    <div class="mobile-nav-container">
                        <!-- Toggle Button -->
                        <button class="mobile-toggle" id="mobileToggle">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </button>
                        
                        <!-- Logo -->
                        <div class="mobile-logo">
                            <img src="./images/LOGOS/2.png" height="60" alt="El Terreno Logo" />
                        </div>
                    </div>
                    
                    <!-- Mobile Navigation -->
                    <nav class="mobile-nav" id="mobileNav">
                        <a href="index.php" class="mobile-nav-link">INÍCIO</a>
                        <a href="pub.php" class="mobile-nav-link">PUB</a>
                        <a href="galeria.php" class="mobile-nav-link">GALERIA</a>
                        <a href="#contact" class="mobile-nav-link">CONTATO</a>
                    </nav>
                </div>
                <!--MENU MOBILE-->


                <!--MENU DESKTOP-->
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link mx-2" href="index.php">INÍCIO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-2" href="pub.php">PUB</a>
                        </li>
                        <li class="nav-item">
                        </li>
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link mx-2" href="#">
                                <img src="./images/LOGOS/2.png" height="80" alt="El Terreno Logo" />
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-2 active" href="galeria.php">GALERIA</a>
                        </li>
                        <li class="nav-item">
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-2" href="#contact">CONTATO</a>
                        </li>
                    </ul>
                </div>
                <!--MENU DESKTOP-->

            </div>
        </nav>
        <!--  MENU  -->

        <!-- Header -->
        <header style="background: linear-gradient(135deg, rgba(109, 93, 178, 0.1) 0%, rgba(0, 0, 0, 0.9) 100%); padding: 2rem 0;">
            <div class="container">
                <div class="text-center">
                    <?php if ($album): ?>
                        <div class="mb-3">
                            <a href="galeria.php" class="btn" style="background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 25px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-arrow-left"></i>
                                Voltar para todos os álbuns
                            </a>
                        </div>
                        <h1 class="heading-primary text-accent" style="color: var(--accent-color);"><?php echo htmlspecialchars($album['nome']); ?></h1>
                        <div class="postcard__bar mx-auto mb-4" style="width: 100px; height: 4px; background: var(--gradient-primary); border-radius: 2px;"></div>
                        <p class="text-body" style="color: var(--text-muted);"><?php echo htmlspecialchars($album['descricao'] ?? ''); ?></p>
                    <?php else: ?>
                        <h1 class="heading-primary text-accent" style="color: var(--accent-color);">Galeria de Fotos</h1>
                        <div class="postcard__bar mx-auto mb-4" style="width: 100px; height: 4px; background: var(--gradient-primary); border-radius: 2px;"></div>
                        <p class="text-body" style="color: var(--text-muted);">Explore nossos álbuns de fotos dos eventos</p>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main style="padding: 3rem 0;">
            <div class="container">
            <?php if ($album): ?>
                <!-- Album Photos View -->
                <div class="mb-5">
                    <p class="text-body mb-3" style="color: var(--text-muted);">Criado em: <?php echo date('d/m/Y', strtotime($album['created_at'])); ?></p>
                </div>

                <?php if (isset($erro) && $erro): ?>
                    <!-- Error State -->
                    <div class="text-center" style="padding: 4rem 0;">
                        <div style="width: 80px; height: 80px; background: rgba(255, 36, 142, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: var(--secondary-color);"></i>
                        </div>
                        <h3 class="heading-secondary" style="color: var(--text-light); margin-bottom: 1rem;">Erro ao carregar fotos</h3>
                        <p class="text-body" style="color: var(--text-muted);"><?php echo htmlspecialchars($erro ?? ''); ?></p>
                    </div>
                <?php elseif (empty($fotos)): ?>
                    <!-- Photos Empty State -->
                    <div class="text-center" style="padding: 4rem 0;">
                        <div style="width: 80px; height: 80px; background: rgba(109, 93, 178, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                            <i class="fas fa-camera" style="font-size: 2rem; color: var(--primary-color);"></i>
                        </div>
                        <h3 class="heading-secondary" style="color: var(--text-light); margin-bottom: 1rem;">Nenhuma foto encontrada</h3>
                        <p class="text-body" style="color: var(--text-muted);">Este álbum ainda não possui fotos.</p>
                    </div>
                <?php else: ?>
                    <!-- Photos Grid -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="text-body mb-0" style="color: var(--text-muted);">
                            Página <?= $page ?> de <?= $total_pages ?? 1 ?> (<?= count($fotos) ?> fotos)
                        </p>
                    </div>
                    
                    <div class="row g-3" style="margin-top: 1rem;">
                        <?php foreach ($fotos as $index => $foto): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="gallery-photo-card" style="position: relative; aspect-ratio: 1; border-radius: var(--border-radius); overflow: hidden; cursor: pointer; transition: var(--transition);">
                                    <a href="<?= UPLOAD_URL ?>albums/<?= $foto['nome_arquivo'] ?>" 
                                       data-lightbox="album-<?= $album_id ?>" 
                                       data-title="<?= htmlspecialchars($foto['nome_original']) ?>">
                                        <img src="<?= UPLOAD_URL ?>thumbs/<?= $foto['nome_arquivo'] ?>" 
                                             alt="<?= htmlspecialchars($foto['nome_original']) ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                    <div class="gallery-photo-overlay" style="position: absolute; inset: 0; background: linear-gradient(rgba(0,0,0,0), rgba(0,0,0,0.7)); opacity: 0; transition: var(--transition); display: flex; align-items: end; padding: 1rem;">
                                        <div>
                                            <p style="color: white; font-weight: 600; margin: 0; font-size: 0.9rem;"><?php echo htmlspecialchars($foto['nome_original']); ?></p>
                                            <p style="color: rgba(255,255,255,0.8); margin: 0; font-size: 0.8rem;"><?php echo date('d/m/Y', strtotime($foto['created_at'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?album=<?= $album_id ?>&page=<?= $page - 1 ?>">Anterior</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?album=<?= $album_id ?>&page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?album=<?= $album_id ?>&page=<?= $page + 1 ?>">Próximo</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <!-- Albums List View -->
                <!-- Loading State -->
                <div id="loading" class="text-center" style="padding: 3rem 0;">
                    <div style="width: 48px; height: 48px; border: 3px solid rgba(109, 93, 178, 0.3); border-top: 3px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                    <p style="color: var(--text-muted);">Carregando álbuns...</p>
                </div>

                <!-- Albums Grid -->
                <div id="albums-grid" class="row g-4">
                    <?php if (isset($albums) && !empty($albums)): ?>
                        <?php foreach ($albums as $album_item): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 shadow-sm" style="border-radius: 15px; overflow: hidden; transition: transform 0.3s ease;">
                                    <div style="height: 250px; position: relative; overflow: hidden;">
                                        <?php if ($album_item['foto_capa'] && file_exists(UPLOAD_PATH . 'thumbs/' . $album_item['foto_capa'])): ?>
                                            <img src="<?= UPLOAD_URL ?>thumbs/<?= $album_item['foto_capa'] ?>" 
                                                 class="card-img-top" alt="<?= htmlspecialchars($album_item['nome']) ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                <i class="fas fa-camera fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($album_item['nome']) ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($album_item['data_evento'])) ?>
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-images me-1"></i>
                                                <?= $album_item['total_fotos'] ?> fotos
                                            </small>
                                        </p>
                                        <a href="?album=<?= $album_item['id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-eye me-1"></i>Ver Álbum
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Empty State -->
                <?php if (isset($albums) && empty($albums)): ?>
                <div id="empty-state" class="text-center" style="padding: 4rem 0;">
                    <div style="width: 80px; height: 80px; background: rgba(109, 93, 178, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                        <i class="fas fa-images" style="font-size: 2rem; color: var(--primary-color);"></i>
                    </div>
                    <h3 class="heading-secondary" style="color: var(--text-light); margin-bottom: 1rem;">Nenhum álbum encontrado</h3>
                    <p class="text-body" style="color: var(--text-muted);">Ainda não há álbuns criados.</p>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
        </main>

        <!-- Footer -->
        <footer style="background: linear-gradient(135deg, rgba(109, 93, 178, 0.05) 0%, rgba(0, 0, 0, 0.95) 100%); padding: 3rem 0; margin-top: 4rem;">
            <div class="container">
                <div class="text-center">
                    <p style="color: var(--text-muted); margin: 0;">&copy; 2025 El Terreno - Cataguases-MG. Todos os direitos reservados.</p>
                </div>
            </div>
        </footer>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    
    <script>
        // Configurar lightbox
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': 'Foto %1 de %2'
            });
        }
        
        // Hover effects
        document.querySelectorAll('.gallery-photo-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.querySelector('.gallery-photo-overlay').style.opacity = '1';
            });
            card.addEventListener('mouseleave', function() {
                this.querySelector('.gallery-photo-overlay').style.opacity = '0';
            });
        });
        
        // Card hover effects
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>

</body>
</html>