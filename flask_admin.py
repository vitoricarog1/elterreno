#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Flask Admin Panel para El Terreno
Sistema de administração via web para manipulação do banco de dados
"""

from flask import Flask, render_template, request, jsonify, redirect, url_for, flash, session
from flask_sqlalchemy import SQLAlchemy
from werkzeug.security import check_password_hash, generate_password_hash
from werkzeug.utils import secure_filename
from datetime import datetime
import os
import json
from functools import wraps

app = Flask(__name__)
app.config['SECRET_KEY'] = 'el-terreno-admin-2025'

# Configuração do banco de dados
# Para desenvolvimento local (XAMPP)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:@localhost/el_terreno'

# Para produção (Hostinger) - descomente e ajuste
# app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://u945783144_elterreno:#Adidas777@localhost/u945783144_elterreno'

app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['UPLOAD_FOLDER'] = 'uploads'
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max file size

db = SQLAlchemy(app)

# Modelos do banco de dados
class Album(db.Model):
    __tablename__ = 'albums'
    
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(255), nullable=False)
    descricao = db.Column(db.Text)
    data_evento = db.Column(db.Date, nullable=False)
    edicao = db.Column(db.String(100), nullable=False)
    foto_capa = db.Column(db.String(255))
    ordem = db.Column(db.Integer, default=0)
    ativo = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    fotos = db.relationship('Foto', backref='album', lazy=True, cascade='all, delete-orphan')

class Foto(db.Model):
    __tablename__ = 'fotos'
    
    id = db.Column(db.Integer, primary_key=True)
    album_id = db.Column(db.Integer, db.ForeignKey('albums.id'), nullable=False)
    nome_arquivo = db.Column(db.String(255), nullable=False)
    nome_original = db.Column(db.String(255), nullable=False)
    tamanho = db.Column(db.Integer, nullable=False)
    ordem = db.Column(db.Integer, default=0)
    ativo = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

class Carrossel(db.Model):
    __tablename__ = 'carrossel'
    
    id = db.Column(db.Integer, primary_key=True)
    titulo = db.Column(db.String(255), nullable=False)
    subtitulo = db.Column(db.String(255))
    imagem_desktop = db.Column(db.String(255), nullable=False)
    imagem_mobile = db.Column(db.String(255), nullable=False)
    link = db.Column(db.String(255))
    ordem = db.Column(db.Integer, default=0)
    ativo = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

class CardapioCategoria(db.Model):
    __tablename__ = 'cardapio_categorias'
    
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100), nullable=False)
    ordem = db.Column(db.Integer, default=0)
    ativo = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    
    itens = db.relationship('CardapioItem', backref='categoria', lazy=True)

class CardapioItem(db.Model):
    __tablename__ = 'cardapio_itens'
    
    id = db.Column(db.Integer, primary_key=True)
    categoria_id = db.Column(db.Integer, db.ForeignKey('cardapio_categorias.id'), nullable=False)
    nome = db.Column(db.String(255), nullable=False)
    preco = db.Column(db.Numeric(10, 2), nullable=False)
    descricao = db.Column(db.Text)
    ordem = db.Column(db.Integer, default=0)
    ativo = db.Column(db.Boolean, default=True)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

# Decorador para verificar login
def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'logged_in' not in session:
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

# Rotas de autenticação
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        
        # Credenciais fixas (mesmo do PHP)
        if username == 'plug' and password == '#Adidas777':
            session['logged_in'] = True
            session['username'] = username
            flash('Login realizado com sucesso!', 'success')
            return redirect(url_for('dashboard'))
        else:
            flash('Usuário ou senha incorretos!', 'error')
    
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.clear()
    flash('Logout realizado com sucesso!', 'success')
    return redirect(url_for('login'))

# Dashboard
@app.route('/')
@login_required
def dashboard():
    stats = {
        'albums': Album.query.filter_by(ativo=True).count(),
        'fotos': Foto.query.filter_by(ativo=True).count(),
        'carrossel': Carrossel.query.filter_by(ativo=True).count(),
        'cardapio': CardapioItem.query.filter_by(ativo=True).count()
    }
    
    ultimos_albums = db.session.query(Album, db.func.count(Foto.id).label('total_fotos'))\
        .outerjoin(Foto, (Album.id == Foto.album_id) & (Foto.ativo == True))\
        .filter(Album.ativo == True)\
        .group_by(Album.id)\
        .order_by(Album.created_at.desc())\
        .limit(5).all()
    
    return render_template('dashboard.html', stats=stats, ultimos_albums=ultimos_albums)

# API Routes para CRUD

# Albums API
@app.route('/api/albums')
@login_required
def api_albums():
    albums = db.session.query(Album, db.func.count(Foto.id).label('total_fotos'))\
        .outerjoin(Foto, (Album.id == Foto.album_id) & (Foto.ativo == True))\
        .filter(Album.ativo == True)\
        .group_by(Album.id)\
        .order_by(Album.created_at.desc()).all()
    
    result = []
    for album, total_fotos in albums:
        result.append({
            'id': album.id,
            'nome': album.nome,
            'descricao': album.descricao,
            'data_evento': album.data_evento.strftime('%Y-%m-%d'),
            'edicao': album.edicao,
            'foto_capa': album.foto_capa,
            'total_fotos': total_fotos,
            'created_at': album.created_at.strftime('%Y-%m-%d %H:%M:%S')
        })
    
    return jsonify(result)

@app.route('/api/albums', methods=['POST'])
@login_required
def api_create_album():
    data = request.get_json()
    
    album = Album(
        nome=data['nome'],
        descricao=data.get('descricao', ''),
        data_evento=datetime.strptime(data['data_evento'], '%Y-%m-%d').date(),
        edicao=data['edicao']
    )
    
    db.session.add(album)
    db.session.commit()
    
    return jsonify({'success': True, 'id': album.id})

@app.route('/api/albums/<int:album_id>', methods=['PUT'])
@login_required
def api_update_album(album_id):
    album = Album.query.get_or_404(album_id)
    data = request.get_json()
    
    album.nome = data['nome']
    album.descricao = data.get('descricao', '')
    album.data_evento = datetime.strptime(data['data_evento'], '%Y-%m-%d').date()
    album.edicao = data['edicao']
    album.updated_at = datetime.utcnow()
    
    db.session.commit()
    
    return jsonify({'success': True})

@app.route('/api/albums/<int:album_id>', methods=['DELETE'])
@login_required
def api_delete_album(album_id):
    album = Album.query.get_or_404(album_id)
    album.ativo = False
    db.session.commit()
    
    return jsonify({'success': True})

# Fotos API
@app.route('/api/albums/<int:album_id>/fotos')
@login_required
def api_album_fotos(album_id):
    fotos = Foto.query.filter_by(album_id=album_id, ativo=True)\
        .order_by(Foto.ordem, Foto.created_at).all()
    
    result = []
    for foto in fotos:
        result.append({
            'id': foto.id,
            'nome_arquivo': foto.nome_arquivo,
            'nome_original': foto.nome_original,
            'tamanho': foto.tamanho,
            'created_at': foto.created_at.strftime('%Y-%m-%d %H:%M:%S')
        })
    
    return jsonify(result)

# Carrossel API
@app.route('/api/carrossel')
@login_required
def api_carrossel():
    slides = Carrossel.query.filter_by(ativo=True)\
        .order_by(Carrossel.ordem, Carrossel.created_at).all()
    
    result = []
    for slide in slides:
        result.append({
            'id': slide.id,
            'titulo': slide.titulo,
            'subtitulo': slide.subtitulo,
            'imagem_desktop': slide.imagem_desktop,
            'imagem_mobile': slide.imagem_mobile,
            'link': slide.link,
            'ordem': slide.ordem,
            'created_at': slide.created_at.strftime('%Y-%m-%d %H:%M:%S')
        })
    
    return jsonify(result)

@app.route('/api/carrossel', methods=['POST'])
@login_required
def api_create_slide():
    data = request.get_json()
    
    slide = Carrossel(
        titulo=data['titulo'],
        subtitulo=data.get('subtitulo', ''),
        imagem_desktop=data['imagem_desktop'],
        imagem_mobile=data['imagem_mobile'],
        link=data.get('link', '')
    )
    
    db.session.add(slide)
    db.session.commit()
    
    return jsonify({'success': True, 'id': slide.id})

@app.route('/api/carrossel/<int:slide_id>', methods=['DELETE'])
@login_required
def api_delete_slide(slide_id):
    slide = Carrossel.query.get_or_404(slide_id)
    slide.ativo = False
    db.session.commit()
    
    return jsonify({'success': True})

# Cardápio API
@app.route('/api/cardapio')
@login_required
def api_cardapio():
    categorias = CardapioCategoria.query.filter_by(ativo=True)\
        .order_by(CardapioCategoria.ordem).all()
    
    result = []
    for categoria in categorias:
        itens = CardapioItem.query.filter_by(categoria_id=categoria.id, ativo=True)\
            .order_by(CardapioItem.ordem, CardapioItem.nome).all()
        
        itens_data = []
        for item in itens:
            itens_data.append({
                'id': item.id,
                'nome': item.nome,
                'preco': float(item.preco),
                'descricao': item.descricao,
                'ordem': item.ordem
            })
        
        result.append({
            'id': categoria.id,
            'nome': categoria.nome,
            'ordem': categoria.ordem,
            'itens': itens_data
        })
    
    return jsonify(result)

@app.route('/api/cardapio/itens', methods=['POST'])
@login_required
def api_create_item():
    data = request.get_json()
    
    item = CardapioItem(
        categoria_id=data['categoria_id'],
        nome=data['nome'],
        preco=data['preco'],
        descricao=data.get('descricao', '')
    )
    
    db.session.add(item)
    db.session.commit()
    
    return jsonify({'success': True, 'id': item.id})

@app.route('/api/cardapio/itens/<int:item_id>', methods=['PUT'])
@login_required
def api_update_item(item_id):
    item = CardapioItem.query.get_or_404(item_id)
    data = request.get_json()
    
    item.categoria_id = data['categoria_id']
    item.nome = data['nome']
    item.preco = data['preco']
    item.descricao = data.get('descricao', '')
    item.updated_at = datetime.utcnow()
    
    db.session.commit()
    
    return jsonify({'success': True})

@app.route('/api/cardapio/itens/<int:item_id>', methods=['DELETE'])
@login_required
def api_delete_item(item_id):
    item = CardapioItem.query.get_or_404(item_id)
    item.ativo = False
    db.session.commit()
    
    return jsonify({'success': True})

# Estatísticas e relatórios
@app.route('/api/stats')
@login_required
def api_stats():
    stats = {
        'albums_total': Album.query.filter_by(ativo=True).count(),
        'fotos_total': Foto.query.filter_by(ativo=True).count(),
        'carrossel_total': Carrossel.query.filter_by(ativo=True).count(),
        'cardapio_total': CardapioItem.query.filter_by(ativo=True).count(),
        'albums_mes': Album.query.filter(
            Album.ativo == True,
            Album.created_at >= datetime.now().replace(day=1)
        ).count(),
        'fotos_mes': Foto.query.filter(
            Foto.ativo == True,
            Foto.created_at >= datetime.now().replace(day=1)
        ).count()
    }
    
    return jsonify(stats)

# Backup e exportação
@app.route('/api/backup')
@login_required
def api_backup():
    """Gerar backup dos dados em JSON"""
    backup_data = {
        'timestamp': datetime.now().isoformat(),
        'albums': [],
        'carrossel': [],
        'cardapio': []
    }
    
    # Albums com fotos
    albums = Album.query.filter_by(ativo=True).all()
    for album in albums:
        fotos = Foto.query.filter_by(album_id=album.id, ativo=True).all()
        backup_data['albums'].append({
            'nome': album.nome,
            'descricao': album.descricao,
            'data_evento': album.data_evento.isoformat(),
            'edicao': album.edicao,
            'fotos': [{'nome_arquivo': f.nome_arquivo, 'nome_original': f.nome_original} for f in fotos]
        })
    
    # Carrossel
    slides = Carrossel.query.filter_by(ativo=True).all()
    for slide in slides:
        backup_data['carrossel'].append({
            'titulo': slide.titulo,
            'subtitulo': slide.subtitulo,
            'imagem_desktop': slide.imagem_desktop,
            'imagem_mobile': slide.imagem_mobile,
            'link': slide.link
        })
    
    # Cardápio
    categorias = CardapioCategoria.query.filter_by(ativo=True).all()
    for categoria in categorias:
        itens = CardapioItem.query.filter_by(categoria_id=categoria.id, ativo=True).all()
        backup_data['cardapio'].append({
            'categoria': categoria.nome,
            'itens': [{'nome': i.nome, 'preco': float(i.preco), 'descricao': i.descricao} for i in itens]
        })
    
    return jsonify(backup_data)

# Templates HTML básicos
@app.before_first_request
def create_templates():
    """Criar templates básicos se não existirem"""
    templates_dir = 'templates'
    if not os.path.exists(templates_dir):
        os.makedirs(templates_dir)
    
    # Template base
    base_template = '''<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}El Terreno Admin{% endblock %}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6d5db2;
            --secondary-color: #ff248e;
            --gradient: linear-gradient(135deg, #6d5db2, #ff248e, #7f67db);
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand { background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-primary { background: var(--gradient); border: none; }
        .card { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: none; }
    </style>
</head>
<body>
    {% if session.logged_in %}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url_for('dashboard') }}">
                <i class="fas fa-cog me-2"></i>El Terreno Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ url_for('logout') }}">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>
    {% endif %}
    
    <div class="container mt-4">
        {% with messages = get_flashed_messages(with_categories=true) %}
            {% if messages %}
                {% for category, message in messages %}
                    <div class="alert alert-{{ 'danger' if category == 'error' else 'success' }} alert-dismissible fade show">
                        {{ message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                {% endfor %}
            {% endif %}
        {% endwith %}
        
        {% block content %}{% endblock %}
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {% block scripts %}{% endblock %}
</body>
</html>'''
    
    with open(os.path.join(templates_dir, 'base.html'), 'w', encoding='utf-8') as f:
        f.write(base_template)
    
    # Template de login
    login_template = '''{% extends "base.html" %}
{% block title %}Login - El Terreno Admin{% endblock %}
{% block content %}
<div class="row justify-content-center">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-user-shield me-2"></i>Login Admin</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Usuário</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{% endblock %}'''
    
    with open(os.path.join(templates_dir, 'login.html'), 'w', encoding='utf-8') as f:
        f.write(login_template)
    
    # Template do dashboard
    dashboard_template = '''{% extends "base.html" %}
{% block content %}
<div class="row mb-4">
    <div class="col">
        <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <p class="text-muted">Painel de controle do El Terreno</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-images fa-2x text-primary mb-3"></i>
                <h3>{{ stats.albums }}</h3>
                <p class="text-muted">Álbuns</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-camera fa-2x text-success mb-3"></i>
                <h3>{{ stats.fotos }}</h3>
                <p class="text-muted">Fotos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-sliders-h fa-2x text-warning mb-3"></i>
                <h3>{{ stats.carrossel }}</h3>
                <p class="text-muted">Slides</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-utensils fa-2x text-info mb-3"></i>
                <h3>{{ stats.cardapio }}</h3>
                <p class="text-muted">Itens Cardápio</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock me-2"></i>Últimos Álbuns</h5>
            </div>
            <div class="card-body">
                {% if ultimos_albums %}
                    {% for album, total_fotos in ultimos_albums %}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ album.nome }}</strong><br>
                            <small class="text-muted">{{ album.data_evento.strftime('%d/%m/%Y') }} - {{ total_fotos }} fotos</small>
                        </div>
                    </div>
                    {% endfor %}
                {% else %}
                    <p class="text-muted">Nenhum álbum encontrado</p>
                {% endif %}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tools me-2"></i>Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="loadAlbums()">
                        <i class="fas fa-images me-2"></i>Gerenciar Álbuns
                    </button>
                    <button class="btn btn-outline-success" onclick="loadCarrossel()">
                        <i class="fas fa-sliders-h me-2"></i>Gerenciar Carrossel
                    </button>
                    <button class="btn btn-outline-warning" onclick="loadCardapio()">
                        <i class="fas fa-utensils me-2"></i>Gerenciar Cardápio
                    </button>
                    <button class="btn btn-outline-info" onclick="downloadBackup()">
                        <i class="fas fa-download me-2"></i>Backup Dados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir dados -->
<div class="modal fade" id="dataModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Dados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Conteúdo será carregado via AJAX -->
            </div>
        </div>
    </div>
</div>
{% endblock %}

{% block scripts %}
<script>
async function loadAlbums() {
    try {
        const response = await fetch('/api/albums');
        const albums = await response.json();
        
        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Nome</th><th>Data</th><th>Fotos</th><th>Criado</th></tr></thead><tbody>';
        albums.forEach(album => {
            html += `<tr>
                <td><strong>${album.nome}</strong><br><small class="text-muted">${album.descricao || ''}</small></td>
                <td>${new Date(album.data_evento).toLocaleDateString('pt-BR')}</td>
                <td><span class="badge bg-info">${album.total_fotos}</span></td>
                <td>${new Date(album.created_at).toLocaleDateString('pt-BR')}</td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        
        document.getElementById('modalTitle').textContent = 'Álbuns';
        document.getElementById('modalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('dataModal')).show();
    } catch (error) {
        alert('Erro ao carregar álbuns: ' + error.message);
    }
}

async function loadCarrossel() {
    try {
        const response = await fetch('/api/carrossel');
        const slides = await response.json();
        
        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Título</th><th>Subtítulo</th><th>Link</th><th>Ordem</th></tr></thead><tbody>';
        slides.forEach(slide => {
            html += `<tr>
                <td><strong>${slide.titulo}</strong></td>
                <td>${slide.subtitulo || '-'}</td>
                <td>${slide.link ? `<a href="${slide.link}" target="_blank">Ver</a>` : '-'}</td>
                <td>${slide.ordem}</td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        
        document.getElementById('modalTitle').textContent = 'Carrossel';
        document.getElementById('modalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('dataModal')).show();
    } catch (error) {
        alert('Erro ao carregar carrossel: ' + error.message);
    }
}

async function loadCardapio() {
    try {
        const response = await fetch('/api/cardapio');
        const categorias = await response.json();
        
        let html = '';
        categorias.forEach(categoria => {
            html += `<h5><i class="fas fa-utensils me-2"></i>${categoria.nome}</h5>`;
            html += '<div class="table-responsive mb-4"><table class="table table-sm"><thead><tr><th>Item</th><th>Preço</th><th>Descrição</th></tr></thead><tbody>';
            categoria.itens.forEach(item => {
                html += `<tr>
                    <td><strong>${item.nome}</strong></td>
                    <td>R$ ${item.preco.toFixed(2).replace('.', ',')}</td>
                    <td>${item.descricao || '-'}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        });
        
        document.getElementById('modalTitle').textContent = 'Cardápio';
        document.getElementById('modalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('dataModal')).show();
    } catch (error) {
        alert('Erro ao carregar cardápio: ' + error.message);
    }
}

async function downloadBackup() {
    try {
        const response = await fetch('/api/backup');
        const backup = await response.json();
        
        const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `el-terreno-backup-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        alert('Backup baixado com sucesso!');
    } catch (error) {
        alert('Erro ao gerar backup: ' + error.message);
    }
}
</script>
{% endblock %}'''
    
    with open(os.path.join(templates_dir, 'dashboard.html'), 'w', encoding='utf-8') as f:
        f.write(dashboard_template)

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    
    print("🚀 Flask Admin Panel - El Terreno")
    print("📍 Acesse: http://localhost:5000")
    print("👤 Login: plug")
    print("🔑 Senha: #Adidas777")
    print("\n🔧 Funcionalidades:")
    print("   • Dashboard com estatísticas")
    print("   • API REST completa")
    print("   • Backup de dados")
    print("   • Interface web responsiva")
    
    app.run(debug=True, host='0.0.0.0', port=5000)