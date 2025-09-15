<?php
require_once 'includes/config.php';

// Script de instalação do banco de dados
$success_messages = [];
$error_messages = [];

if ($_POST['action'] ?? '' === 'install') {
    try {
        // Conectar sem especificar database para criar se não existir
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Criar database se não existir
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);
        
        // Criar tabela albums
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS albums (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                data_evento DATE NOT NULL,
                edicao VARCHAR(100) NOT NULL,
                foto_capa VARCHAR(255) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_created_at (created_at),
                INDEX idx_data_evento (data_evento)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Criar tabela fotos
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS fotos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                album_id INT NOT NULL,
                nome_arquivo VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE,
                INDEX idx_album_id (album_id),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Criar tabela admin_users
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        $success_messages[] = "Banco de dados criado com sucesso!";
        
        // Criar usuário admin padrão
        $admin_username = $_POST['admin_username'] ?? 'admin';
        $admin_password = $_POST['admin_password'] ?? 'admin123';
        
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        
        try {
            $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)")
                ->execute([$admin_username, $hashed_password]);
            $success_messages[] = "Usuário admin criado: $admin_username";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $success_messages[] = "Usuário admin já existe: $admin_username";
            } else {
                throw $e;
            }
        }
        
        // Inserir dados de exemplo
        try {
            // Álbum de exemplo 1
            $album1_id = $pdo->prepare("INSERT INTO albums (nome, data_evento, edicao) VALUES (?, ?, ?)")
                ->execute(['Casamento João & Maria', '2024-01-15', '2024']);
            $album1_id = $pdo->lastInsertId();
            
            // Álbum de exemplo 2
            $album2_id = $pdo->prepare("INSERT INTO albums (nome, data_evento, edicao) VALUES (?, ?, ?)")
                ->execute(['Festa de Aniversário', '2024-02-20', '2024']);
            $album2_id = $pdo->lastInsertId();
            
            // Álbum de exemplo 3
            $album3_id = $pdo->prepare("INSERT INTO albums (nome, data_evento, edicao) VALUES (?, ?, ?)")
                ->execute(['Evento Corporativo', '2024-03-10', '2024']);
            $album3_id = $pdo->lastInsertId();
            
            $success_messages[] = "Álbuns de exemplo criados!";
            
        } catch (PDOException $e) {
            $error_messages[] = "Erro ao criar dados de exemplo: " . $e->getMessage();
        }
        
        // Criar diretórios de upload
        $upload_dirs = [
            UPLOAD_PATH . 'albums/',
            THUMB_PATH
        ];
        
        foreach ($upload_dirs as $dir) {
            if (!file_exists($dir)) {
                if (mkdir($dir, 0755, true)) {
                    $success_messages[] = "Diretório criado: $dir";
                } else {
                    $error_messages[] = "Erro ao criar diretório: $dir";
                }
            }
        }
        
    } catch (PDOException $e) {
        $error_messages[] = "Erro: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Galeria de Fotos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">🖼️ Instalação da Galeria</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_messages)): ?>
                            <div class="alert alert-success">
                                <?php foreach ($success_messages as $msg): ?>
                                    <div>✅ <?= $msg ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_messages)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($error_messages as $msg): ?>
                                    <div>❌ <?= $msg ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($success_messages)): ?>
                            <form method="POST">
                                <input type="hidden" name="action" value="install">
                                
                                <div class="mb-3">
                                    <label class="form-label">Usuário Admin:</label>
                                    <input type="text" name="admin_username" class="form-control" value="admin" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Senha Admin:</label>
                                    <input type="password" name="admin_password" class="form-control" value="admin123" required>
                                    <small class="text-muted">Altere após a instalação!</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    🚀 Instalar Sistema
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <h5 class="text-success mb-3">✅ Instalação Concluída!</h5>
                                <a href="<?= BASE_URL ?>" class="btn btn-success me-2">Ver Galeria</a>
                                <a href="<?= BASE_URL ?>gallerydev/admin/" class="btn btn-primary">Painel Admin</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Configurações do Sistema</h5>
                    </div>
                    <div class="card-body">
                        <small>
                            <strong>Banco:</strong> <?= DB_HOST ?>/<?= DB_NAME ?><br>
                            <strong>Upload Path:</strong> <?= UPLOAD_PATH ?><br>
                            <strong>Base URL:</strong> <?= BASE_URL ?><br>
                            <strong>Ambiente:</strong> <?= $is_local ? 'Local' : 'Produção' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>