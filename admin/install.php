<?php
require_once 'config.php';

$messages = [];

if ($_POST['action'] ?? '' === 'install') {
    try {
        // Criar tabelas
        
        // Tabela de álbuns
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS albums (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                descricao TEXT,
                data_evento DATE NOT NULL,
                edicao VARCHAR(100) NOT NULL,
                foto_capa VARCHAR(255),
                ordem INT DEFAULT 0,
                ativo TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_ativo (ativo),
                INDEX idx_ordem (ordem),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Tabela de fotos
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS fotos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                album_id INT NOT NULL,
                nome_arquivo VARCHAR(255) NOT NULL,
                nome_original VARCHAR(255) NOT NULL,
                tamanho INT NOT NULL,
                ordem INT DEFAULT 0,
                ativo TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE,
                INDEX idx_album (album_id),
                INDEX idx_ativo (ativo),
                INDEX idx_ordem (ordem)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Tabela do carrossel
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS carrossel (
                id INT AUTO_INCREMENT PRIMARY KEY,
                titulo VARCHAR(255) NOT NULL,
                subtitulo VARCHAR(255),
                imagem_desktop VARCHAR(255) NOT NULL,
                imagem_mobile VARCHAR(255) NOT NULL,
                link VARCHAR(255),
                ordem INT DEFAULT 0,
                ativo TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_ativo (ativo),
                INDEX idx_ordem (ordem)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Tabela de categorias do cardápio
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cardapio_categorias (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                ordem INT DEFAULT 0,
                ativo TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_ativo (ativo),
                INDEX idx_ordem (ordem)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Tabela de itens do cardápio
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS cardapio_itens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                categoria_id INT NOT NULL,
                nome VARCHAR(255) NOT NULL,
                preco DECIMAL(10,2) NOT NULL,
                descricao TEXT,
                ordem INT DEFAULT 0,
                ativo TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (categoria_id) REFERENCES cardapio_categorias(id) ON DELETE CASCADE,
                INDEX idx_categoria (categoria_id),
                INDEX idx_ativo (ativo),
                INDEX idx_ordem (ordem)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        $messages[] = "✅ Tabelas criadas com sucesso!";
        
        // Inserir categorias padrão
        $categorias = [
            'Porções e Petiscos',
            'Burgers',
            'Bebidas Não Alcoólicas',
            'Bebidas Alcoólicas / Cervejas / Chopp',
            'Combos',
            'Doses',
            'Drinks',
            'Drinks sem Álcool',
            'Cigarros',
            'Balas e Chicletes'
        ];
        
        foreach ($categorias as $index => $categoria) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO cardapio_categorias (nome, ordem) VALUES (?, ?)");
            $stmt->execute([$categoria, $index + 1]);
        }
        
        $messages[] = "✅ Categorias do cardápio criadas!";
        
        // Inserir itens do cardápio
        $cardapio_inicial = [
            'Porções e Petiscos' => [
                ['Batata Frita com Queijo e Bacon', 35.00],
                ['Pastel 6 unidades (Carne e Queijo)', 35.00],
                ['Isca de Frango Empanada (acompanha maionese)', 45.00],
                ['Bolinho (Costela ou Carne Seca) 10 unidades', 45.00],
                ['Coxinha sem Massa - 8 unidades', 40.00],
                ['Trio Mineiro na pedra (Torresmo, mandioca e linguiça)', 60.00],
                ['Tábua de Frios (Queijo parmesão, mussarela, ovo de codorna, salaminho, azeitona)', 60.00],
                ['Isca de Tilápia Empanada (acompanha maionese)', 65.00],
                ['Filé com Fritas ou Mandioca', 89.00],
                ['Picanha com Fritas ou Mandioca', 99.00]
            ],
            'Burgers' => [
                ['Hambúrguer', 20.00],
                ['X Bacon', 25.00],
                ['X Egg', 22.00],
                ['X Frango', 25.00]
            ],
            'Bebidas Não Alcoólicas' => [
                ['Água Mineral 500ml', 5.00],
                ['Água Mineral com Gás 500ml', 6.00],
                ['Água Tônica lata', 6.00],
                ['Refrigerante Lata (Coca Cola, Coca Cola Zero, Guaraná Antártica, Guaraná Antártica Zero, Fanta Laranja, Sprite)', 6.00],
                ['Refrigerante 500ml (Coca Cola, Coca Cola Zero, Guaraná Antártica, Guaraná Antártica Zero)', 8.00],
                ['Refrigerante 1,5L (Coca Cola, Coca Cola Zero, Guaraná Antártica, Guaraná Antártica Zero)', 15.00],
                ['Limoneto H20 500ml', 8.00],
                ['Suco Del Valle Lata (Uva e Maracujá)', 8.00],
                ['Suco Natural Copo 400ml (Consultar sabores)', 12.00],
                ['Suco Natural Jarra 1L (Consultar sabores)', 25.00],
                ['Energético Red Bull (Tradicional, Melancia, Tropical)', 18.00]
            ],
            'Bebidas Alcoólicas / Cervejas / Chopp' => [
                ['Cerveja Brahma Lata 350ml', 8.00],
                ['Cerveja Heineken Lata 350ml', 10.00],
                ['COMBO: 10 Brahmas Latinha no Balde', 75.00],
                ['COMBO: 10 Heineken Latinha no Balde', 95.00],
                ['Cerveja Heineken Long Neck', 12.00],
                ['Cerveja Corona Long Neck', 12.00],
                ['Cerveja Stella Pure Gold', 12.00],
                ['COMBO: 10 Long Necks no balde (Heineken, Corona ou Stella)', 110.00],
                ['Chopp Laut 400ml', 12.00],
                ['Chopp Vinho', 16.00],
                ['Skol Beats (Senses, GT, Tropical, Red Mix)', 15.00]
            ],
            'Combos' => [
                ['Whisky Red Label + 4 Energéticos', 320.00],
                ['Whisky Buchanans + 4 Energéticos', 380.00],
                ['Whisky Black Label + 4 Energéticos', 380.00],
                ['Whisky Jack Daniels + 4 Energéticos', 400.00],
                ['Vodka Smirnoff + 4 Energéticos', 250.00],
                ['Vodka Absolut + 4 Energéticos', 320.00],
                ['Vodka Ciroc + 4 Energéticos', 400.00],
                ['Vodka Orloff + 4 Energéticos', 220.00],
                ['Gin Tanqueray + 4 Energéticos', 340.00],
                ['Garrafa Chandon', 200.00]
            ],
            'Doses' => [
                ['Cachaça de Sabor (Consultar sabores)', 15.00],
                ['Campari', 18.00],
                ['Tequila Jose Cuervo Ouro', 24.00],
                ['Tequila Tekpar', 15.00],
                ['Vodka Smirnoff', 15.00],
                ['Vodka Absolut', 25.00],
                ['Vodka Ciroc', 30.00],
                ['Gin Seagers', 20.00],
                ['Whisky Red Label', 20.00],
                ['Whisky Jack Daniels', 26.00],
                ['Whisky Black Label', 30.00],
                ['Licor 43', 23.00]
            ],
            'Drinks' => [
                ['Caipirinha', 20.00],
                ['Caipírissima c/ Absolut (Limão, Morango, Abacaxi)', 30.00],
                ['Gin com Fruta (Limão, Morango, Abacaxi)', 30.00],
                ['Gin Tropical', 30.00],
                ['Piña Colada', 25.00],
                ['Sex on the Beach', 25.00],
                ['Aperol Spritz', 25.00],
                ['Moscow Mule', 26.00],
                ['Mojito', 25.00],
                ['Negroni', 28.00],
                ['Cozumel', 26.00],
                ['Clericot', 28.00]
            ],
            'Drinks sem Álcool' => [
                ['Soda de Verão', 25.00],
                ['Sunset on the Beach', 25.00],
                ['Coquetel de Fruta', 25.00]
            ],
            'Cigarros' => [
                ['Cigarro Dunhil', 3.00],
                ['Cigarro de Palha', 3.00]
            ],
            'Balas e Chicletes' => [
                ['Halls', 3.00],
                ['Trident', 3.00],
                ['Pirulito', 1.00]
            ]
        ];
        
        foreach ($cardapio_inicial as $categoria_nome => $itens) {
            $stmt = $pdo->prepare("SELECT id FROM cardapio_categorias WHERE nome = ?");
            $stmt->execute([$categoria_nome]);
            $categoria = $stmt->fetch();
            
            if ($categoria) {
                foreach ($itens as $index => $item) {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO cardapio_itens (categoria_id, nome, preco, ordem) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$categoria['id'], $item[0], $item[1], $index + 1]);
                }
            }
        }
        
        $messages[] = "✅ Cardápio inicial criado!";
        
        // Criar diretórios de upload
        $dirs = [
            UPLOAD_PATH,
            UPLOAD_PATH . 'albums/',
            UPLOAD_PATH . 'thumbs/',
            UPLOAD_PATH . 'carrossel/'
        ];
        
        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
                $messages[] = "✅ Diretório criado: " . basename($dir);
            }
        }
        
        $messages[] = "🎉 Instalação concluída com sucesso!";
        
    } catch (Exception $e) {
        $messages[] = "❌ Erro: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - El Terreno Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6d5db2, #ff248e, #7f67db);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="install-card p-5">
        <div class="text-center mb-4">
            <h1 class="h3 mb-3">🚀 Instalação El Terreno</h1>
            <p class="text-muted">Configure o sistema administrativo</p>
        </div>
        
        <?php if (!empty($messages)): ?>
            <div class="alert alert-info">
                <?php foreach ($messages as $message): ?>
                    <div><?= $message ?></div>
                <?php endforeach; ?>
            </div>
            
            <?php if (strpos(end($messages), 'concluída') !== false): ?>
                <div class="text-center">
                    <a href="login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Acessar Painel Admin
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="action" value="install">
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Atenção</h6>
                    <p class="mb-0">Esta instalação irá criar as tabelas necessárias no banco de dados.</p>
                </div>
                
                <div class="mb-3">
                    <h6>Configurações:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Usuário Admin:</strong> plug</li>
                        <li><strong>Senha Admin:</strong> #Adidas777</li>
                        <li><strong>Banco:</strong> <?= DB_NAME ?></li>
                        <li><strong>Host:</strong> <?= DB_HOST ?></li>
                    </ul>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-download me-2"></i>Instalar Sistema
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>