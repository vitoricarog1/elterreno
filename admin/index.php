<?php
require_once 'config.php';
requireLogin();

// Estatísticas
$stats = [
    'albums' => $pdo->query("SELECT COUNT(*) FROM albums WHERE ativo = 1")->fetchColumn(),
    'fotos' => $pdo->query("SELECT COUNT(*) FROM fotos WHERE ativo = 1")->fetchColumn(),
    'carrossel' => $pdo->query("SELECT COUNT(*) FROM carrossel WHERE ativo = 1")->fetchColumn(),
    'cardapio' => $pdo->query("SELECT COUNT(*) FROM cardapio_itens WHERE ativo = 1")->fetchColumn()
];

// Últimos álbuns
$ultimos_albums = $pdo->query("
    SELECT a.*, COUNT(f.id) as total_fotos 
    FROM albums a 
    LEFT JOIN fotos f ON a.id = f.album_id AND f.ativo = 1
    WHERE a.ativo = 1 
    GROUP BY a.id 
    ORDER BY a.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - El Terreno Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6d5db2;
            --secondary-color: #ff248e;
            --accent-color: #7f67db;
            --gradient: linear-gradient(135deg, #6d5db2, #ff248e, #7f67db);
        }
        
        body {
            font-family: 'Lato', sans-serif;
            background: #f8f9fa;
        }
        
        .sidebar {
            background: var(--gradient);
            min-height: 100vh;
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .recent-albums {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .album-item {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
            transition: background 0.3s ease;
        }
        
        .album-item:hover {
            background: #f8f9fa;
        }
        
        .album-item:last-child {
            border-bottom: none;
        }
        
        .header-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .action-btn {
            background: var(--gradient);
            border: none;
            border-radius: 12px;
            color: white;
            padding: 1rem 1.5rem;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .action-btn:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(109, 93, 178, 0.3);
        }
    </style>
</head>
<body>
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="mb-1">El Terreno</h4>
                    <small class="opacity-75">Painel Admin</small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="albums.php">
                        <i class="fas fa-images me-2"></i>Gerenciar Álbuns
                    </a>
                    <a class="nav-link" href="carrossel.php">
                        <i class="fas fa-sliders-h me-2"></i>Gerenciar Carrossel
                    </a>
                    <a class="nav-link" href="cardapio.php">
                        <i class="fas fa-utensils me-2"></i>Gerenciar Cardápio
                    </a>
                    <hr class="my-3 opacity-50">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Site
                    </a>
                    <a class="nav-link" href="../pub.php" target="_blank">
                        <i class="fas fa-glass-cheers me-2"></i>Ver Cardápio
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </h1>
                    <div class="text-muted">
                        <i class="fas fa-user me-1"></i>
                        Olá, <?= sanitize($_SESSION['admin_user']) ?>
                    </div>
                </div>
                
                <!-- Estatísticas -->
                <div class="row g-4 mb-5">
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-number text-primary"><?= $stats['albums'] ?></div>
                            <div class="stat-label">Álbuns</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-number text-success"><?= $stats['fotos'] ?></div>
                            <div class="stat-label">Fotos</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-number text-warning"><?= $stats['carrossel'] ?></div>
                            <div class="stat-label">Slides</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-number text-info"><?= $stats['cardapio'] ?></div>
                            <div class="stat-label">Itens Cardápio</div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Ações Rápidas -->
                    <div class="col-md-4">
                        <div class="quick-actions">
                            <h5 class="mb-3">
                                <i class="fas fa-bolt me-2"></i>Ações Rápidas
                            </h5>
                            <a href="albums.php?action=create" class="action-btn">
                                <i class="fas fa-plus me-2"></i>Criar Álbum
                            </a>
                            <a href="carrossel.php?action=create" class="action-btn">
                                <i class="fas fa-image me-2"></i>Adicionar Slide
                            </a>
                            <a href="cardapio.php?action=create" class="action-btn">
                                <i class="fas fa-utensils me-2"></i>Adicionar Item
                            </a>
                        </div>
                    </div>
                    
                    <!-- Últimos Álbuns -->
                    <div class="col-md-8">
                        <div class="recent-albums">
                            <div class="p-3 border-bottom">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Últimos Álbuns
                                </h5>
                            </div>
                            <div class="p-0">
                                <?php if (empty($ultimos_albums)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Nenhum álbum criado ainda</p>
                                        <a href="albums.php?action=create" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Criar Primeiro Álbum
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($ultimos_albums as $album): ?>
                                        <div class="album-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?= sanitize($album['nome']) ?></h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= formatDate($album['data_evento']) ?>
                                                        <span class="ms-3">
                                                            <i class="fas fa-images me-1"></i>
                                                            <?= $album['total_fotos'] ?> fotos
                                                        </span>
                                                    </small>
                                                </div>
                                                <div>
                                                    <a href="albums.php?action=edit&id=<?= $album['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="p-3 text-center border-top">
                                        <a href="albums.php" class="btn btn-outline-primary">
                                            <i class="fas fa-eye me-2"></i>Ver Todos
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>