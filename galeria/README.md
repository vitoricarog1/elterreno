# 🖼️ Sistema de Galeria de Fotos em PHP

Sistema completo de galeria de fotos desenvolvido em PHP puro com MySQL, Bootstrap e jQuery.

## 📋 Características

- **Backend:** PHP 8+ com MySQL
- **Frontend:** Bootstrap 5, Font Awesome, Lightbox2
- **Arquitetura:** MVC simplificada
- **Responsivo:** Design adaptável para todos os dispositivos
- **Segurança:** Prepared statements, validação de uploads, autenticação

## 🚀 Funcionalidades

### 👥 Área Pública
- ✅ **Página Principal:** Grid responsivo com todos os álbuns
- ✅ **Página Últimos:** 3 álbuns mais recentes
- ✅ **Visualização de Álbum:** Lightbox com paginação (50 fotos/página)
- ✅ **Design Responsivo:** Funciona em mobile, tablet e desktop

### 🔐 Painel Administrativo
- ✅ **Dashboard:** Estatísticas e visão geral
- ✅ **Criar Álbuns:** Formulário completo
- ✅ **Gerenciar Álbuns:** Listar, editar e deletar
- ✅ **Upload Múltiplo:** Drag & drop com preview
- ✅ **Otimização Automática:** Redimensionamento e thumbnails

## 📁 Estrutura do Projeto

```
/
├── index.php              # Página principal (grid de álbuns)
├── ultimos.php            # Página dos últimos álbuns
└── /gallerydev/
    ├── /admin/            # Painel administrativo
    │   ├── index.php      # Login
    │   ├── dashboard.php  # Dashboard principal
    │   ├── criar-album.php
    │   ├── gerenciar-albums.php
    │   └── upload-fotos.php
    ├── /includes/         # Arquivos de configuração
    │   ├── config.php     # Configurações gerais
    │   ├── database.php   # Classe de banco de dados
    │   ├── functions.php  # Funções auxiliares
    │   └── auth.php       # Sistema de autenticação
    ├── /uploads/          # Arquivos enviados
    │   ├── /albums/       # Fotos originais
    │   └── /thumbs/       # Thumbnails
    ├── /assets/           # CSS e JS customizados
    ├── album.php          # Visualização individual
    └── install.php        # Script de instalação
```

## ⚙️ Instalação

### 1. Requisitos
- PHP 8.0+
- MySQL 5.7+
- Extensões PHP: PDO, GD, fileinfo
- Servidor web (Apache/Nginx)

### 2. Configuração Local (XAMPP/WAMP)

1. **Clone/baixe o projeto** para a pasta do servidor:
   ```
   C:\xampp\htdocs\galeria\
   ```

2. **Execute a instalação:**
   ```
   http://localhost/galeria/gallerydev/install.php
   ```

3. **Configure o usuário admin** (padrão: admin/admin123)

4. **Acesse a galeria:**
   ```
   http://localhost/galeria/
   ```

### 3. Configuração em Produção

1. **Upload dos arquivos** para o servidor
2. **Ajuste as configurações** em `gallerydev/includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   define('DB_NAME', 'galeria_fotos');
   ```
3. **Execute a instalação** via navegador
4. **Configure permissões** da pasta uploads (755)

## 🗄️ Banco de Dados

### Tabelas Criadas Automaticamente:

**albums:**
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `nome` (VARCHAR 255) - Nome do álbum
- `data_evento` (DATE) - Data do evento
- `edicao` (VARCHAR 100) - Edição/ano
- `foto_capa` (VARCHAR 255) - Arquivo da foto de capa
- `created_at` (TIMESTAMP) - Data de criação

**fotos:**
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `album_id` (INT, FOREIGN KEY) - Referência ao álbum
- `nome_arquivo` (VARCHAR 255) - Nome do arquivo
- `created_at` (TIMESTAMP) - Data do upload

**admin_users:**
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `username` (VARCHAR 50, UNIQUE) - Nome de usuário
- `password` (VARCHAR 255) - Senha criptografada
- `created_at` (TIMESTAMP) - Data de criação

## 🔧 Configurações

### Upload de Imagens:
- **Formatos aceitos:** JPG, PNG, GIF
- **Tamanho máximo:** 10MB por arquivo
- **Redimensionamento:** Máximo 1920x1080px
- **Thumbnails:** 300x300px automáticos

### Segurança:
- Prepared statements (anti SQL injection)
- Validação rigorosa de uploads
- Sanitização de inputs (anti XSS)
- Autenticação com sessões seguras
- Timeout de sessão (1 hora)

## 🎨 Design

- **Framework:** Bootstrap 5
- **Ícones:** Font Awesome 6
- **Lightbox:** Lightbox2
- **Cores:** Gradiente roxo/azul
- **Responsivo:** Mobile-first
- **Animações:** Hover states e transições suaves

## 📱 Recursos Responsivos

- **Mobile:** Grid adaptável, navegação otimizada
- **Tablet:** Layout intermediário balanceado
- **Desktop:** Grid completo com hover effects

## 🔐 Acesso Administrativo

**URL:** `/gallerydev/admin/`
**Usuário padrão:** admin
**Senha padrão:** admin123

⚠️ **IMPORTANTE:** Altere as credenciais após a instalação!

## 🛠️ Manutenção

### Backup:
- Exportar banco de dados MySQL
- Copiar pasta `/gallerydev/uploads/`

### Limpeza:
- Thumbnails são gerados automaticamente
- Arquivos órfãos podem ser removidos manualmente

## 📞 Suporte

Sistema desenvolvido seguindo as melhores práticas de:
- Segurança web
- Performance
- Usabilidade
- Manutenibilidade

---

**Versão:** 1.0  
**Desenvolvido em:** 2025  
**Compatibilidade:** PHP 8+, MySQL 5.7+