<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

// Estatísticas
$db = getDB();
$stats = [
    'total_albums' => $db->fetchOne("SELECT COUNT(*) as total FROM albums")['total'],
    'total_photos' => $db->fetchOne("SELECT COUNT(*) as total FROM fotos")['total'],
    'recent_albums' => getAlbums(5)
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Galeria</title>
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
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
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
                    <small class="opacity-75">
                        Olá, <?= sanitize($_SESSION[ADMIN_SESSION_NAME]['username']) ?>
                    </small>
                </div>
                
                <nav class="nav flex-column">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="criar-album.php">
                        <i class="fas fa-plus me-2"></i>Criar Álbum
                    </a>
                    <a class="nav-link" href="gerenciar-albums.php">
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
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                    <div class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        <?= date('d/m/Y H:i') ?>
                    </div>
                </div>
                
                <!-- Estatísticas -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-folder fa-3x text-primary mb-3"></i>
                                <h3 class="text-primary"><?= $stats['total_albums'] ?></h3>
                                <p class="text-muted mb-0">Álbuns Criados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-images fa-3x text-success mb-3"></i>
                                <h3 class="text-success"><?= $stats['total_photos'] ?></h3>
                                <p class="text-muted mb-0">Fotos Enviadas</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ações Rápidas -->
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-plus-circle fa-3x text-info mb-3"></i>
                                <h5>Criar Álbum</h5>
                                <p class="text-muted">Adicione um novo álbum à galeria</p>
                                <a href="criar-album.php" class="btn btn-info">
                                    <i class="fas fa-plus me-2"></i>Criar
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-upload fa-3x text-warning mb-3"></i>
                                <h5>Upload Fotos</h5>
                                <p class="text-muted">Envie fotos para os álbuns</p>
                                <a href="upload-fotos.php" class="btn btn-warning">
                                    <i class="fas fa-upload me-2"></i>Upload
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-cog fa-3x text-secondary mb-3"></i>
                                <h5>Gerenciar</h5>
                                <p class="text-muted">Edite ou remova álbuns</p>
                                <a href="gerenciar-albums.php" class="btn btn-secondary">
                                    <i class="fas fa-cog me-2"></i>Gerenciar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Álbuns Recentes -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Álbuns Recentes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($stats['recent_albums'])): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhum álbum criado ainda</p>
                                <a href="criar-album.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Criar Primeiro Álbum
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Data do Evento</th>
                                            <th>Edição</th>
                                            <th>Fotos</th>
                                            <th>Criado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stats['recent_albums'] as $album): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= sanitize($album['nome']) ?></strong>
                                                </td>
                                                <td><?= formatDate($album['data_evento']) ?></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= sanitize($album['edicao']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= $album['total_fotos'] ?> fotos
                                                    </span>
                                                </td>
                                                <td><?= formatDate($album['created_at']) ?></td>
                                                <td>
                                                    <a href="../album.php?id=<?= $album['id'] ?>" 
                                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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