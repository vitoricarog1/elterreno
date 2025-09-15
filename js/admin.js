// Admin panel functionality
let albumsData = [];

function initializeAdmin() {
    setupTabs();
    setupCreateAlbumForm();
    setupUploadForm();
    loadAlbumsForSelect();
    loadAlbumsForManagement();
}

// Tab functionality
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.id.replace('tab-', 'content-');
            
            // Remove active classes
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('border-transparent', 'text-slate-500');
                btn.classList.remove('border-blue-500', 'text-blue-600');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            this.classList.remove('border-transparent', 'text-slate-500');
            this.classList.add('border-blue-500', 'text-blue-600');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target tab content
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }

            // Load data based on active tab
            if (targetTab === 'content-upload-photos') {
                loadAlbumsForSelect();
            } else if (targetTab === 'content-manage-albums') {
                loadAlbumsForManagement();
            }
        });
    });
}

// Create album form
function setupCreateAlbumForm() {
    const form = document.getElementById('create-album-form');
    const resetBtn = document.getElementById('reset-form');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const data = {
                nome: formData.get('nome'),
                descricao: formData.get('descricao'),
                data: formData.get('data')
            };
            
            try {
                showMessage('Criando álbum...', 'info');
                
                const response = await fetch('api/criar-album.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    form.reset();
                    loadAlbumsForSelect();
                    loadAlbumsForManagement();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                showMessage('Erro ao criar álbum: ' + error.message, 'error');
            }
        });
    }
    
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            form.reset();
        });
    }
}

// Upload form
function setupUploadForm() {
    const selectPhotosBtn = document.getElementById('select-photos');
    const photosInput = document.getElementById('photos-input');
    const uploadBtn = document.getElementById('upload-photos');
    const albumSelect = document.getElementById('select-album');
    
    if (selectPhotosBtn && photosInput) {
        selectPhotosBtn.addEventListener('click', function() {
            photosInput.click();
        });
        
        photosInput.addEventListener('change', function() {
            handleFileSelection(this.files);
        });
    }
    
    if (uploadBtn) {
        uploadBtn.addEventListener('click', async function() {
            await uploadPhotos();
        });
    }
    
    // Drag and drop
    const uploadArea = document.querySelector('.border-dashed');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('upload-area-dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('upload-area-dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('upload-area-dragover');
            handleFileSelection(e.dataTransfer.files);
        });
    }
}

function handleFileSelection(files) {
    const selectedPhotos = document.getElementById('selected-photos');
    const photosPreview = document.getElementById('photos-preview');
    
    if (files.length === 0) return;
    
    selectedPhotos.classList.remove('hidden');
    photosPreview.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'photo-preview relative group';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}" class="w-full h-20 object-cover rounded-lg">
                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition-opacity" onclick="removePhoto(${index})">×</button>
                    <p class="text-xs text-slate-600 mt-1 truncate">${file.name}</p>
                `;
                photosPreview.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
}

function removePhoto(index) {
    const photosInput = document.getElementById('photos-input');
    const dt = new DataTransfer();
    const files = photosInput.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    photosInput.files = dt.files;
    handleFileSelection(photosInput.files);
}

async function uploadPhotos() {
    const albumSelect = document.getElementById('select-album');
    const photosInput = document.getElementById('photos-input');
    const uploadProgress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    if (!albumSelect.value) {
        showMessage('Selecione um álbum primeiro', 'error');
        return;
    }
    
    if (!photosInput.files.length) {
        showMessage('Selecione as fotos primeiro', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('album_id', albumSelect.value);
    
    Array.from(photosInput.files).forEach(file => {
        formData.append('fotos[]', file);
    });
    
    try {
        uploadProgress.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressText.textContent = percentComplete + '%';
            }
        });
        
        xhr.addEventListener('load', function() {
            uploadProgress.classList.add('hidden');
            
            if (xhr.status === 200) {
                const result = JSON.parse(xhr.responseText);
                if (result.success) {
                    showMessage(result.message, 'success');
                    photosInput.value = '';
                    document.getElementById('selected-photos').classList.add('hidden');
                } else {
                    showMessage(result.message, 'error');
                }
            } else {
                showMessage('Erro no upload', 'error');
            }
        });
        
        xhr.addEventListener('error', function() {
            uploadProgress.classList.add('hidden');
            showMessage('Erro no upload', 'error');
        });
        
        xhr.open('POST', 'api/upload-fotos.php');
        xhr.send(formData);
        
    } catch (error) {
        uploadProgress.classList.add('hidden');
        showMessage('Erro no upload: ' + error.message, 'error');
    }
}

// Load albums for select dropdown
async function loadAlbumsForSelect() {
    const select = document.getElementById('select-album');
    if (!select) return;
    
    try {
        const response = await fetch('api/listar-albuns.php');
        const data = await response.json();
        
        select.innerHTML = '<option value="">Selecione um álbum</option>';
        
        if (data.success && data.albuns) {
            data.albuns.forEach(album => {
                const option = document.createElement('option');
                option.value = album.id;
                option.textContent = album.nome;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar álbuns:', error);
    }
}

// Load albums for management
async function loadAlbumsForManagement() {
    const loading = document.getElementById('albums-loading');
    const list = document.getElementById('albums-list');
    const empty = document.getElementById('albums-empty');
    
    loading.classList.remove('hidden');
    list.classList.add('hidden');
    empty.classList.add('hidden');
    
    try {
        const response = await fetch('api/listar-albuns.php');
        const data = await response.json();
        
        loading.classList.add('hidden');
        
        if (data.success && data.albuns.length > 0) {
            displayAlbumsForManagement(data.albuns);
            list.classList.remove('hidden');
        } else {
            empty.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Erro ao carregar álbuns:', error);
        loading.classList.add('hidden');
        empty.classList.remove('hidden');
    }
}

function displayAlbumsForManagement(albums) {
    const list = document.getElementById('albums-list');
    list.innerHTML = '';
    
    albums.forEach(album => {
        const albumItem = document.createElement('div');
        albumItem.className = 'bg-white border border-slate-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow';
        
        albumItem.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-800 mb-2">${escapeHtml(album.nome)}</h3>
                    <p class="text-slate-600 text-sm mb-2">${escapeHtml(album.descricao || 'Sem descrição')}</p>
                    <div class="flex items-center space-x-4 text-sm text-slate-500">
                        <span>${formatDate(album.data)}</span>
                        <span>${album.total_fotos} fotos</span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="galeria.php?album=${album.id}" target="_blank" class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Ver
                    </a>
                    <button onclick="deleteAlbum(${album.id}, '${escapeHtml(album.nome)}')" class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Excluir
                    </button>
                </div>
            </div>
        `;
        
        list.appendChild(albumItem);
    });
}

async function deleteAlbum(albumId, albumName) {
    if (!confirm(`Tem certeza que deseja excluir o álbum "${albumName}"? Todas as fotos serão removidas permanentemente.`)) {
        return;
    }
    
    try {
        const response = await fetch('api/deletar-album.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ album_id: albumId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            loadAlbumsForManagement();
            loadAlbumsForSelect();
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Erro ao excluir álbum: ' + error.message, 'error');
    }
}

// Utility functions
function showMessage(message, type) {
    const messagesDiv = document.getElementById('messages');
    const messageEl = document.createElement('div');
    messageEl.className = `message-${type}`;
    messageEl.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-current opacity-70 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    messagesDiv.innerHTML = '';
    messagesDiv.appendChild(messageEl);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (messageEl.parentElement) {
            messageEl.remove();
        }
    }, 5000);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}