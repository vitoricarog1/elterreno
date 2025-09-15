<?php
// Verificar se um álbum específico foi solicitado
$albumId = isset($_GET['album']) ? (int)$_GET['album'] : null;
$albumData = null;
$fotos = [];
$erro = null;

if ($albumId) {
    // Carregar dados do álbum específico e fotos diretamente
    include_once 'galeria/gallerydev/includes/config.php';
    try {
        // Buscar álbum
        $stmt = $pdo->prepare("SELECT * FROM albuns WHERE id = ?");
        $stmt->execute([$albumId]);
        if ($stmt && method_exists($stmt, 'fetch')) {
            if ($stmt instanceof PDOStatement) {
                $albumData = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $albumData = null;
            }
        } else {
            $albumData = null;
        }
        
        if ($albumData) {
            // Buscar fotos do álbum usando created_at
            $stmt = $pdo->prepare("SELECT * FROM fotos WHERE album_id = ? ORDER BY created_at DESC");
            $stmt->execute([$albumId]);
            if ($stmt && method_exists($stmt, 'fetchAll')) {
$fotos = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fotos[] = $row;
}
            } else {
                $fotos = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $fotos[] = $row;
                }
            }
        }
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $albumData ? 'Álbum: ' . htmlspecialchars($albumData['nome']) . ' - El Terreno - Cataguases-MG' : 'Galeria - El Terreno - Cataguases-MG'; ?></title>
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
                            <a class="nav-link mx-2" aria-current="page" href="index.php">INÍCIO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-2" aria-current="page" href="pub.php">PUB</a>
                        </li>
                        <li class="nav-item">
                        </li>
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link mx-2" href="#">
                                <img src="./images/LOGOS/2.png" height="80" alt="El Terreno Logo" />
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-2" href="galeria.php">GALERIA</a>
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
                    <?php if ($albumData): ?>
                        <div class="mb-3">
                            <a href="galeria.php" class="btn" style="background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 25px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-arrow-left"></i>
                                Voltar para todos os álbuns
                            </a>
                        </div>
                        <h1 class="heading-primary text-accent" style="color: var(--accent-color);"><?php echo htmlspecialchars($albumData['nome']); ?></h1>
                        <div class="postcard__bar mx-auto mb-4" style="width: 100px; height: 4px; background: var(--gradient-primary); border-radius: 2px;"></div>
                        <p class="text-body" style="color: var(--text-muted);"><?php echo htmlspecialchars($albumData['descricao']); ?></p>
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
            <?php if ($albumData): ?>
                <!-- Album Photos View -->
                <div class="mb-5">
                    <p class="text-body mb-3" style="color: var(--text-muted);">Criado em: <?php echo date('d/m/Y', strtotime($albumData['created_at'])); ?></p>
                </div>

                <?php if ($erro): ?>
                    <!-- Error State -->
                    <div class="text-center" style="padding: 4rem 0;">
                        <div style="width: 80px; height: 80px; background: rgba(255, 36, 142, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: var(--secondary-color);"></i>
                        </div>
                        <h3 class="heading-secondary" style="color: var(--text-light); margin-bottom: 1rem;">Erro ao carregar fotos</h3>
                        <p class="text-body" style="color: var(--text-muted);"><?php echo htmlspecialchars($erro); ?></p>
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
                    <div class="row" style="margin-top: 2rem;">
                        <?php foreach ($fotos as $index => $foto): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="gallery-photo-card" onclick="openPhotoModal(<?php echo $index; ?>)" style="background-image: url('<?php echo htmlspecialchars($foto['caminho']); ?>'); background-size: cover; background-position: center; height: 250px; border-radius: var(--border-radius); cursor: pointer; position: relative; overflow: hidden; transition: var(--transition);">
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
                <?php endif; ?>
            <?php else: ?>
                <!-- Albums List View -->
                <!-- Loading State -->
                <div id="loading" class="text-center" style="padding: 3rem 0;">
                    <div style="width: 48px; height: 48px; border: 3px solid rgba(109, 93, 178, 0.3); border-top: 3px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                    <p style="color: var(--text-muted);">Carregando álbuns...</p>
                </div>

                <!-- Albums Grid -->
                <div id="albums-grid" class="row"></div>

                <!-- Empty State -->
                <div id="empty-state" class="text-center" style="padding: 4rem 0; display: none;">
                    <div style="width: 80px; height: 80px; background: rgba(109, 93, 178, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                        <i class="fas fa-images" style="font-size: 2rem; color: var(--primary-color);"></i>
                    </div>
                    <h3 class="heading-secondary" style="color: var(--text-light); margin-bottom: 1rem;">Nenhum álbum encontrado</h3>
                    <p class="text-body" style="color: var(--text-muted);">Crie seu primeiro álbum para começar a organizar suas fotos.</p>
                </div>
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

    <!-- Photo Modal -->
    <div id="photo-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="relative max-w-4xl max-h-full p-4">
            <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain">
            <div class="absolute bottom-4 left-4 right-4 text-white text-center">
                <div class="flex justify-between items-center">
                    <button onclick="navigateModal('prev')" class="bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <a id="modal-download" href="" download="" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">
                        Download
                    </a>
                    <button onclick="navigateModal('next')" class="bg-black bg-opacity-50 hover:bg-opacity-75 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($albumData && !empty($fotos)): ?>
    <script>
        // Define allPhotos for modal.js
        const allPhotos = <?php echo json_encode($fotos); ?>;
        
        // Fallback modal functions in case modal.js fails to load
        window.openPhotoModal = window.openPhotoModal || function(photoIndex) {
            console.log('Modal function called with index:', photoIndex);
            if (!allPhotos || !allPhotos[photoIndex]) {
                console.error('Photo not found at index:', photoIndex);
                return;
            }
            
            const modal = document.getElementById('photo-modal');
            const modalImage = document.getElementById('modal-image');
            const modalDownload = document.getElementById('modal-download');
            
            if (!modal || !modalImage) {
                console.error('Modal elements not found');
                return;
            }
            
            const photo = allPhotos[photoIndex];
            modalImage.src = photo.caminho;
            modalImage.alt = photo.nome_original;
            
            if (modalDownload) {
                modalDownload.href = photo.caminho;
                modalDownload.download = photo.nome_original;
            }
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            window.currentPhotoIndex = photoIndex;
            window.modalPhotos = allPhotos;
        };
        
        window.closePhotoModal = window.closePhotoModal || function() {
            const modal = document.getElementById('photo-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        };
        
        window.navigateModal = window.navigateModal || function(direction) {
             if (!window.modalPhotos || !window.modalPhotos.length) return;
             
             if (direction === 'next') {
                 window.currentPhotoIndex = (window.currentPhotoIndex + 1) % window.modalPhotos.length;
             } else if (direction === 'prev') {
                 window.currentPhotoIndex = (window.currentPhotoIndex - 1 + window.modalPhotos.length) % window.modalPhotos.length;
             }
             
             const photo = window.modalPhotos[window.currentPhotoIndex];
             const modalImage = document.getElementById('modal-image');
             const modalDownload = document.getElementById('modal-download');
             
             if (modalImage) {
                 modalImage.src = photo.caminho;
                 modalImage.alt = photo.nome_original;
             }
             
             if (modalDownload) {
                 modalDownload.href = photo.caminho;
                 modalDownload.download = photo.nome_original;
             }
         };
         
         // Add event listeners for modal functionality
         document.addEventListener('DOMContentLoaded', function() {
             const modal = document.getElementById('photo-modal');
             if (modal) {
                 // Close modal when clicking outside
                 modal.addEventListener('click', function(e) {
                     if (e.target === modal) {
                         closePhotoModal();
                     }
                 });
             }
             
             // Close modal with ESC key
             document.addEventListener('keydown', function(e) {
                 if (e.key === 'Escape') {
                     closePhotoModal();
                 }
             });
         });
    </script>
    <?php endif; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="js/modal.js" onerror="console.log('modal.js failed to load, using fallback functions')"></script>
    <?php if (!$albumData): ?>
    <script src="js/gallery.js"></script>
    <script>
        // Initialize albums loading only for main gallery
        document.addEventListener('DOMContentLoaded', function() {
            loadAlbums();
        });
    </script>
    <?php endif; ?>
    
    <!-- Menu Toggle Script -->
     <script>
         function toggleMenu() {
             const mobileMenu = document.getElementById('mobile-menu');
             mobileMenu.classList.toggle('show');
         }
     </script>

</body>
</html>