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
            $nome = trim($_POST['nome']);
            $descricao = trim($_POST['descricao']);
            $data_evento = $_POST['data_evento'];
            $edicao = trim($_POST['edicao']);
            
            if ($nome && $data_evento && $edicao) {
                $stmt = $pdo->prepare("INSERT INTO albums (nome, descricao, data_evento, edicao) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$nome, $descricao, $data_evento, $edicao])) {
                    $message = 'Álbum criado com sucesso!';
                    $action = 'list';
                } else {
                    $error = 'Erro ao criar álbum.';
                }
            } else {
                $error = 'Preencha todos os campos obrigatórios.';
            }
            break;
            
        case 'update':
            $nome = trim($_POST['nome']);
            $descricao = trim($_POST['descricao']);
            $data_evento = $_POST['data_evento'];
            $edicao = trim($_POST['edicao']);
            
            if ($nome && $data_evento && $edicao && $id) {
                $stmt = $pdo->prepare("UPDATE albums SET nome = ?, descricao = ?, data_evento = ?, edicao = ?, updated_at = NOW() WHERE id = ?");
                if ($stmt->execute([$nome, $descricao, $data_evento, $edicao, $id])) {
                    $message = 'Álbum atualizado com sucesso!';
                    $action = 'list';
                } else {
                    $error = 'Erro ao atualizar álbum.';
                }
            } else {
                $error = 'Preencha todos os campos obrigatórios.';
            }
            break;
            
        case 'delete':
            if ($id) {
                // Deletar fotos físicas
                $fotos = $pdo->prepare("SELECT nome_arquivo FROM fotos WHERE album_id = ?");
                $fotos->execute([$id]);
                while ($foto = $fotos->fetch()) {
                    @unlink(UPLOAD_PATH . 'albums/' . $foto['nome_arquivo']);
                    @unlink(UPLOAD_PATH . 'thumbs/' . $foto['nome_arquivo']);
                }
                
                // Deletar álbum (CASCADE deleta as fotos)
                $stmt = $pdo->prepare("DELETE FROM albums WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $message = 'Álbum deletado com sucesso!';
                } else {
                    $error = 'Erro ao deletar álbum.';
                }
            }
            $action = 'list';
            break;
            
        case 'upload':
            if ($id && isset($_FILES['fotos'])) {
                $uploaded = 0;
                $total = count($_FILES['fotos']['name']);
                
                for ($i = 0; $i < $total; $i++) {
                    if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['fotos']['tmp_name'][$i];
                        $file_name = $_FILES['fotos']['name'][$i];
                        $file_size = $_FILES['fotos']['size'][$i];
                        
                        // Validar arquivo
                        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        if (in_array($ext, $allowed) && $file_size <= MAX_FILE_SIZE) {
                            $new_name = uniqid() . '.' . $ext;
                            $upload_path = UPLOAD_PATH . 'albums/' . $new_name;
                            $thumb_path = UPLOAD_PATH . 'thumbs/' . $new_name;
                            
                            if (move_uploaded_file($file_tmp, $upload_path)) {
                                // Criar thumbnail
                                createThumbnail($upload_path, $thumb_path);
                                
                                // Salvar no banco
                                $stmt = $pdo->prepare("INSERT INTO fotos (album_id, nome_arquivo, nome_original, tamanho) VALUES (?, ?, ?, ?)");
                                if ($stmt->execute([$id, $new_name, $file_name, $file_size])) {
                                    $uploaded++;
                                    
                                    // Definir como capa se for a primeira foto
                                    $album = $pdo->prepare("SELECT foto_capa FROM albums WHERE id = ?");
                                    $album->execute([$id]);
                                    $album_data = $album->fetch();
                                    
                                    if (!$album_data['foto_capa']) {
                                        $pdo->prepare("UPDATE albums SET foto_capa = ? WHERE id = ?")
                                            ->execute([$new_name, $id]);
                                    }
                                }
                            }
                        }
                    }
                }
                
                $message = "$uploaded de $total fotos enviadas com sucesso!";
            }
            break;
            
        case 'set_cover':
            $foto_id = intval($_POST['foto_id']);
            if ($id && $foto_id) {
                $foto = $pdo->prepare("SELECT nome_arquivo FROM fotos WHERE id = ? AND album_id = ?");
                $foto->execute([$foto_id, $id]);
                $foto_data = $foto->fetch();
                
                if ($foto_data) {
                    $stmt = $pdo->prepare("UPDATE albums SET foto_capa = ? WHERE id = ?");
                    if ($stmt->execute([$foto_data['nome_arquivo'], $id])) {
                        $message = 'Capa do álbum alterada com sucesso!';
                    }
                }
            }
            break;
    }
}

// Buscar dados conforme a ação
switch ($action) {
    case 'edit':
    case 'photos':
        if ($id) {
            $album = $pdo->prepare("SELECT * FROM albums WHERE id = ?");
            $album->execute([$id]);
            $album = $album->fetch();
            
            if ($action === 'photos') {
                $fotos = $pdo->prepare("SELECT * FROM fotos WHERE album_id = ? AND ativo = 1 ORDER BY ordem, created_at");
                $fotos->execute([$id]);
                $fotos = $fotos->fetchAll();
            }
        }
        break;
        
    case 'list':
    default:
        $albums = $pdo->query("
            SELECT a.*, COUNT(f.id) as total_fotos 
            FROM albums a 
            LEFT JOIN fotos f ON a.id = f.album_id AND f.ativo = 1
            WHERE a.ativo = 1 
            GROUP BY a.id 
            ORDER BY a.created_at DESC
        ")->fetchAll();
        break;
}

// Função para criar thumbnail
function createThumbnail($source, $destination, $width = 300, $height = 300) {
    $info = getimagesize($source);
    if (!$info) return false;
    
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    if (!$image) return false;
    
    $original_width = imagesx($image);
    $original_height = imagesy($image);
    
    // Calcular proporções
    $ratio = min($width / $original_width, $height / $original_height);
    $new_width = $original_width * $ratio;
    $new_height = $original_height * $ratio;
    
    // Criar thumbnail
    $thumbnail = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($thumbnail, 255, 255, 255);
    imagefill($thumbnail, 0, 0, $white);
    
    $x = ($width - $new_width) / 2;
    $y = ($height - $new_height) / 2;
    
    imagecopyresampled(
        $thumbnail, $image,
        $x, $y, 0, 0,
        $new_width, $new_height,
        $original_width, $original_height
    );
    
    $result = imagejpeg($thumbnail, $destination, 85);
    
    imagedestroy($image);
    imagedestroy($thumbnail);
    
    return $result;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Álbuns - El Terreno Admin</title>
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
        
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .photo-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .photo-item:hover {
            transform: scale(1.05);
        }
        
        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .photo-item:hover .photo-overlay {
            opacity: 1;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-area:hover,
        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(109, 93, 178, 0.05);
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
                    <a class="nav-link active" href="albums.php">
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
                    <!-- Lista de Álbuns -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-images me-2"></i>Gerenciar Álbuns</h1>
                        <a href="?action=create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Criar Álbum
                        </a>
                    </div>
                    
                    <div class="content-card">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Capa</th>
                                        <th>Nome</th>
                                        <th>Data do Evento</th>
                                        <th>Edição</th>
                                        <th>Fotos</th>
                                        <th>Criado em</th>
                                        <th width="200">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($albums as $album): ?>
                                        <tr>
                                            <td>
                                                <?php if ($album['foto_capa']): ?>
                                                    <img src="<?= UPLOAD_URL ?>thumbs/<?= $album['foto_capa'] ?>" 
                                                         class="rounded" width="50" height="50" style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= sanitize($album['nome']) ?></strong>
                                                <?php if ($album['descricao']): ?>
                                                    <br><small class="text-muted"><?= sanitize($album['descricao']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= formatDate($album['data_evento']) ?></td>
                                            <td><span class="badge bg-secondary"><?= sanitize($album['edicao']) ?></span></td>
                                            <td><span class="badge bg-info"><?= $album['total_fotos'] ?></span></td>
                                            <td><?= formatDate($album['created_at']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?action=photos&id=<?= $album['id'] ?>" 
                                                       class="btn btn-outline-primary" title="Gerenciar Fotos">
                                                        <i class="fas fa-images"></i>
                                                    </a>
                                                    <a href="?action=edit&id=<?= $album['id'] ?>" 
                                                       class="btn btn-outline-secondary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="confirmDelete(<?= $album['id'] ?>, '<?= sanitize($album['nome']) ?>')" 
                                                            title="Deletar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                <?php elseif ($action === 'create'): ?>
                    <!-- Criar Álbum -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-plus me-2"></i>Criar Álbum</h1>
                        <a href="?action=list" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    
                    <div class="content-card p-4">
                        <form method="POST">
                            <input type="hidden" name="action" value="create">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nome do Álbum *</label>
                                        <input type="text" name="nome" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Data do Evento *</label>
                                        <input type="date" name="data_evento" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Edição *</label>
                                        <input type="text" name="edicao" class="form-control" 
                                               placeholder="Ex: 2024, 1ª Edição" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Criar Álbum
                                </button>
                                <a href="?action=list" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($action === 'edit' && $album): ?>
                    <!-- Editar Álbum -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-edit me-2"></i>Editar Álbum</h1>
                        <a href="?action=list" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    
                    <div class="content-card p-4">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nome do Álbum *</label>
                                        <input type="text" name="nome" class="form-control" 
                                               value="<?= sanitize($album['nome']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Data do Evento *</label>
                                        <input type="date" name="data_evento" class="form-control" 
                                               value="<?= $album['data_evento'] ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Edição *</label>
                                        <input type="text" name="edicao" class="form-control" 
                                               value="<?= sanitize($album['edicao']) ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"><?= sanitize($album['descricao']) ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Salvar Alterações
                                </button>
                                <a href="?action=photos&id=<?= $album['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-images me-2"></i>Gerenciar Fotos
                                </a>
                                <a href="?action=list" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($action === 'photos' && $album): ?>
                    <!-- Gerenciar Fotos -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-images me-2"></i><?= sanitize($album['nome']) ?></h1>
                        <div>
                            <a href="?action=edit&id=<?= $album['id'] ?>" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-edit me-2"></i>Editar Álbum
                            </a>
                            <a href="?action=list" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                        </div>
                    </div>
                    
                    <!-- Upload de Fotos -->
                    <div class="content-card p-4 mb-4">
                        <h5><i class="fas fa-upload me-2"></i>Upload de Fotos</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload">
                            
                            <div class="upload-area" onclick="document.getElementById('fotos').click()">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Clique aqui ou arraste as fotos</h5>
                                <p class="text-muted mb-0">
                                    Formatos: JPG, PNG, GIF | Máximo: <?= number_format(MAX_FILE_SIZE/1024/1024, 1) ?>MB por foto
                                </p>
                            </div>
                            
                            <input type="file" id="fotos" name="fotos[]" multiple accept="image/*" 
                                   style="display: none;" onchange="this.form.submit()">
                        </form>
                    </div>
                    
                    <!-- Grid de Fotos -->
                    <?php if (!empty($fotos)): ?>
                        <div class="content-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5><i class="fas fa-images me-2"></i>Fotos do Álbum (<?= count($fotos) ?>)</h5>
                                <small class="text-muted">Clique em uma foto para defini-la como capa</small>
                            </div>
                            
                            <div class="photo-grid">
                                <?php foreach ($fotos as $foto): ?>
                                    <div class="photo-item" onclick="setCover(<?= $foto['id'] ?>)">
                                        <img src="<?= UPLOAD_URL ?>thumbs/<?= $foto['nome_arquivo'] ?>" 
                                             alt="<?= sanitize($foto['nome_original']) ?>">
                                        <div class="photo-overlay">
                                            <?php if ($album['foto_capa'] === $foto['nome_arquivo']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-star me-1"></i>Capa
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-star me-1"></i>Definir como Capa
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="content-card p-4 text-center">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma foto encontrada</h5>
                            <p class="text-muted">Faça upload das primeiras fotos deste álbum.</p>
                        </div>
                    <?php endif; ?>
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
                    <p>Tem certeza que deseja deletar o álbum <strong id="albumName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta ação não pode ser desfeita. Todas as fotos serão removidas.
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

    <!-- Form para definir capa -->
    <form id="setCoverForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="set_cover">
        <input type="hidden" name="foto_id" id="coverFotoId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('albumName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        function setCover(fotoId) {
            document.getElementById('coverFotoId').value = fotoId;
            document.getElementById('setCoverForm').submit();
        }
        
        // Drag and drop
        const uploadArea = document.querySelector('.upload-area');
        if (uploadArea) {
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                document.getElementById('fotos').files = e.dataTransfer.files;
                document.getElementById('fotos').form.submit();
            });
        }
    </script>
</body>
</html>