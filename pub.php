<?php require_once 'admin/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>El Terreno Pub - Cardápio Digital</title>
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
    
    <!-- Pub CSS -->
    <link rel="stylesheet" href="pub-styles.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        <a href="index.php" class="mobile-nav-link">INÍCIO</a>
                        <a href="pub.php" class="mobile-nav-link active">PUB</a>
                        <a href="./galeria.php" class="mobile-nav-link">GALERIA</a>
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
                            <a class="nav-link mx-2 active" aria-current="page" href="pub.php">PUB</a>
                        </li>
                        <li class="nav-item">
                        </li>
                        <li class="nav-item d-none d-lg-block">
                            <a class="nav-link mx-2" href="#">
                                <img src="./images/LOGOS/2.png" height="80" alt="El Terreno Logo" />
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-2" href="./galeria.php">GALERIA</a>
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

        <!--  BANNER PUB  -->
        <div id="pubCarousel" class="pub-carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active pub-slide-1">
                    <div class="carousel-caption">
                        <h1>EL TERRENO PUB</h1>
                        <p>Cardápio Digital - Sabores Únicos</p>
                    </div>
                </div>
                <div class="carousel-item pub-slide-2">
                    <div class="carousel-caption">
                        <h1>PETISCOS ESPECIAIS</h1>
                        <p>Delícias para Compartilhar</p>
                    </div>
                </div>
                <div class="carousel-item pub-slide-3">
                    <div class="carousel-caption">
                        <h1>BEBIDAS ARTESANAIS</h1>
                        <p>Drinks e Cervejas Especiais</p>
                    </div>
                </div>
            </div>
            
            <!-- Controles de navegação -->
            <button class="carousel-control-prev" type="button" data-bs-target="#pubCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#pubCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
            
            <!-- Indicadores -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#pubCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#pubCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#pubCarousel" data-bs-slide-to="2"></button>
            </div>
        </div>
        <!--  BANNER PUB  -->

        <!-- CARDÁPIO -->
        <div class="menu-container">
            
            <?php include 'includes/cardapio-content.php'; ?>
            <!-- SEÇÃO PETISCOS -->
            <section class="menu-section" id="petiscos">
                <div class="container">
                    <div class="section-header">
                        <h2><i class="fas fa-utensils"></i> PETISCOS</h2>
                        <p>Deliciosos petiscos para compartilhar</p>
                    </div>
                    
                    <div class="menu-grid" id="petiscosGrid">
                        <!-- Petiscos serão gerados dinamicamente -->
                    </div>
                </div>
            </section>
            
            <!-- SEÇÃO BEBIDAS -->
            <section class="menu-section" id="bebidas">
                <div class="container">
                    <div class="section-header">
                        <h2><i class="fas fa-cocktail"></i> BEBIDAS</h2>
                        <p>Drinks especiais e cervejas artesanais</p>
                    </div>
                    
                    <div class="menu-grid" id="bebidasGrid">
                        <!-- Bebidas serão geradas dinamicamente -->
                    </div>
                </div>
            </section>
        </div>
        <!-- CARDÁPIO -->

        <!-- MODAL DETALHES -->
        <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="itemModalLabel">Detalhes do Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- Conteúdo será inserido dinamicamente -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="orderBtn">Pedir Agora</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL DETALHES -->

        <!-- FOOTER -->
        <footer id="contact" style="background-image: url('./images/footer-bg.jpg'); background-repeat: no-repeat; background-size: cover; min-height: 200px; margin-top: 50px;">
            <div class="container">
                <div class="row py-5">
                    <div class="col-md-4 text-center">
                        <h5 class="text-white mb-3">CONTATO</h5>
                        <p class="text-white"><i class="fas fa-phone"></i> (32) 99999-9999</p>
                        <p class="text-white"><i class="fas fa-envelope"></i> contato@elterreno.com</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h5 class="text-white mb-3">ENDEREÇO</h5>
                        <p class="text-white"><i class="fas fa-map-marker-alt"></i> Rua Principal, 123<br>Cataguases - MG</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h5 class="text-white mb-3">REDES SOCIAIS</h5>
                        <div class="socials">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center py-3" style="background-color: rgba(0,0,0,0.3);">
                <p class="text-white mb-0">© 2025 El Terreno Pub. Todos os direitos reservados.</p>
            </div>
        </footer>
        <!-- FOOTER -->

        <!-- WhatsApp Button -->
        <a href="https://wa.me/5532999999999" class="whatsappp" target="_blank"></a>
        
    </div>

    <!-- Scripts -->
    <script src="pub-scripts.js"></script>
    <script src="scripts-v2.js"></script>
    
</body>
</html>