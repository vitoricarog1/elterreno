<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

$success_message = '';
$error_message = '';

// Processar criação do álbum
if ($_POST['action'] ?? '' === 'create') {
    $nome = trim($_POST['nome'] ?? '');
    $data_evento = $_POST['data_evento'] ?? '';
    $edicao = trim($_POST['edicao'] ?? '');
    
    if (empty($nome) || empty($data_evento) || empty($edicao)) {
        $error_message = 'Todos os campos são obrigatórios!';
    } else {
        // Processar upload da capa
        $foto_capa = null;
        if (isset($_FILES['foto_capa']) && $_FILES['foto_capa']['error'] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $_FILES['foto_capa']['name'],
                'tmp_name' => $_FILES['foto_capa']['tmp_name'],
                'size' => $_FILES['foto_capa']['size'],
                'type' => $_FILES['foto_capa']['type']
            ];
            if (isValidImage($file)) {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = uniqid() . '_' . time() . '.' . $extension;
                if (!file_exists(UPLOAD_PATH)) {
                    mkdir(UPLOAD_PATH, 0755, true);
                }
                if (!file_exists(THUMB_PATH)) {
                    mkdir(THUMB_PATH, 0755, true);
                }
                $upload_path = UPLOAD_PATH . $filename;
                $thumb_path = THUMB_PATH . $filename;
                if (resizeImageIfNeeded($file['tmp_name'], $upload_path)) {
                    createThumbnail($upload_path, $thumb_path);
                    $foto_capa = $filename;
                }
            }
        }
        $db = getDB();
        $album_id = $db->insert(
            "INSERT INTO albums (nome, data_evento, edicao, foto_capa, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$nome, $data_evento, $edicao, $foto_capa]
        );
        
        if ($album_id) {
            $success_message = 'Álbum criado com sucesso!';
            // Limpar campos
            $_POST = [];
        } else {
            $error_message = 'Erro ao criar álbum. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Álbum - Admin</title>
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
        .form-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                    <a class="nav-link active" href="criar-album.php">
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
                <h2 class="mb-4">
                    <i class="fas fa-plus me-2"></i>Criar Novo Álbum
                </h2>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card form-card">
                            <div class="card-body p-4">
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
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="create">
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-tag me-2"></i>Nome do Álbum
                                        </label>
                                        <input type="text" name="nome" class="form-control form-control-lg" 
                                               placeholder="Ex: Casamento João & Maria"
                                               value="<?= sanitize($_POST['nome'] ?? '') ?>" required>
                                        <small class="text-muted">Nome que aparecerá na galeria</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-calendar me-2"></i>Data do Evento
                                        </label>
                                        <input type="date" name="data_evento" class="form-control form-control-lg" 
                                               value="<?= $_POST['data_evento'] ?? '' ?>" required>
                                        <small class="text-muted">Data em que o evento aconteceu</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-bookmark me-2"></i>Edição
                                        </label>
                                        <input type="text" name="edicao" class="form-control form-control-lg" 
                                               placeholder="Ex: 2024, Verão 2024, 1ª Edição"
                                               value="<?= sanitize($_POST['edicao'] ?? '') ?>" required>
                                        <small class="text-muted">Identificação da edição ou ano</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-image me-2"></i>Imagem de Capa do Álbum
                                        </label>
                                        <input type="file" name="foto_capa" class="form-control" accept="image/*">
                                        <small class="text-muted">Opcional. Se não enviar, a primeira foto será usada como capa.</small>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save me-2"></i>Criar Álbum
                                        </button>
                                        <a href="dashboard.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                                        </a>
                                    </div>
                                </form>
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

<?php
// Processar logout
if ($_GET['action'] ?? '' === 'logout') {
    logout();
    header('Location: index.php');
    exit;
}
?>