<?php
require_once 'config.php';
requireLogin();

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);
$message = '';
$error = '';

// Processar ações
if ($_POST) {
    switch ($_POST['action']) {
        case 'create':
            $titulo = trim($_POST['titulo']);
            $subtitulo = trim($_POST['subtitulo']);
            $link = trim($_POST['link']);
            
            if ($titulo) {
                $imagem_desktop = '';
                $imagem_mobile = '';
                
                // Upload imagem desktop
                if (isset($_FILES['imagem_desktop']) && $_FILES['imagem_desktop']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['imagem_desktop']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $imagem_desktop = 'desktop_' . uniqid() . '.' . $ext;
                        move_uploaded_file($_FILES['imagem_desktop']['tmp_name'], UPLOAD_PATH . 'carrossel/' . $imagem_desktop);
                    }
                }
                
                // Upload imagem mobile
                if (isset($_FILES['imagem_mobile']) && $_FILES['imagem_mobile']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['imagem_mobile']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $imagem_mobile = 'mobile_' . uniqid() . '.' . $ext;
                        move_uploaded_file($_FILES['imagem_mobile']['tmp_name'], UPLOAD_PATH . 'carrossel/' . $imagem_mobile);
                    }
                }
                
                if ($imagem_desktop && $imagem_mobile) {
                    $stmt = $pdo->prepare("INSERT INTO carrossel (titulo, subtitulo, imagem_desktop, imagem_mobile, link) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$titulo, $subtitulo, $imagem_desktop, $imagem_mobile, $link])) {
                        $message = 'Slide criado com sucesso!';
                        $action = 'list';
                    } else {
                        $error = 'Erro ao criar slide.';
                    }
                } else {
                    $error = 'É necessário enviar as duas imagens (desktop e mobile).';
                }
            } else {
                $error = 'Título é obrigatório.';
            }
            break;
            
        case 'update':
            $titulo = trim($_POST['titulo']);
            $subtitulo = trim($_POST['subtitulo']);
            $link = trim($_POST['link']);
            
            if ($titulo && $id) {
                $updates = ['titulo = ?', 'subtitulo = ?', 'link = ?', 'updated_at = NOW()'];
                $params = [$titulo, $subtitulo, $link];
                
                // Buscar slide atual
                $slide = $pdo->prepare("SELECT * FROM carrossel WHERE id = ?");
                $slide->execute([$id]);
                $slide = $slide->fetch();
                
                // Upload nova imagem desktop
                if (isset($_FILES['imagem_desktop']) && $_FILES['imagem_desktop']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['imagem_desktop']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $imagem_desktop = 'desktop_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($_FILES['imagem_desktop']['tmp_name'], UPLOAD_PATH . 'carrossel/' . $imagem_desktop)) {
                            // Deletar imagem antiga
                            if ($slide['imagem_desktop']) {
                                @unlink(UPLOAD_PATH . 'carrossel/' . $slide['imagem_desktop']);
                            }
                            $updates[] = 'imagem_desktop = ?';
                            $params[] = $imagem_desktop;
                        }
                    }
                }
                
                // Upload nova imagem mobile
                if (isset($_FILES['imagem_mobile']) && $_FILES['imagem_mobile']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['imagem_mobile']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $imagem_mobile = 'mobile_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($_FILES['imagem_mobile']['tmp_name'], UPLOAD_PATH . 'carrossel/' . $imagem_mobile)) {
                            // Deletar imagem antiga
                            if ($slide['imagem_mobile']) {
                                @unlink(UPLOAD_PATH . 'carrossel/' . $slide['imagem_mobile']);
                            }
                            $updates[] = 'imagem_mobile = ?';
                            $params[] = $imagem_mobile;
                        }
                    }
                }
                
                $params[] = $id;
                $sql = "UPDATE carrossel SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute($params)) {
                    $message = 'Slide atualizado com sucesso!';
                    $action = 'list';
                } else {
                    $error = 'Erro ao atualizar slide.';
                }
            } else {
                $error = 'Título é obrigatório.';
            }
            break;
            
        case 'delete':
            if ($id) {
                // Buscar imagens para deletar
                $slide = $pdo->prepare("SELECT * FROM carrossel WHERE id = ?");
                $slide->execute([$id]);
                $slide = $slide->fetch();
                
                if ($slide) {
                    @unlink(UPLOAD_PATH . 'carrossel/' . $slide['imagem_desktop']);
                    @unlink(UPLOAD_PATH . 'carrossel/' . $slide['imagem_mobile']);
                    
                    $stmt = $pdo->prepare("DELETE FROM carrossel WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $message = 'Slide deletado com sucesso!';
                    } else {
                        $error = 'Erro ao deletar slide.';
                    }
                }
            }
            $action = 'list';
            break;
            
        case 'update_ordem':
            $slides = $_POST['slides'] ?? [];
            foreach ($slides as $ordem => $slide_id) {
                $stmt = $pdo->prepare("UPDATE carrossel SET ordem = ? WHERE id = ?");
                $stmt->execute([$ordem + 1, $slide_id]);
            }
            $message = 'Ordem atualizada com sucesso!';
            break;
    }
}

// Buscar dados
switch ($action) {
    case 'edit':
        if ($id) {
            $slide = $pdo->prepare("SELECT * FROM carrossel WHERE id = ?");
            $slide->execute([$id]);
            $slide = $slide->fetch();
        }
        break;
        
    case 'list':
    default:
        $slides = $pdo->query("SELECT * FROM carrossel WHERE ativo = 1 ORDER BY ordem, created_at")->fetchAll();
        break;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Carrossel - El Terreno Admin</title>
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
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
        }
        
        .slide-preview {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
        }
        
        .slide-preview img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .slide-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 1rem;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-area:hover,
        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(109, 93, 178, 0.05);
        }
        
        .sortable {
            cursor: move;
        }
        
        .sortable:hover {
            background: #f8f9fa;
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
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="albums.php">
                        <i class="fas fa-images me-2"></i>Gerenciar Álbuns
                    </a>
                    <a class="nav-link active" href="carrossel.php">
                        <i class="fas fa-sliders-h me-2"></i>Gerenciar Carrossel
                    </a>
                    <a class="nav-link" href="cardapio.php">
                        <i class="fas fa-utensils me-2"></i>Gerenciar Cardápio
                    </a>
                    <hr class="my-3 opacity-50">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Site
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
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($action === 'list'): ?>
                    <!-- Lista de Slides -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-sliders-h me-2"></i>Gerenciar Carrossel</h1>
                        <a href="?action=create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Criar Slide
                        </a>
                    </div>
                    
                    <?php if (empty($slides)): ?>
                        <div class="content-card p-5 text-center">
                            <i class="fas fa-sliders-h fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum slide encontrado</h5>
                            <p class="text-muted">Crie o primeiro slide do carrossel.</p>
                            <a href="?action=create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Criar Primeiro Slide
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="content-card">
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Slides do Carrossel</h5>
                                    <small class="text-muted">Arraste para reordenar</small>
                                </div>
                            </div>
                            
                            <div id="sortable-slides">
                                <?php foreach ($slides as $slide): ?>
                                    <div class="p-3 border-bottom sortable" data-id="<?= $slide['id'] ?>">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <div class="slide-preview">
                                                    <img src="<?= UPLOAD_URL ?>carrossel/<?= $slide['imagem_desktop'] ?>" 
                                                         alt="<?= sanitize($slide['titulo']) ?>">
                                                    <div class="slide-overlay">
                                                        <small>Desktop</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="slide-preview">
                                                    <img src="<?= UPLOAD_URL ?>carrossel/<?= $slide['imagem_mobile'] ?>" 
                                                         alt="<?= sanitize($slide['titulo']) ?>">
                                                    <div class="slide-overlay">
                                                        <small>Mobile</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="mb-1"><?= sanitize($slide['titulo']) ?></h6>
                                                <?php if ($slide['subtitulo']): ?>
                                                    <p class="text-muted mb-1"><?= sanitize($slide['subtitulo']) ?></p>
                                                <?php endif; ?>
                                                <?php if ($slide['link']): ?>
                                                    <small class="text-primary">
                                                        <i class="fas fa-link me-1"></i>
                                                        <?= sanitize($slide['link']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?action=edit&id=<?= $slide['id'] ?>" 
                                                       class="btn btn-outline-secondary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="confirmDelete(<?= $slide['id'] ?>, '<?= sanitize($slide['titulo']) ?>')" 
                                                            title="Deletar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <span class="btn btn-outline-secondary" title="Arrastar">
                                                        <i class="fas fa-grip-vertical"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php elseif ($action === 'create'): ?>
                    <!-- Criar Slide -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-plus me-2"></i>Criar Slide</h1>
                        <a href="?action=list" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    
                    <div class="content-card p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="create">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Título *</label>
                                        <input type="text" name="titulo" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subtítulo</label>
                                        <input type="text" name="subtitulo" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Link (opcional)</label>
                                <input type="url" name="link" class="form-control" 
                                       placeholder="https://exemplo.com">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Imagem Desktop *</label>
                                        <div class="upload-area" onclick="document.getElementById('imagem_desktop').click()">
                                            <i class="fas fa-desktop fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Clique para selecionar</p>
                                            <small class="text-muted">Recomendado: 1920x600px</small>
                                        </div>
                                        <input type="file" id="imagem_desktop" name="imagem_desktop" 
                                               accept="image/*" style="display: none;" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Imagem Mobile *</label>
                                        <div class="upload-area" onclick="document.getElementById('imagem_mobile').click()">
                                            <i class="fas fa-mobile-alt fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Clique para selecionar</p>
                                            <small class="text-muted">Recomendado: 768x600px</small>
                                        </div>
                                        <input type="file" id="imagem_mobile" name="imagem_mobile" 
                                               accept="image/*" style="display: none;" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Criar Slide
                                </button>
                                <a href="?action=list" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($action === 'edit' && $slide): ?>
                    <!-- Editar Slide -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-edit me-2"></i>Editar Slide</h1>
                        <a href="?action=list" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    
                    <div class="content-card p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Título *</label>
                                        <input type="text" name="titulo" class="form-control" 
                                               value="<?= sanitize($slide['titulo']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Subtítulo</label>
                                        <input type="text" name="subtitulo" class="form-control" 
                                               value="<?= sanitize($slide['subtitulo']) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Link (opcional)</label>
                                <input type="url" name="link" class="form-control" 
                                       value="<?= sanitize($slide['link']) ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Imagem Desktop</label>
                                        <div class="mb-2">
                                            <img src="<?= UPLOAD_URL ?>carrossel/<?= $slide['imagem_desktop'] ?>" 
                                                 class="img-fluid rounded" style="max-height: 150px;">
                                        </div>
                                        <div class="upload-area" onclick="document.getElementById('imagem_desktop').click()">
                                            <i class="fas fa-desktop fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Clique para alterar</p>
                                            <small class="text-muted">Deixe em branco para manter atual</small>
                                        </div>
                                        <input type="file" id="imagem_desktop" name="imagem_desktop" 
                                               accept="image/*" style="display: none;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Imagem Mobile</label>
                                        <div class="mb-2">
                                            <img src="<?= UPLOAD_URL ?>carrossel/<?= $slide['imagem_mobile'] ?>" 
                                                 class="img-fluid rounded" style="max-height: 150px;">
                                        </div>
                                        <div class="upload-area" onclick="document.getElementById('imagem_mobile').click()">
                                            <i class="fas fa-mobile-alt fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Clique para alterar</p>
                                            <small class="text-muted">Deixe em branco para manter atual</small>
                                        </div>
                                        <input type="file" id="imagem_mobile" name="imagem_mobile" 
                                               accept="image/*" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                                <a href="?action=list" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja deletar o slide <strong id="slideName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta ação não pode ser desfeita. As imagens serão removidas.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Deletar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('slideName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        // Inicializar sortable
        const sortableElement = document.getElementById('sortable-slides');
        if (sortableElement) {
            new Sortable(sortableElement, {
                handle: '.fa-grip-vertical',
                animation: 150,
                onEnd: function(evt) {
                    const slides = Array.from(sortableElement.children).map(item => item.dataset.id);
                    
                    const formData = new FormData();
                    formData.append('action', 'update_ordem');
                    slides.forEach((id, index) => {
                        formData.append(`slides[${index}]`, id);
                    });
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (response.ok) {
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
                            alert.style.top = '20px';
                            alert.style.right = '20px';
                            alert.style.zIndex = '9999';
                            alert.innerHTML = `
                                <i class="fas fa-check me-2"></i>Ordem atualizada!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(alert);
                            
                            setTimeout(() => alert.remove(), 3000);
                        }
                    });
                }
            });
        }
        
        // Preview de imagens
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        document.getElementById('imagem_desktop')?.addEventListener('change', function() {
            previewImage(this, 'preview_desktop');
        });
        
        document.getElementById('imagem_mobile')?.addEventListener('change', function() {
            previewImage(this, 'preview_mobile');
        });
    </script>
</body>
</html>