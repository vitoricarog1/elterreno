<?php
require_once 'config.php';
requireLogin();

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);
$categoria_id = intval($_GET['categoria_id'] ?? 0);
$message = '';
$error = '';

// Processar ações
if ($_POST) {
    switch ($_POST['action']) {
        case 'create_item':
            $categoria_id = intval($_POST['categoria_id']);
            $nome = trim($_POST['nome']);
            $preco = floatval($_POST['preco']);
            $descricao = trim($_POST['descricao']);
            
            if ($categoria_id && $nome && $preco > 0) {
                $stmt = $pdo->prepare("INSERT INTO cardapio_itens (categoria_id, nome, preco, descricao) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$categoria_id, $nome, $preco, $descricao])) {
                    $message = 'Item adicionado com sucesso!';
                    $action = 'list';
                } else {
                    $error = 'Erro ao adicionar item.';
                }
            } else {
                $error = 'Preencha todos os campos obrigatórios.';
            }
            break;
            
        case 'update_item':
            $categoria_id = intval($_POST['categoria_id']);
            $nome = trim($_POST['nome']);
            $preco = floatval($_POST['preco']);
            $descricao = trim($_POST['descricao']);
            
            if ($id && $categoria_id && $nome && $preco > 0) {
                $stmt = $pdo->prepare("UPDATE cardapio_itens SET categoria_id = ?, nome = ?, preco = ?, descricao = ?, updated_at = NOW() WHERE id = ?");
                if ($stmt->execute([$categoria_id, $nome, $preco, $descricao, $id])) {
                    $message = 'Item atualizado com sucesso!';
                    $action = 'list';
                } else {
                    $error = 'Erro ao atualizar item.';
                }
            } else {
                $error = 'Preencha todos os campos obrigatórios.';
            }
            break;
            
        case 'delete_item':
            if ($id) {
                $stmt = $pdo->prepare("UPDATE cardapio_itens SET ativo = 0 WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $message = 'Item removido com sucesso!';
                } else {
                    $error = 'Erro ao remover item.';
                }
            }
            $action = 'list';
            break;
            
        case 'update_ordem':
            $itens = $_POST['itens'] ?? [];
            foreach ($itens as $ordem => $item_id) {
                $stmt = $pdo->prepare("UPDATE cardapio_itens SET ordem = ? WHERE id = ?");
                $stmt->execute([$ordem + 1, $item_id]);
            }
            $message = 'Ordem atualizada com sucesso!';
            break;
    }
}

// Buscar dados
$categorias = $pdo->query("SELECT * FROM cardapio_categorias WHERE ativo = 1 ORDER BY ordem")->fetchAll();

switch ($action) {
    case 'edit_item':
        if ($id) {
            $item = $pdo->prepare("SELECT * FROM cardapio_itens WHERE id = ?");
            $item->execute([$id]);
            $item = $item->fetch();
        }
        break;
        
    case 'list':
    default:
        $cardapio = [];
        foreach ($categorias as $categoria) {
            $itens = $pdo->prepare("SELECT * FROM cardapio_itens WHERE categoria_id = ? AND ativo = 1 ORDER BY ordem, nome");
            $itens->execute([$categoria['id']]);
            $cardapio[$categoria['id']] = [
                'categoria' => $categoria,
                'itens' => $itens->fetchAll()
            ];
        }
        break;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cardápio - El Terreno Admin</title>
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
        
        .categoria-header {
            background: var(--gradient);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
        }
        
        .item-row {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f3f4;
            transition: background 0.3s ease;
        }
        
        .item-row:hover {
            background: #f8f9fa;
        }
        
        .item-row:last-child {
            border-bottom: none;
            border-radius: 0 0 12px 12px;
        }
        
        .preco {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .sortable {
            cursor: move;
        }
        
        .sortable:hover {
            background: #f0f0f0;
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
                    <a class="nav-link" href="carrossel.php">
                        <i class="fas fa-sliders-h me-2"></i>Gerenciar Carrossel
                    </a>
                    <a class="nav-link active" href="cardapio.php">
                        <i class="fas fa-utensils me-2"></i>Gerenciar Cardápio
                    </a>
                    <hr class="my-3 opacity-50">
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
                    <!-- Lista do Cardápio -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-utensils me-2"></i>Gerenciar Cardápio</h1>
                        <a href="?action=create_item" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Adicionar Item
                        </a>
                    </div>
                    
                    <?php foreach ($cardapio as $cat_id => $dados): ?>
                        <div class="content-card mb-4">
                            <div class="categoria-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-utensils me-2"></i>
                                        <?= sanitize($dados['categoria']['nome']) ?>
                                    </h5>
                                    <span class="badge bg-light text-dark">
                                        <?= count($dados['itens']) ?> itens
                                    </span>
                                </div>
                            </div>
                            
                            <?php if (empty($dados['itens'])): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-utensils fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Nenhum item nesta categoria</p>
                                    <a href="?action=create_item&categoria_id=<?= $cat_id ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>Adicionar Primeiro Item
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="sortable-container" data-categoria="<?= $cat_id ?>">
                                    <?php foreach ($dados['itens'] as $item): ?>
                                        <div class="item-row sortable" data-id="<?= $item['id'] ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1"><?= sanitize($item['nome']) ?></h6>
                                                            <?php if ($item['descricao']): ?>
                                                                <small class="text-muted"><?= sanitize($item['descricao']) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="preco">
                                                            R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ms-3">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?action=edit_item&id=<?= $item['id'] ?>" 
                                                           class="btn btn-outline-secondary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-outline-danger" 
                                                                onclick="confirmDelete(<?= $item['id'] ?>, '<?= sanitize($item['nome']) ?>')" 
                                                                title="Remover">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <span class="btn btn-outline-secondary" title="Arrastar para reordenar">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                <?php elseif ($action === 'create_item'): ?>
                    <!-- Criar Item -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-plus me-2"></i>Adicionar Item</h1>
                        <a href="?action=list" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    
                    <div class="content-card p-4">
                        <form method="POST">
                            <input type="hidden" name="action" value="create_item">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Categoria *</label>
                                        <select name="categoria_id" class="form-select" required>
                                            <option value="">Selecione uma categoria</option>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?= $categoria['id'] ?>" 
                                                        <?= $categoria['id'] == $categoria_id ? 'selected' : '' ?>>
                                                    <?= sanitize($categoria['nome']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Preço *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" name="preco" class="form-control" 
                                                   step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nome do Item *</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3" 
                                          placeholder="Descrição opcional do item"></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Adicionar Item
                                </button>
                                <a href="?action=list" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($action === 'edit_item' && $item): ?>
                    <!-- Editar Item -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-edit me-2"></i>Editar Item</h1>
                        <a href="?action=list" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                    
                    <div class="content-card p-4">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_item">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Categoria *</label>
                                        <select name="categoria_id" class="form-select" required>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?= $categoria['id'] ?>" 
                                                        <?= $categoria['id'] == $item['categoria_id'] ? 'selected' : '' ?>>
                                                    <?= sanitize($categoria['nome']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Preço *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">R$</span>
                                            <input type="number" name="preco" class="form-control" 
                                                   step="0.01" min="0" value="<?= $item['preco'] ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nome do Item *</label>
                                <input type="text" name="nome" class="form-control" 
                                       value="<?= sanitize($item['nome']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="3"><?= sanitize($item['descricao']) ?></textarea>
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
                    <h5 class="modal-title">Confirmar Remoção</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja remover o item <strong id="itemName"></strong>?</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        O item será ocultado do cardápio, mas não será deletado permanentemente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Remover</button>
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
            document.getElementById('itemName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        // Inicializar sortable para reordenação
        document.querySelectorAll('.sortable-container').forEach(container => {
            new Sortable(container, {
                handle: '.fa-grip-vertical',
                animation: 150,
                onEnd: function(evt) {
                    const categoriaId = container.dataset.categoria;
                    const itens = Array.from(container.children).map(item => item.dataset.id);
                    
                    // Enviar nova ordem via AJAX
                    const formData = new FormData();
                    formData.append('action', 'update_ordem');
                    itens.forEach((id, index) => {
                        formData.append(`itens[${index}]`, id);
                    });
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (response.ok) {
                            // Mostrar feedback visual
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
                            
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>