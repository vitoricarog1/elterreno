<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

$success_message = '';
$error_message = '';
$upload_results = [];

// Buscar álbuns para o select
$albums = getAlbums();

// Álbum pré-selecionado via URL
$selected_album = intval($_GET['album_id'] ?? 0);

// Processar upload
if ($_POST['action'] ?? '' === 'upload') {
    $album_id = intval($_POST['album_id'] ?? 0);
    
    if ($album_id <= 0) {
        $error_message = 'Selecione um álbum válido!';
    } elseif (empty($_FILES['photos']['name'][0])) {
        $error_message = 'Selecione pelo menos uma foto!';
    } else {
        $db = getDB();
        $album = getAlbumById($album_id);
        
        if (!$album) {
            $error_message = 'Álbum não encontrado!';
        } else {
            // Criar diretórios se não existirem
            if (!file_exists(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH, 0755, true);
            }
            if (!file_exists(THUMB_PATH)) {
                mkdir(THUMB_PATH, 0755, true);
            }
            
            $uploaded_count = 0;
            $total_files = count($_FILES['photos']['name']);
            
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['photos']['name'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                        'size' => $_FILES['photos']['size'][$i],
                        'type' => $_FILES['photos']['type'][$i]
                    ];
                    
                    if (isValidImage($file)) {
                        // Gerar nome único
                        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $filename = uniqid() . '_' . time() . '.' . $extension;
                        
                        $upload_path = UPLOAD_PATH . $filename;
                        $thumb_path = THUMB_PATH . $filename;
                        
                        // Redimensionar e salvar imagem principal
                        if (resizeImageIfNeeded($file['tmp_name'], $upload_path)) {
                            // Criar thumbnail
                            if (createThumbnail($upload_path, $thumb_path)) {
                                // Salvar no banco
                                if ($db->insert(
                                    "INSERT INTO fotos (album_id, nome_arquivo, created_at) VALUES (?, ?, NOW())",
                                    [$album_id, $filename]
                                )) {
                                    $uploaded_count++;
                                    
                                    // Definir como foto de capa se for a primeira
                                    if (!$album['foto_capa']) {
                                        $db->query(
                                            "UPDATE albums SET foto_capa = ? WHERE id = ?",
                                            [$filename, $album_id]
                                        );
                                    }
                                    
                                    $upload_results[] = [
                                        'status' => 'success',
                                        'filename' => $file['name'],
                                        'message' => 'Upload realizado com sucesso'
                                    ];
                                } else {
                                    $upload_results[] = [
                                        'status' => 'error',
                                        'filename' => $file['name'],
                                        'message' => 'Erro ao salvar no banco de dados'
                                    ];
                                }
                            } else {
                                $upload_results[] = [
                                    'status' => 'error',
                                    'filename' => $file['name'],
                                    'message' => 'Erro ao criar thumbnail'
                                ];
                            }
                        } else {
                            $upload_results[] = [
                                'status' => 'error',
                                'filename' => $file['name'],
                                'message' => 'Erro ao processar imagem'
                            ];
                        }
                    } else {
                        $upload_results[] = [
                            'status' => 'error',
                            'filename' => $file['name'],
                            'message' => 'Arquivo inválido ou muito grande'
                        ];
                    }
                } else {
                    $upload_results[] = [
                        'status' => 'error',
                        'filename' => $_FILES['photos']['name'][$i],
                        'message' => 'Erro no upload: ' . $_FILES['photos']['error'][$i]
                    ];
                }
            }
            
            if ($uploaded_count > 0) {
                $success_message = "$uploaded_count foto(s) enviada(s) com sucesso!";
            }
            
            if ($uploaded_count < $total_files) {
                $failed_count = $total_files - $uploaded_count;
                $error_message = "$failed_count arquivo(s) falharam no upload.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Fotos - Admin</title>
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
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        .upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            cursor: pointer;
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
                    <a class="nav-link" href="criar-album.php">
                        <i class="fas fa-plus me-2"></i>Criar Álbum
                    </a>
                    <a class="nav-link" href="gerenciar-albums.php">
                        <i class="fas fa-folder me-2"></i>Gerenciar Álbuns
                    </a>
                    <a class="nav-link active" href="upload-fotos.php">
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
                    <i class="fas fa-upload me-2"></i>Upload de Fotos
                </h2>
                
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
                
                <!-- Resultados detalhados do upload -->
                <?php if (!empty($upload_results)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Resultados do Upload</h6>
                        </div>
                        <div class="card-body">
                            <?php foreach ($upload_results as $result): ?>
                                <div class="alert alert-<?= $result['status'] === 'success' ? 'success' : 'danger' ?> py-2">
                                    <small>
                                        <i class="fas fa-<?= $result['status'] === 'success' ? 'check' : 'times' ?> me-2"></i>
                                        <strong><?= sanitize($result['filename']) ?>:</strong> 
                                        <?= $result['message'] ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                                    <input type="hidden" name="action" value="upload">
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-folder me-2"></i>Selecionar Álbum
                                        </label>
                                        <select name="album_id" class="form-select form-select-lg" required>
                                            <option value="">Escolha um álbum...</option>
                                            <?php foreach ($albums as $album): ?>
                                                <option value="<?= $album['id'] ?>" 
                                                        <?= $album['id'] == $selected_album ? 'selected' : '' ?>>
                                                    <?= sanitize($album['nome']) ?> 
                                                    (<?= $album['total_fotos'] ?> fotos)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-images me-2"></i>Selecionar Fotos
                                        </label>
                                        <div class="upload-area" onclick="document.getElementById('photoInput').click()">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Clique aqui ou arraste as fotos</h5>
                                            <p class="text-muted mb-0">
                                                Formatos aceitos: JPG, PNG, GIF<br>
                                                Tamanho máximo: <?= number_format(MAX_FILE_SIZE / 1024 / 1024, 1) ?>MB por foto
                                            </p>
                                        </div>
                                        <input type="file" 
                                               id="photoInput" 
                                               name="photos[]" 
                                               multiple 
                                               accept="image/*" 
                                               style="display: none;"
                                               onchange="previewImages(this)">
                                    </div>
                                    
                                    <!-- Preview das fotos selecionadas -->
                                    <div id="previewContainer" style="display: none;">
                                        <h6><i class="fas fa-eye me-2"></i>Fotos Selecionadas:</h6>
                                        <div id="previewGrid" class="preview-grid"></div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-success btn-lg" id="uploadBtn" disabled>
                                            <i class="fas fa-upload me-2"></i>
                                            <span id="uploadText">Enviar Fotos</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informações
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Upload múltiplo suportado
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Redimensionamento automático
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Thumbnails gerados automaticamente
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        Primeira foto vira capa do álbum
                                    </li>
                                </ul>
                                
                                <hr>
                                
                                <h6>Formatos Aceitos:</h6>
                                <div class="d-flex gap-2 mb-3">
                                    <span class="badge bg-primary">JPG</span>
                                    <span class="badge bg-primary">PNG</span>
                                    <span class="badge bg-primary">GIF</span>
                                </div>
                                
                                <h6>Tamanho Máximo:</h6>
                                <p class="text-muted mb-0">
                                    <?= number_format(MAX_FILE_SIZE / 1024 / 1024, 1) ?>MB por foto
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedFiles = [];
        
        // Preview das imagens selecionadas
        function previewImages(input) {
            const files = Array.from(input.files);
            selectedFiles = files;
            
            const previewContainer = document.getElementById('previewContainer');
            const previewGrid = document.getElementById('previewGrid');
            const uploadBtn = document.getElementById('uploadBtn');
            const uploadText = document.getElementById('uploadText');
            
            previewGrid.innerHTML = '';
            
            if (files.length > 0) {
                previewContainer.style.display = 'block';
                uploadBtn.disabled = false;
                uploadText.textContent = `Enviar ${files.length} foto(s)`;
                
                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-preview" onclick="removePreview(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        previewGrid.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.style.display = 'none';
                uploadBtn.disabled = true;
                uploadText.textContent = 'Enviar Fotos';
            }
        }
        
        // Remover foto do preview
        function removePreview(index) {
            selectedFiles.splice(index, 1);
            
            // Criar novo FileList
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            document.getElementById('photoInput').files = dt.files;
            
            previewImages(document.getElementById('photoInput'));
        }
        
        // Drag and drop
        const uploadArea = document.querySelector('.upload-area');
        
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
            
            const files = e.dataTransfer.files;
            document.getElementById('photoInput').files = files;
            previewImages(document.getElementById('photoInput'));
        });
        
        // Progress durante upload
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const uploadBtn = document.getElementById('uploadBtn');
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando...';
            uploadBtn.disabled = true;
        });
    </script>
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