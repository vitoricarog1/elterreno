/* ===== PUB SCRIPTS - EL TERRENO PUB ===== */

// === DADOS DOS PETISCOS ===
const petiscos = [
    {
        id: 1,
        nome: "Bolinho de Bacalhau",
        preco: "R$ 28,90",
        descricao: "Deliciosos bolinhos crocantes com bacalhau desfiado",
        imagem: "./images/petiscos/bolinho-bacalhau.jpg",
        categoria: "Fritos",
        ingredientes: "Bacalhau desfiado, batata, ovos, farinha de trigo, cebola, alho, salsa, pimenta-do-reino",
        peso: "300g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-fish"
    },
    {
        id: 2,
        nome: "Tábua de Frios",
        preco: "R$ 45,90",
        descricao: "Seleção especial de queijos e embutidos artesanais",
        imagem: "./images/petiscos/tabua-frios.jpg",
        categoria: "Frios",
        ingredientes: "Queijo brie, queijo gorgonzola, presunto parma, salame italiano, azeitonas, nozes, mel",
        peso: "400g",
        porcoes: "3-4 pessoas",
        icon: "fas fa-cheese"
    },
    {
        id: 3,
        nome: "Camarão Empanado",
        preco: "R$ 38,90",
        descricao: "Camarões grandes empanados com molho especial",
        imagem: "./images/petiscos/camarao-empanado.jpg",
        categoria: "Frutos do Mar",
        ingredientes: "Camarão grande, farinha panko, ovos, farinha de trigo, molho tártaro, limão",
        peso: "250g",
        porcoes: "2 pessoas",
        icon: "fas fa-shrimp"
    },
    {
        id: 4,
        nome: "Bruschetta Italiana",
        preco: "R$ 24,90",
        descricao: "Pão italiano tostado com tomate, manjericão e mozzarella",
        imagem: "./images/petiscos/bruschetta.jpg",
        categoria: "Vegetariano",
        ingredientes: "Pão italiano, tomate cereja, mozzarella de búfala, manjericão fresco, azeite extra virgem, alho",
        peso: "200g",
        porcoes: "1-2 pessoas",
        icon: "fas fa-bread-slice"
    },
    {
        id: 5,
        nome: "Asas de Frango BBQ",
        preco: "R$ 32,90",
        descricao: "Asas suculentas com molho barbecue artesanal",
        imagem: "./images/petiscos/asas-frango.jpg",
        categoria: "Grelhados",
        ingredientes: "Asas de frango, molho barbecue caseiro, mel, páprica, alho, cebola",
        peso: "350g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-drumstick-bite"
    },
    {
        id: 6,
        nome: "Nachos Supremos",
        preco: "R$ 29,90",
        descricao: "Tortillas crocantes com queijo derretido e guacamole",
        imagem: "./images/petiscos/nachos.jpg",
        categoria: "Mexicano",
        ingredientes: "Tortillas de milho, queijo cheddar, guacamole, molho salsa, creme azedo, jalapeños",
        peso: "300g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-pepper-hot"
    },
    {
        id: 7,
        nome: "Coxinha Gourmet",
        preco: "R$ 26,90",
        descricao: "Coxinhas artesanais com frango desfiado temperado",
        imagem: "./images/petiscos/coxinha.jpg",
        categoria: "Brasileira",
        ingredientes: "Frango desfiado, catupiry, massa de batata, farinha de rosca, temperos especiais",
        peso: "280g",
        porcoes: "2 pessoas",
        icon: "fas fa-egg"
    },
    {
        id: 8,
        nome: "Polvo à Lagareiro",
        preco: "R$ 52,90",
        descricao: "Polvo grelhado com batatas e azeite português",
        imagem: "./images/petiscos/polvo-lagareiro.jpg",
        categoria: "Frutos do Mar",
        ingredientes: "Polvo fresco, batatas, azeite português, alho, coentros, pimenta-do-reino",
        peso: "400g",
        porcoes: "3-4 pessoas",
        icon: "fas fa-fish"
    },
    {
        id: 9,
        nome: "Carpaccio de Carne",
        preco: "R$ 42,90",
        descricao: "Fatias finas de carne com rúcula e parmesão",
        imagem: "./images/petiscos/carpaccio.jpg",
        categoria: "Carne",
        ingredientes: "Filé mignon, rúcula, queijo parmesão, azeite trufado, alcaparras, limão",
        peso: "200g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-cut"
    },
    {
        id: 10,
        nome: "Dadinho de Tapioca",
        preco: "R$ 22,90",
        descricao: "Cubinhos crocantes de tapioca com mel e pimenta",
        imagem: "./images/petiscos/dadinho-tapioca.jpg",
        categoria: "Brasileira",
        ingredientes: "Tapioca, queijo coalho, mel, pimenta biquinho, azeite de dendê",
        peso: "250g",
        porcoes: "2 pessoas",
        icon: "fas fa-cube"
    },
    {
        id: 11,
        nome: "Lula à Dorê",
        preco: "R$ 36,90",
        descricao: "Anéis de lula empanados com molho aioli",
        imagem: "./images/petiscos/lula-dore.jpg",
        categoria: "Frutos do Mar",
        ingredientes: "Lula fresca, farinha de trigo, ovos, farinha panko, molho aioli, limão",
        peso: "300g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-ring"
    },
    {
        id: 12,
        nome: "Escondidinho de Carne Seca",
        preco: "R$ 34,90",
        descricao: "Camadas de purê de mandioca e carne seca desfiada",
        imagem: "./images/petiscos/escondidinho.jpg",
        categoria: "Brasileira",
        ingredientes: "Carne seca desfiada, purê de mandioca, queijo coalho, cebola, alho, cheiro-verde",
        peso: "350g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-layer-group"
    },
    {
        id: 13,
        nome: "Fondue de Queijo",
        preco: "R$ 48,90",
        descricao: "Queijos derretidos com pães e vegetais para mergulhar",
        imagem: "./images/petiscos/fondue.jpg",
        categoria: "Vegetariano",
        ingredientes: "Queijo gruyère, queijo emmental, vinho branco, pães variados, brócolis, cenoura",
        peso: "400g",
        porcoes: "3-4 pessoas",
        icon: "fas fa-fire"
    },
    {
        id: 14,
        nome: "Pastéis Variados",
        preco: "R$ 25,90",
        descricao: "Seleção de pastéis com recheios especiais",
        imagem: "./images/petiscos/pasteis.jpg",
        categoria: "Fritos",
        ingredientes: "Massa de pastel, queijo, carne, camarão, palmito, temperos especiais",
        peso: "300g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-utensils"
    },
    {
        id: 15,
        nome: "Trio de Patês",
        preco: "R$ 31,90",
        descricao: "Patês artesanais de berinjela, ricota e salmão",
        imagem: "./images/petiscos/pates.jpg",
        categoria: "Vegetariano",
        ingredientes: "Berinjela, ricota temperada, salmão defumado, torradas, azeite, ervas finas",
        peso: "250g",
        porcoes: "2-3 pessoas",
        icon: "fas fa-palette"
    }
];

// === DADOS DAS BEBIDAS ===
const bebidas = [
    {
        id: 16,
        nome: "Caipirinha Premium",
        preco: "R$ 18,90",
        descricao: "Cachaça artesanal com limão tahiti e açúcar cristal",
        imagem: "./images/bebidas/caipirinha.jpg",
        categoria: "Caipirinhas",
        ingredientes: "Cachaça artesanal, limão tahiti, açúcar cristal, gelo",
        icon: "fas fa-cocktail"
    },
    {
        id: 17,
        nome: "Mojito Cubano",
        preco: "R$ 22,90",
        descricao: "Rum branco, hortelã fresca, limão e água com gás",
        imagem: "./images/bebidas/mojito.jpg",
        categoria: "Drinks",
        ingredientes: "Rum branco, hortelã fresca, limão, açúcar, água com gás, gelo",
        icon: "fas fa-leaf"
    },
    {
        id: 18,
        nome: "Cerveja Artesanal IPA",
        preco: "R$ 16,90",
        descricao: "Cerveja artesanal com lúpulo americano, 500ml",
        imagem: "./images/bebidas/cerveja-ipa.jpg",
        categoria: "Cervejas",
        ingredientes: "Malte, lúpulo americano, levedura, água mineral",
        icon: "fas fa-beer"
    },
    {
        id: 19,
        nome: "Gin Tônica Especial",
        preco: "R$ 24,90",
        descricao: "Gin premium com água tônica e botanicals",
        imagem: "./images/bebidas/gin-tonica.jpg",
        categoria: "Drinks",
        ingredientes: "Gin premium, água tônica, limão siciliano, zimbro, pepino, gelo",
        icon: "fas fa-glass-martini-alt"
    },
    {
        id: 20,
        nome: "Whisky Sour",
        preco: "R$ 28,90",
        descricao: "Whisky bourbon com limão e clara de ovo",
        imagem: "./images/bebidas/whisky-sour.jpg",
        categoria: "Drinks",
        ingredientes: "Whisky bourbon, suco de limão, xarope simples, clara de ovo, angostura",
        icon: "fas fa-glass-whiskey"
    },
    {
        id: 21,
        nome: "Sangria Espanhola",
        preco: "R$ 26,90",
        descricao: "Vinho tinto com frutas frescas e especiarias",
        imagem: "./images/bebidas/sangria.jpg",
        categoria: "Vinhos",
        ingredientes: "Vinho tinto, laranja, maçã, pêssego, canela, açúcar, água com gás",
        icon: "fas fa-wine-glass-alt"
    },
    {
        id: 22,
        nome: "Cerveja Pilsen Gelada",
        preco: "R$ 8,90",
        descricao: "Cerveja pilsen tradicional bem gelada, 350ml",
        imagem: "./images/bebidas/cerveja-pilsen.jpg",
        categoria: "Cervejas",
        ingredientes: "Malte, lúpulo, levedura, água mineral",
        icon: "fas fa-beer"
    },
    {
        id: 23,
        nome: "Cosmopolitan",
        preco: "R$ 25,90",
        descricao: "Vodka premium com cranberry e limão",
        imagem: "./images/bebidas/cosmopolitan.jpg",
        categoria: "Drinks",
        ingredientes: "Vodka premium, licor de laranja, suco de cranberry, suco de limão",
        icon: "fas fa-cocktail"
    },
    {
        id: 24,
        nome: "Chopp Artesanal",
        preco: "R$ 12,90",
        descricao: "Chopp artesanal direto do barril, 300ml",
        imagem: "./images/bebidas/chopp.svg",
        categoria: "Cervejas",
        ingredientes: "Malte especial, lúpulo nobre, levedura alemã, água pura",
        icon: "fas fa-beer"
    },
    {
        id: 25,
        nome: "Margarita Clássica",
        preco: "R$ 23,90",
        descricao: "Tequila silver com limão e sal na borda",
        imagem: "./images/bebidas/margarita.svg",
        categoria: "Drinks",
        ingredientes: "Tequila silver, licor de laranja, suco de limão, sal grosso",
        icon: "fas fa-glass-martini"
    },
    {
        id: 26,
        nome: "Vinho Tinto Reserva",
        preco: "R$ 32,90",
        descricao: "Vinho tinto encorpado, taça 150ml",
        imagem: "./images/bebidas/vinho-tinto.svg",
        categoria: "Vinhos",
        ingredientes: "Uvas Cabernet Sauvignon, Merlot, taninos, 13% álcool",
        icon: "fas fa-wine-glass"
    },
    {
        id: 27,
        nome: "Piña Colada",
        preco: "R$ 21,90",
        descricao: "Rum branco com coco e abacaxi",
        imagem: "./images/bebidas/pina-colada.svg",
        categoria: "Drinks",
        ingredientes: "Rum branco, leite de coco, suco de abacaxi, gelo, cereja",
        icon: "fas fa-coconut"
    },
    {
        id: 28,
        nome: "Cerveja Weiss",
        preco: "R$ 14,90",
        descricao: "Cerveja de trigo alemã, 500ml",
        imagem: "./images/bebidas/cerveja-weiss.svg",
        categoria: "Cervejas",
        ingredientes: "Malte de trigo, malte de cevada, lúpulo, levedura alemã",
        icon: "fas fa-beer"
    },
    {
        id: 29,
        nome: "Old Fashioned",
        preco: "R$ 29,90",
        descricao: "Whisky bourbon com açúcar e angostura",
        imagem: "./images/bebidas/old-fashioned.svg",
        categoria: "Drinks",
        ingredientes: "Whisky bourbon, açúcar mascavo, angostura, casca de laranja, gelo",
        icon: "fas fa-glass-whiskey"
    },
    {
        id: 30,
        nome: "Espumante Brut",
        preco: "R$ 19,90",
        descricao: "Espumante nacional seco, taça 120ml",
        imagem: "./images/bebidas/espumante.svg",
        categoria: "Vinhos",
        ingredientes: "Uvas Chardonnay, Pinot Noir, método tradicional, 12% álcool",
        icon: "fas fa-champagne-glasses"
    }
];

// === FUNÇÕES PRINCIPAIS ===

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log('🍺 El Terreno Pub - Cardápio Digital Carregado!');
    
    // Carregar itens do cardápio
    carregarPetiscos();
    carregarBebidas();
    
    // Inicializar eventos
    inicializarEventos();
    
    // Animação de entrada
    setTimeout(() => {
        document.querySelectorAll('.menu-item').forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }, 500);
});

// Carregar petiscos
function carregarPetiscos() {
    const grid = document.getElementById('petiscosGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    petiscos.forEach(item => {
        const itemElement = criarItemCardapio(item);
        grid.appendChild(itemElement);
    });
}

// Carregar bebidas
function carregarBebidas() {
    const grid = document.getElementById('bebidasGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    bebidas.forEach(item => {
        const itemElement = criarItemCardapio(item);
        grid.appendChild(itemElement);
    });
}

// Criar elemento do item do cardápio
function criarItemCardapio(item) {
    const itemDiv = document.createElement('div');
    itemDiv.className = 'menu-item';
    itemDiv.style.opacity = '0';
    itemDiv.style.transform = 'translateY(30px)';
    itemDiv.setAttribute('data-id', item.id);
    
    // Usar imagem padrão se não existir - detectar se é bebida
    const isBeverage = item.categoria === 'Bebidas' || (item.imagem && item.imagem.includes('/bebidas/'));
    const defaultImage = isBeverage ? './images/default-beverage.svg' : './images/default-food.svg';
    const imagemSrc = item.imagem || defaultImage;
    
    itemDiv.innerHTML = `
        <div class="menu-item-image" style="background-image: url('${imagemSrc}')" onerror="this.style.backgroundImage='url(${defaultImage})'"></div>
        <div class="menu-item-content">
            <div class="menu-item-header">
                <h3 class="menu-item-name">${item.nome}</h3>
                <span class="menu-item-price">${item.preco}</span>
            </div>
            <p class="menu-item-description">${item.descricao}</p>
            <div class="menu-item-footer">
                <span class="menu-item-category">${item.categoria}</span>
                <i class="menu-item-icon ${item.icon}"></i>
            </div>
        </div>
    `;
    
    // Adicionar evento de clique
    itemDiv.addEventListener('click', () => abrirModal(item));
    
    // Adicionar evento de teclado para acessibilidade
    itemDiv.setAttribute('tabindex', '0');
    itemDiv.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            abrirModal(item);
        }
    });
    
    return itemDiv;
}

// Abrir modal com detalhes do item
function abrirModal(item) {
    const modal = document.getElementById('itemModal');
    const modalTitle = document.getElementById('itemModalLabel');
    const modalBody = document.getElementById('modalBody');
    const orderBtn = document.getElementById('orderBtn');
    
    if (!modal || !modalTitle || !modalBody) return;
    
    // Definir título
    modalTitle.textContent = item.nome;
    
    // Criar conteúdo do modal - detectar se é bebida
    const isBeverage = item.categoria === 'Bebidas' || (item.imagem && item.imagem.includes('/bebidas/'));
    const defaultImage = isBeverage ? './images/default-beverage.svg' : './images/default-food.svg';
    const imagemSrc = item.imagem || defaultImage;
    
    let modalContent = `
        <div class="row">
            <div class="col-md-6">
                <img src="${imagemSrc}" alt="${item.nome}" class="modal-item-image" onerror="this.src='${defaultImage}'">
            </div>
            <div class="col-md-6">
                <div class="modal-item-details">
                    <h4><i class="${item.icon}"></i> ${item.nome}</h4>
                    <p><strong>Preço:</strong> <span style="color: var(--primary-color); font-size: 1.2em;">${item.preco}</span></p>
                    <p><strong>Categoria:</strong> ${item.categoria}</p>
                    <p><strong>Descrição:</strong> ${item.descricao}</p>
                    
                    <div class="modal-item-specs">
                        <h5><i class="fas fa-list-ul"></i> Ingredientes:</h5>
                        <p>${item.ingredientes}</p>
    `;
    
    // Adicionar informações específicas para petiscos
    if (item.peso && item.porcoes) {
        modalContent += `
                        <p><strong><i class="fas fa-weight"></i> Peso:</strong> ${item.peso}</p>
                        <p><strong><i class="fas fa-users"></i> Serve:</strong> ${item.porcoes}</p>
        `;
    }
    
    modalContent += `
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modalBody.innerHTML = modalContent;
    
    // Configurar botão de pedido
    if (orderBtn) {
        orderBtn.onclick = () => fazerPedido(item);
    }
    
    // Abrir modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Fazer pedido (integração com WhatsApp)
function fazerPedido(item) {
    const mensagem = `Olá! Gostaria de pedir:\n\n*${item.nome}*\nPreço: ${item.preco}\n\nObrigado!`;
    const numeroWhatsApp = '5532999999999'; // Substitua pelo número real
    const url = `https://wa.me/${numeroWhatsApp}?text=${encodeURIComponent(mensagem)}`;
    
    window.open(url, '_blank');
    
    // Fechar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('itemModal'));
    if (modal) {
        modal.hide();
    }
}

// Inicializar eventos
function inicializarEventos() {
    // Smooth scroll para seções
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Lazy loading para imagens
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.style.backgroundImage = img.dataset.src;
                    img.classList.remove('loading');
                    observer.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('.menu-item-image[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Animação de scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.pub-carousel');
        if (parallax) {
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        }
    });
}

// Função de busca (para implementação futura)
function buscarItens(termo) {
    const todoItens = [...petiscos, ...bebidas];
    return todoItens.filter(item => 
        item.nome.toLowerCase().includes(termo.toLowerCase()) ||
        item.categoria.toLowerCase().includes(termo.toLowerCase()) ||
        item.ingredientes.toLowerCase().includes(termo.toLowerCase())
    );
}

// Filtrar por categoria
function filtrarPorCategoria(categoria, tipo = 'todos') {
    let itens = [];
    
    if (tipo === 'petiscos' || tipo === 'todos') {
        itens = [...itens, ...petiscos];
    }
    
    if (tipo === 'bebidas' || tipo === 'todos') {
        itens = [...itens, ...bebidas];
    }
    
    if (categoria === 'todos') {
        return itens;
    }
    
    return itens.filter(item => item.categoria.toLowerCase() === categoria.toLowerCase());
}

// Exportar funções para uso global
window.pubFunctions = {
    buscarItens,
    filtrarPorCategoria,
    abrirModal,
    fazerPedido
};

console.log('🍻 El Terreno Pub Scripts carregados com sucesso!');
console.log('📱 Funções disponíveis:', Object.keys(window.pubFunctions));
console.log('🍽️ Total de petiscos:', petiscos.length);
console.log('🍹 Total de bebidas:', bebidas.length);

// === TRATAMENTO DE ERROS ===
window.addEventListener('error', function(e) {
    console.error('Erro no Pub Scripts:', e.error);
});

// === PERFORMANCE ===
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registrado com sucesso:', registration.scope);
            })
            .catch(function(error) {
                console.log('Falha ao registrar ServiceWorker:', error);
            });
    });
}