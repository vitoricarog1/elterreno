<?php
// Buscar cardápio do banco de dados
try {
    $categorias = $pdo->query("
        SELECT c.*, COUNT(i.id) as total_itens
        FROM cardapio_categorias c
        LEFT JOIN cardapio_itens i ON c.id = i.categoria_id AND i.ativo = 1
        WHERE c.ativo = 1
        GROUP BY c.id
        HAVING total_itens > 0
        ORDER BY c.ordem
    ")->fetchAll();
    
    $cardapio = [];
    foreach ($categorias as $categoria) {
        $itens = $pdo->prepare("
            SELECT * FROM cardapio_itens 
            WHERE categoria_id = ? AND ativo = 1 
            ORDER BY ordem, nome
        ");
        $itens->execute([$categoria['id']]);
        $cardapio[$categoria['id']] = [
            'categoria' => $categoria,
            'itens' => $itens->fetchAll()
        ];
    }
} catch (Exception $e) {
    $cardapio = [];
}
?>

<?php foreach ($cardapio as $cat_id => $dados): ?>
    <section class="menu-section" id="categoria-<?= $cat_id ?>">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-utensils"></i> <?= strtoupper(sanitize($dados['categoria']['nome'])) ?></h2>
                <p><?= sanitize($dados['categoria']['nome']) ?></p>
            </div>
            
            <div class="menu-grid" id="categoria<?= $cat_id ?>Grid">
                <?php foreach ($dados['itens'] as $item): ?>
                    <div class="menu-item" data-id="<?= $item['id'] ?>">
                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-name"><?= sanitize($item['nome']) ?></h3>
                                <span class="menu-item-price">R$ <?= number_format($item['preco'], 2, ',', '.') ?></span>
                            </div>
                            <?php if ($item['descricao']): ?>
                                <p class="menu-item-description"><?= sanitize($item['descricao']) ?></p>
                            <?php endif; ?>
                            <div class="menu-item-footer">
                                <span class="menu-item-category"><?= sanitize($dados['categoria']['nome']) ?></span>
                                <button class="btn btn-whatsapp" onclick="pedirWhatsApp('<?= sanitize($item['nome']) ?>', <?= $item['preco'] ?>)">
                                    <i class="fab fa-whatsapp me-1"></i>Pedir
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<style>
.btn-whatsapp {
    background: #25d366;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.btn-whatsapp:hover {
    background: #128c7e;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
}

.menu-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}
</style>

<script>
function pedirWhatsApp(nome, preco) {
    const precoFormatado = 'R$ ' + preco.toFixed(2).replace('.', ',');
    const mensagem = `Olá! Gostaria de pedir:\n\n*${nome}*\nPreço: ${precoFormatado}\n\nObrigado!`;
    const numeroWhatsApp = '5532999999999'; // Substitua pelo número real
    const url = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(mensagem)}`;
    window.open(url, '_blank');
}
</script>