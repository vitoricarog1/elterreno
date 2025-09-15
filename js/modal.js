// Photo modal functionality
let currentPhotoIndex = 0;
let modalPhotos = [];

function openPhotoModal(photoIndex) {
    currentPhotoIndex = photoIndex;
    modalPhotos = allPhotos;
    
    const modal = document.getElementById('photo-modal');
    const modalImage = document.getElementById('modal-image');
    const modalDownload = document.getElementById('modal-download');
    
    if (!modalPhotos || modalPhotos.length === 0) return;
    
    const photo = modalPhotos[currentPhotoIndex];
    if (modalImage) {
        modalImage.src = photo.caminho;
        modalImage.alt = photo.nome_original;
    }
    if (modalDownload) {
        modalDownload.href = photo.caminho;
        modalDownload.download = photo.nome_original;
    }
    
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('show');
    }
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    const modal = document.getElementById('photo-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('show');
    }
    document.body.style.overflow = 'auto';
}

function navigateModal(direction) {
    if (!modalPhotos || modalPhotos.length === 0) return;
    
    if (direction === 'next') {
        currentPhotoIndex = (currentPhotoIndex + 1) % modalPhotos.length;
    } else if (direction === 'prev') {
        currentPhotoIndex = (currentPhotoIndex - 1 + modalPhotos.length) % modalPhotos.length;
    }
    
    const photo = modalPhotos[currentPhotoIndex];
    const modalImage = document.getElementById('modal-image');
    const modalDownload = document.getElementById('modal-download');
    
    if (modalImage) {
        modalImage.src = photo.caminho;
        modalImage.alt = photo.nome_original;
    }
    if (modalDownload) {
        modalDownload.href = photo.caminho;
        modalDownload.download = photo.nome_original;
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('photo-modal');
    
    // Fechar modal ao clicar fora da imagem
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePhotoModal();
            }
        });
    }
    
    // Fechar com ESC e navegar com setas
    document.addEventListener('keydown', function(e) {
        if (modal && !modal.classList.contains('hidden')) {
            switch(e.key) {
                case 'Escape':
                    closePhotoModal();
                    break;
                case 'ArrowLeft':
                    navigateModal('prev');
                    break;
                case 'ArrowRight':
                    navigateModal('next');
                    break;
            }
        }
    });
});
