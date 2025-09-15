// Gallery functionality
let allPhotos = [];
let currentPhotos = [];
let currentAlbumId = null;
let currentPage = 1;
let totalPages = 1;
let photosPerPage = 50;

// Load albums for main page
async function loadAlbums() {
    const loading = document.getElementById('loading');
    const grid = document.getElementById('albums-grid');
    const empty = document.getElementById('empty-state');

    try {
        const response = await fetch('./api/listar-albuns.php');
        const data = await response.json();

        loading.style.display = 'none';

        if (data.success && data.albuns.length > 0) {
            displayAlbums(data.albuns);
            grid.style.display = 'block';
        } else {
            empty.style.display = 'block';
        }
    } catch (error) {
        console.error('Erro ao carregar álbuns:', error);
        loading.style.display = 'none';
        empty.style.display = 'block';
    }
}

// Display albums in grid
function displayAlbums(albums) {
    const grid = document.getElementById('albums-grid');
    grid.innerHTML = '';

    albums.forEach(album => {
        const albumCard = document.createElement('div');
        albumCard.className = 'album-card bg-white rounded-lg shadow-lg overflow-hidden cursor-pointer';
        albumCard.onclick = () => window.location.href = `galeria.php?album=${album.id}`;

        albumCard.innerHTML = `
            <div class="aspect-video bg-gradient-to-br from-blue-100 to-emerald-100 overflow-hidden">
                <div class="album-image w-full h-full flex items-center justify-center transition-transform duration-300">
                    <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-2">${escapeHtml(album.nome)}</h3>
                <p class="text-slate-600 mb-4 text-sm line-clamp-2">${escapeHtml(album.descricao || 'Sem descrição')}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">${formatDate(album.data)}</span>
                    <div class="flex items-center text-blue-600">
                        <span class="text-sm font-medium">Ver álbum</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        `;

        albumCard.classList.add('fade-in');
        grid.appendChild(albumCard);
    });
}

// Load photos for gallery page
async function loadPhotos(albumId, page = 1) {
    currentAlbumId = albumId;
    currentPage = page;

    const loading = document.getElementById('photos-loading');
    const grid = document.getElementById('photos-grid');
    const empty = document.getElementById('photos-empty');
    const pagination = document.getElementById('pagination-container');
    const totalPhotosElement = document.getElementById('total-photos');
    const currentPageElement = document.getElementById('current-page');

    if (loading) loading.classList.remove('hidden');
    if (grid) grid.classList.add('hidden');
    if (empty) empty.classList.add('hidden');
    if (pagination) pagination.classList.add('hidden');

    try {
        const response = await fetch(`api/listar-fotos.php?album_id=${albumId}&page=${page}&per_page=${photosPerPage}`);
        const data = await response.json();

        if (loading) loading.classList.add('hidden');

        if (data.success && data.fotos.length > 0) {
            allPhotos = data.all_photos || [];
            currentPhotos = data.fotos;
            totalPages = data.total_pages;

            displayPhotos(data.fotos);
            setupPagination();
            
            if (totalPhotosElement) totalPhotosElement.textContent = data.total_photos;
            if (currentPageElement) currentPageElement.textContent = page;
            
            if (grid) grid.classList.remove('hidden');
            if (totalPages > 1 && pagination) {
                pagination.classList.remove('hidden');
            }
        } else {
            if (empty) empty.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Erro ao carregar fotos:', error);
        if (loading) loading.classList.add('hidden');
        if (empty) empty.classList.remove('hidden');
    }
}

// Display photos in grid
function displayPhotos(photos) {
    const grid = document.getElementById('photos-grid');
    grid.innerHTML = '';

    photos.forEach((photo, index) => {
        const photoItem = document.createElement('div');
        photoItem.className = 'photo-item bg-white rounded-lg shadow-md overflow-hidden cursor-pointer';
        
        const globalIndex = ((currentPage - 1) * photosPerPage) + index;
        photoItem.onclick = () => openPhotoModal(globalIndex);

        photoItem.innerHTML = `
            <div class="aspect-square overflow-hidden">
                <img src="${escapeHtml(photo.caminho)}" alt="${escapeHtml(photo.nome_original)}" 
                     class="w-full h-full object-cover transition-transform duration-300"
                     loading="lazy">
            </div>
            <div class="p-3">
                <p class="text-sm font-medium text-slate-700 truncate">${escapeHtml(photo.nome_original)}</p>
                <p class="text-xs text-slate-500">${formatFileSize(photo.tamanho)}</p>
            </div>
        `;

        photoItem.classList.add('fade-in');
        grid.appendChild(photoItem);
    });
}

// Setup pagination
function setupPagination() {
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const pageNumbers = document.getElementById('page-numbers');

    // Check if pagination elements exist
    if (!prevBtn || !nextBtn || !pageNumbers) {
        return; // Exit if pagination elements don't exist
    }

    // Update navigation buttons
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;

    prevBtn.onclick = () => {
        if (currentPage > 1) {
            loadPhotos(currentAlbumId, currentPage - 1);
        }
    };

    nextBtn.onclick = () => {
        if (currentPage < totalPages) {
            loadPhotos(currentAlbumId, currentPage + 1);
        }
    };

    // Generate page numbers
    pageNumbers.innerHTML = '';
    const maxVisiblePages = 7;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = i === currentPage 
            ? 'px-3 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg'
            : 'px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg';
        pageBtn.textContent = i;
        pageBtn.onclick = () => {
            if (i !== currentPage) {
                loadPhotos(currentAlbumId, i);
            }
        };
        pageNumbers.appendChild(pageBtn);
    }
}

// Utility functions
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

function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
}