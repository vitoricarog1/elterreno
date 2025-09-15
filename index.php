<?php
require_once 'admin/config.php';

// Buscar os 3 últimos álbuns
try {
    $albums = $pdo->query("
        SELECT a.*, COUNT(f.id) as total_fotos 
        FROM albums a 
        LEFT JOIN fotos f ON a.id = f.album_id AND f.ativo = 1
        WHERE a.ativo = 1 
        GROUP BY a.id 
        ORDER BY a.created_at DESC 
        LIMIT 3
    ")->fetchAll();
} catch (Exception $e) {
    $albums = [];
}

// Buscar slides do carrossel
try {
    $slides = $pdo->query("
        SELECT * FROM carrossel 
        WHERE ativo = 1 
        ORDER BY ordem, created_at 
        LIMIT 5
    ")->fetchAll();
} catch (Exception $e) {
    $slides = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>El Terreno - Cataguases-MG</title>
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
     
     <!-- Inline Styles from Original -->
     <style>
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









         .ie-panel{display: none;background: #212121;padding: 10px 0;box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3);clear: both;text-align:center;position: relative;z-index: 1;} 
         html.ie-10 .ie-panel, html.lt-ie-10 .ie-panel {display: block;}
         
         .socials a{margin:3%;}
         .socials i{font-size:25px;color:white;}
         
         @import url("https://fonts.googleapis.com/css2?family=Baloo+2&display=swap");
         body {font-family: 'Mukta', sans-serif;font-size: 16px;color: #fff;text-rendering: optimizeLegibility;font-weight: initial;}
         .light {background: #f3f5f7;}
         a, a:hover {text-decoration: none;transition: color 0.3s ease-in-out;}
         #pageHeaderTitle {margin: 2rem 0;text-transform: uppercase;text-align: center;font-size: 2.5rem;}
         
         /* WhatsApp Button */
         .whatsappp {background-image: url(https://i.ibb.co/RvTJC4r/whatsapp.png);border-radius: 34px;width: 60px;font-size: 0;height: 60px;position: fixed;right: 10px;z-index: 999;display: block;bottom: 10px;background-size: 73%;background-repeat: no-repeat;background-color: #1bd741;background-position: center;}
         
         /* Google Maps */
         .google-map {border-radius: 20px;}
         
         /* Footer */
         footer {background-repeat: no-repeat;background-size: cover;min-height: 50px;}
         footer p {text-align: center;line-height: 60px;color: #fff;font-size: 16px;font-weight: 400;}
         footer p a {color: #fff;transition: all .3s;position: relative;z-index: 3;}
         footer p a:hover {opacity: 0.75;}
         
         /* Carousel Styles - Otimizado */
          .carousel-indicators {height: 4px;margin-top: 10px;overflow: hidden;}
          .carousel-indicators button {width: 25px;height: 4px;border-radius: 0;background-color: rgba(255, 255, 255, 0.5);border: none;}
          .carousel-control-prev, .carousel-control-next {width: 5%;}
          .carousel-item {background-size: cover;background-position: center;width: 100%;height: auto;min-height: 400px;}
          
          /* Desktop - Altura ajustada às imagens */
          @media (min-width: 992px) {
              .carousel-item {height: 100vh;min-height: 600px;}
              .carousel-item:nth-child(1) {background-image: url('images/pecadoF.png');}
              .carousel-item:nth-child(2) {background-image: url('images/aniverF.jpg');}
              .carousel-item:nth-child(3) {background-image: url('images/halloweenF.png');}
          }
          
          /* Mobile - Altura ajustada às imagens */
          @media (max-width: 991px) {
              .carousel-item {height: 70vh;min-height: 400px;}
              .carousel-item:nth-child(1) {background-image: url('images/pecadoS.png');}
              .carousel-item:nth-child(2) {background-image: url('images/aniverS.jpg');}
              .carousel-item:nth-child(3) {background-image: url('images/halloweenS.png');}
          }
         
         /* Text Styles */
         a, h1, h2, h3, h4, p, em {text-decoration:none;}
         
         /* Card Styles */
         h1 {color: #fff;}
         .lead {color: #aaa;}
         .wrapper {background-color: #232323;margin: 10vh;}
         .card {border: none;transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);overflow: hidden;border-radius: 20px;min-height: 450px;box-shadow: 0 0 12px 0 rgba(0, 0, 0, 0.2);}
         @media (max-width: 768px) {.card {min-height: 350px;}}
         @media (max-width: 420px) {.card {min-height: 300px;}}
         .card.card-has-bg {transition: all 1500ms cubic-bezier(0.19, 1, 0.22, 1);background-size: 120%;background-repeat: no-repeat;background-position: center center;}
         .card.card-has-bg:before {content: '';position: absolute;top: 0;right: 0;bottom: 0;left: 0;background: inherit;}
         .card.card-has-bg:hover {transform: scale(0.98);box-shadow: 0 0 5px -2px rgba(0, 0, 0, 0.3);background-size: 130%;transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);}
         .card.card-has-bg:hover .card-img-overlay {transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);background: #ff21892a;}
         .card .card-footer {background: none;border-top: none;}
         .card .card-footer .media img {border: solid 3px rgba(255, 255, 255, 0.3);}
         .card .card-title {font-weight: 800;}
         .card .card-meta {color: rgba(0, 0, 0, 0.3);text-transform: uppercase;font-weight: 500;letter-spacing: 2px;}
         .card .card-body {transition: all 500ms cubic-bezier(0.19, 1, 0.22, 1);}
         .card:hover {cursor: pointer;transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);}
         .card:hover .card-body {margin-top: 30px;transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);}
         .card .card-img-overlay {transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);}
         .postcard__img img {transition: opacity 0.3s ease-in-out;}
    

    </style>
     
     <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 </head>
<body style="background-color: #232323;">
    

    
    <div class="page" >
    
    <!--  MENU  -->
    <nav class="navbar navbar-expand-lg navbar-dark  " id="headerNav"  style="display: flex; align-items: center;padding:0rem; justify-content: space-between;; width: 100%; transition: all .5s;  ">
      <div class="container-fluid"  >

        
        
        
        <!--MENU MOBILE-->
        <div class="mobile-header ">
          <div class="mobile-nav-container">
            <!-- Toggle Button -->
            <button class="mobile-toggle" id="mobileToggle">
              <span class="hamburger-line"></span>
              <span class="hamburger-line"></span>
              <span class="hamburger-line"></span>
            </button>
            
            <!-- Logo -->
            <div class="mobile-logo">
              <img src="./images/LOGOS/2.png" alt="El Terreno Logo" />
            </div>
          </div>
        </div>
        
        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu" id="mobileMenu">
          <div class="mobile-menu-header">
            <button class="mobile-close" id="mobileClose">
              <span class="close-x"></span>
              <span class="close-x"></span>
            </button>
          </div>
          
          <nav class="mobile-nav">
            <a href="index.html" class="mobile-nav-link">INÍCIO</a>
            <a href="pub.php" class="mobile-nav-link">PUB</a>
            <a href="../galeria/index.php" class="mobile-nav-link">GALERIA</a>
            
            <a href="#contact" class="mobile-nav-link">CONTATO</a>
          </nav>
        </div>
        <!--MENU MOBILE-->


        <!--MENU DESKTOP-->
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav mx-auto">
            <li class="nav-item">
              <a class="nav-link mx-2" aria-current="page" href="index.html">INÍCIO</a>
              
            </li>
            <li class="nav-item">
              <a class="nav-link mx-2" aria-current="page" href="pub.php">pub</a>
              
            </li>
            <li class="nav-item">
              
            </li>
            <li class="nav-item d-none d-lg-block">
              <a class="nav-link mx-2" href="#">
                <img src="./images/LOGOS/2.png" height="80" alt="El Terreno Logo" />
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link mx-2" href="../galeria/index.php">GALERIA</a>
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

    <!--  BANNER  -->
    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <?php if (!empty($slides)): ?>
                <?php foreach ($slides as $index => $slide): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" 
                         <?= $slide['link'] ? 'onclick="window.open(\'' . sanitize($slide['link']) . '\', \'_blank\')" style="cursor: pointer;"' : '' ?>>
                        <picture>
                            <source media="(max-width: 768px)" srcset="<?= UPLOAD_URL ?>carrossel/<?= $slide['imagem_mobile'] ?>">
                            <img src="<?= UPLOAD_URL ?>carrossel/<?= $slide['imagem_desktop'] ?>" 
                                 class="d-block w-100" alt="<?= sanitize($slide['titulo']) ?>">
                        </picture>
                        <div class="carousel-caption d-none d-md-block">
                            <h1><?= sanitize($slide['titulo']) ?></h1>
                            <?php if ($slide['subtitulo']): ?>
                                <p><?= sanitize($slide['subtitulo']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Slides padrão caso não haja no banco -->
                <div class="carousel-item active"></div>
                <div class="carousel-item"></div>
                <div class="carousel-item"></div>
            <?php endif; ?>
        </div>
        
        <!-- Controles de navegação -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Próximo</span>
        </button>
        
        <!-- Indicadores -->
        <div class="carousel-indicators">
            <?php if (!empty($slides)): ?>
                <?php foreach ($slides as $index => $slide): ?>
                    <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="<?= $index ?>" 
                            <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> 
                            aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>


      
      <!-- Seção Tem Foto na Casa - Redesenhada -->
      <section class="photo-gallery-section" style="background: linear-gradient(135deg, rgba(109, 93, 178, 0.1) 0%, rgba(0, 0, 0, 0.9) 100%); padding: 4rem 0;">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="gallery-title" style="font-family: 'Kanit', sans-serif; font-size: 3rem; font-weight: 700; color: var(--accent-color); text-shadow: 0 0 20px rgba(109, 93, 178, 0.5); margin-bottom: 1rem;">TEM FOTO NA CASA?</h2>
            <div class="gallery-subtitle" style="background: linear-gradient(45deg, var(--primary-color), var(--accent-color)); height: 3px; width: 100px; margin: 0 auto 2rem; border-radius: 2px;"></div>
          </div>
        </div>



        <div class="container mb-5">
        <?php if (empty($albums)): ?>
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
                                
                                <?php if ($album['foto_capa'] && file_exists(UPLOAD_PATH . 'thumbs/' . $album['foto_capa'])): ?>
                                    <img src="<?= UPLOAD_URL ?>thumbs/<?= $album['foto_capa'] ?>" alt="<?= sanitize($album['nome']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-camera placeholder-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="album-info">
                                <h5 class="album-title"><?= sanitize($album['nome']) ?></h5>
                                <div class="album-meta">
                                    <div class="mb-2">
                                        <i class="fas fa-calendar me-2"></i><?= date('d/m/Y', strtotime($album['data_evento'])) ?>
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
                                <a href="galeria.php?album=<?= $album['id'] ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-eye me-2"></i>Ver Álbum
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="galeria.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-th me-2"></i>Ver Todos os Álbuns
                </a>
            </div>
        <?php endif; ?>
    </div>
        
      </section>
      
      <!-- Fotos -->
      
      <!-- Instagram Feed -->
      <section style="background: linear-gradient(135deg, rgba(109, 93, 178, 0.05) 0%, rgba(0, 0, 0, 0.95) 100%); padding: 5rem 0;" id="instagram">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="heading-primary text-accent">Siga-nos no Instagram</h2>
            <div class="postcard__bar mx-auto mb-4" style="width: 100px;"></div>
            <p class="text-body">Acompanhe os bastidores e novidades do El Terreno</p>
            <a href="https://www.instagram.com/elterrenofestas/" target="_blank" rel="noopener noreferrer" class="instagram-btn">
              <i class="fab fa-instagram me-2"></i>
              <span>@elterrenofestas</span>
              <div class="instagram-btn-glow"></div>
            </a>
          </div>
          
          <!-- Instagram Posts Grid -->
          <div class="row" id="instagram-feed">
            <!-- Posts serão carregados dinamicamente -->
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
              <div class="instagram-post">
                <div class="instagram-placeholder">
                  <i class="fab fa-instagram fa-3x text-accent mb-3"></i>
                  <p class="text-small">Carregando posts...</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
              <div class="instagram-post">
                <div class="instagram-placeholder">
                  <i class="fab fa-instagram fa-3x text-accent mb-3"></i>
                  <p class="text-small">Carregando posts...</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
              <div class="instagram-post">
                <div class="instagram-placeholder">
                  <i class="fab fa-instagram fa-3x text-accent mb-3"></i>
                  <p class="text-small">Carregando posts...</p>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
              <div class="instagram-post">
                <div class="instagram-placeholder">
                  <i class="fab fa-instagram fa-3x text-accent mb-3"></i>
                  <p class="text-small">Carregando posts...</p>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Instagram Stories Highlight -->
          <div class="row mt-5">
            <div class="col-12">
              <h3 class="heading-secondary text-center text-white mb-4">Destaques dos Stories</h3>
              <div class="instagram-stories d-flex justify-content-center flex-wrap gap-3">
                <a href="https://www.instagram.com/stories/highlights/elterrenofestas/" target="_blank" rel="noopener noreferrer" class="instagram-story">
                  <div class="story-circle">
                    <i class="fas fa-music"></i>
                  </div>
                  <span class="text-small mt-2">Eventos</span>
                </a>
                <a href="https://www.instagram.com/stories/highlights/elterrenofestas/" target="_blank" rel="noopener noreferrer" class="instagram-story">
                  <div class="story-circle">
                    <i class="fas fa-camera"></i>
                  </div>
                  <span class="text-small mt-2">Bastidores</span>
                </a>
                <a href="https://www.instagram.com/stories/highlights/elterrenofestas/" target="_blank" rel="noopener noreferrer" class="instagram-story">
                  <div class="story-circle">
                    <i class="fas fa-star"></i>
                  </div>
                  <span class="text-small mt-2">Destaques</span>
                </a>
                <a href="https://www.instagram.com/stories/highlights/elterrenofestas/" target="_blank" rel="noopener noreferrer" class="instagram-story">
                  <div class="story-circle">
                    <i class="fas fa-calendar"></i>
                  </div>
                  <span class="text-small mt-2">Agenda</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- Instagram Feed -->
        
    <!--FOOTER -->
      <section style="margin-top:7%;padding:5%;" id="contact">
        <div class="container" >
            <div class="row" style="align-items: center; flex-direction: column;" >
                <div class="col-lg-12 col-12 text-center">
                </div>
                <div class="col-lg-5 col-12 mb-4 mb-lg-0">
                    <iframe class="google-map" src="https://www.google.com/maps/embed?pb=!1m17!1m11!1m3!1d475.15134013196365!2d-42.72044905970719!3d-21.40214067441606!2m2!1f0!2f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xa2dbd7b023ed41%3A0xb8b9b08de893ed6d!2sEl%20Terreno!5e1!3m2!1spt-BR!2sbr!4v1689731990350!5m2!1spt-BR!2sbr" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mx-auto">
                    <p class="text-body">El Terreno - ROD, S/N - Vila Minalda, Cataguases - MG, 36770-970</p>
                    <div class="socials" style="display:flex;margin:auto;justify-content: center;gap: 15px;">
                        <a href="https://whatsa.me/32998589698" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.instagram.com/elterrenofestas/" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.facebook.com/elterrenofestas" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/elterrenofestas" class="social-link" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--FOOTER -->
    </div>

  <a class="whatsapp-float" href="https://wa.me/32998589698/" target="_blank" rel="noopener noreferrer">
    <i class="fab fa-whatsapp"></i>
  </a>
  
  <!-- Arquivos JS inexistentes removidos -->
  <script src="js/gallery.js"></script>
  <script src="scripts-v2.js"></script>

  <!-- JavaScript Functions from Original -->
  <script>
      function fotoboteco3ed() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function pecado() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function nossosamba() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function noflow2() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function privadinhafoto() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function enjoyfoto() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function embraza7foto() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function sejoga2025foto() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      function festahypefoto() {
          window.open('https://www.instagram.com/p/C2Ey8Ixu8Ey/?utm_source=ig_web_copy_link&igsh=MzRlODBiNWFlZA==', '_blank');
      }
      
      // Load recent albums dynamically
      async function loadRecentAlbums() {
          console.log('🚀 loadRecentAlbums() iniciada');
          try {
              console.log('📡 Fazendo fetch para api/listar-albuns.php');
              const response = await fetch('api/listar-albuns.php');
              console.log('📊 Response recebida:', response.status, response.statusText);
              const data = await response.json();
              console.log('📋 Dados parseados:', data);
              
              const albumsGrid = document.getElementById('recentAlbumsGrid');
              const loadingElement = document.getElementById('albumsLoading');
              console.log('🎯 Elementos encontrados - Grid:', albumsGrid, 'Loading:', loadingElement);
              
              if (data.success && data.albuns && data.albuns.length > 0) {
                  console.log('✅ Dados válidos - Total álbuns:', data.albuns.length);
                  // Get the 3 most recent albums
                  const recentAlbums = data.albuns.slice(0, 3);
                  console.log('🔢 Álbuns recentes selecionados:', recentAlbums.length);
                  
                  // Remove loading state
                  console.log('🗑️ Removendo estado de loading...');
                  loadingElement.remove();
                  console.log('✅ Loading removido com sucesso');
                  
                  // Create cards for recent albums
                  console.log('🎨 Iniciando criação de cards...');
                  recentAlbums.forEach((album, index) => {
                      console.log(`🎨 Criando card ${index + 1} para álbum:`, album.nome);
                      const albumCard = document.createElement('div');
                      albumCard.className = 'col-lg-4 col-md-6 col-sm-12';
                      console.log('📦 Elemento div criado com classe:', albumCard.className);
                      
                      // Format date
                      const date = new Date(album.created_at);
                      const formattedDate = date.toLocaleDateString('pt-BR', {
                          day: 'numeric',
                          month: 'long',
                          year: 'numeric'
                      });
                      
                      // Get first photo as background or use default
                      const backgroundImage = album.primeira_foto ? 
                          `url('${album.primeira_foto.caminho}')` : 
                          `url('https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&h=600')`;
                      
                      albumCard.innerHTML = `
                          <div class="gallery-card" onclick="window.location.href='galeria.php?album=${album.id}'" 
                               style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7)), ${backgroundImage}; cursor: pointer;">
                              <div class="gallery-card-content">
                                  <div class="gallery-card-header">
                                      <span class="gallery-badge">${album.total_fotos} Fotos</span>
                                  </div>
                                  <div class="gallery-card-footer">
                                      <h4 class="gallery-card-title">${album.nome}</h4>
                                      <p class="gallery-card-date"><i class="far fa-calendar-alt me-2"></i>${formattedDate}</p>
                                      <div class="gallery-card-overlay">
                                          <i class="fas fa-camera fa-2x"></i>
                                          <span>Ver Galeria</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      `;
                      console.log('📝 HTML do card criado:', albumCard.innerHTML.substring(0, 100) + '...');
                      
                      albumsGrid.appendChild(albumCard);
                      console.log(`✅ Card ${index + 1} adicionado ao grid com sucesso`);
                  });
                  console.log('🎊 Todos os cards foram criados e adicionados!');
              } else {
                  // Show empty state
                  loadingElement.innerHTML = `
                      <div style="padding: 3rem; color: var(--text-muted); text-align: center;">
                          <i class="fas fa-images fa-2x mb-3"></i>
                          <p>Nenhum álbum encontrado.</p>
                      </div>
                  `;
              }
          } catch (error) {
              console.error('Erro ao carregar álbuns:', error);
              const loadingElement = document.getElementById('albumsLoading');
              loadingElement.innerHTML = `
                  <div style="padding: 3rem; color: var(--text-muted); text-align: center;">
                      <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                      <p>Erro ao carregar álbuns. Tente novamente mais tarde.</p>
                  </div>
              `;
          }
      }
      
      // Load albums when page loads
      document.addEventListener('DOMContentLoaded', function() {
          loadRecentAlbums();
      });
  </script>

  </body>
  </html>
