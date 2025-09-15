// El Terreno - Clean JavaScript

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initMobileMenu();
    initStickyNavbar();
    initActiveSection();
    initScrollAnimations();
    initInstagramFeed();
    initSutilScrollAnimations();
});

// Novo Mobile Menu Functionality
function initMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileClose = document.querySelector('.mobile-close');
    const mobileLinks = document.querySelectorAll('.mobile-nav-link');
    const body = document.body;

    if (!mobileToggle || !mobileMenu || !mobileClose) {
        console.warn('Mobile menu elements not found');
        return;
    }

    // Open mobile menu
    function openMobileMenu() {
        mobileMenu.classList.add('active');
        mobileToggle.classList.add('active');
        body.style.overflow = 'hidden';
        
        // Reset animation delays
        mobileLinks.forEach((link, index) => {
            link.style.transitionDelay = `${(index + 1) * 0.1}s`;
        });
    }

    // Close mobile menu
    function closeMobileMenu() {
        mobileMenu.classList.remove('active');
        mobileToggle.classList.remove('active');
        body.style.overflow = '';
        
        // Reset transition delays
        mobileLinks.forEach(link => {
            link.style.transitionDelay = '0s';
        });
    }

    // Toggle menu
    mobileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });

    // Close button
    mobileClose.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeMobileMenu();
    });

    // Close on overlay click
    mobileMenu.addEventListener('click', function(e) {
        if (e.target === mobileMenu) {
            closeMobileMenu();
        }
    });

    // Close on link click
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(closeMobileMenu, 300);
        });
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Prevent scroll when menu is open
    mobileMenu.addEventListener('touchmove', function(e) {
        if (e.target === mobileMenu) {
            e.preventDefault();
        }
    }, { passive: false });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });
}

// Sticky Navbar with Enhanced Animation
function initStickyNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    
    let ticking = false;
    let isTransitioning = false;
    
    function updateNavbar() {
        const scrollY = window.scrollY;
        const scrollThreshold = 50;
        
        if (scrollY > scrollThreshold) {
            if (!navbar.classList.contains('scrolled') && !isTransitioning) {
                isTransitioning = true;
                
                // Primeira fase: aplicar transição com redução de opacidade
                navbar.classList.add('transitioning');
                
                // Segunda fase: após a transição, aplicar o estado final
                setTimeout(() => {
                    navbar.classList.remove('transitioning');
                    navbar.classList.add('scrolled');
                    isTransitioning = false;
                }, 300);
            }
        } else {
            if (navbar.classList.contains('scrolled')) {
                navbar.classList.remove('scrolled');
                navbar.classList.remove('transitioning');
                isTransitioning = false;
            }
        }
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    });
}

// Removed smooth scrolling functionality

// Active Section Indicator
function initActiveSection() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    if (sections.length === 0 || navLinks.length === 0) return;
    
    window.addEventListener('scroll', function() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.offsetHeight;
            
            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
}

// Carousel functionality is now handled by banner.js using Bootstrap Carousel

// Scroll Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.card, .postcard, section');
    animateElements.forEach(el => {
        observer.observe(el);
    });
}

// Instagram Feed Integration
function initInstagramFeed() {
    const instagramGrid = document.querySelector('.instagram-grid');
    if (!instagramGrid) return;
    
    // Placeholder posts for demonstration
    const placeholderPosts = [
        {
            id: '1',
            media_url: 'https://via.placeholder.com/300x300/6d5db2/ffffff?text=Post+1',
            caption: 'Festa incrível no El Terreno! 🎉',
            permalink: '#'
        },
        {
            id: '2',
            media_url: 'https://via.placeholder.com/300x300/ff248e/ffffff?text=Post+2',
            caption: 'Música boa e energia única! 🎵',
            permalink: '#'
        },
        {
            id: '3',
            media_url: 'https://via.placeholder.com/300x300/79dd09/000000?text=Post+3',
            caption: 'Noite inesquecível com os amigos! ✨',
            permalink: '#'
        },
        {
            id: '4',
            media_url: 'https://via.placeholder.com/300x300/6d5db2/ffffff?text=Post+4',
            caption: 'El Terreno sempre surpreendendo! 🔥',
            permalink: '#'
        }
    ];
    
    // Clear existing content
    instagramGrid.innerHTML = '';
    
    // Create posts
    placeholderPosts.forEach(post => {
        const postElement = document.createElement('div');
        postElement.className = 'instagram-post';
        postElement.innerHTML = `
            <img src="${post.media_url}" alt="${post.caption}" loading="lazy">
        `;
        
        postElement.addEventListener('click', function() {
            if (post.permalink !== '#') {
                window.open(post.permalink, '_blank');
            }
        });
        
        instagramGrid.appendChild(postElement);
    });
    
    // Add loading state
    instagramGrid.classList.add('loaded');
}

// Real Instagram Integration Function (for future use)
function loadRealInstagramFeed() {
    // This function would integrate with Instagram Basic Display API
    // You'll need to:
    // 1. Register your app at developers.facebook.com
    // 2. Get Instagram Basic Display API access
    // 3. Obtain user access token
    // 4. Make API calls to fetch posts
    
    const ACCESS_TOKEN = 'YOUR_INSTAGRAM_ACCESS_TOKEN';
    const API_URL = `https://graph.instagram.com/me/media?fields=id,caption,media_url,permalink&access_token=${ACCESS_TOKEN}`;
    
    fetch(API_URL)
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                displayInstagramPosts(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading Instagram feed:', error);
            // Fallback to placeholder posts
            initInstagramFeed();
        });
}

function displayInstagramPosts(posts) {
    const instagramGrid = document.querySelector('.instagram-grid');
    if (!instagramGrid) return;
    
    instagramGrid.innerHTML = '';
    
    posts.slice(0, 6).forEach(post => {
        const postElement = document.createElement('div');
        postElement.className = 'instagram-post';
        postElement.innerHTML = `
            <img src="${post.media_url}" alt="${post.caption || 'Instagram post'}" loading="lazy">
        `;
        
        postElement.addEventListener('click', function() {
            window.open(post.permalink, '_blank');
        });
        
        instagramGrid.appendChild(postElement);
    });
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Performance optimizations
window.addEventListener('scroll', debounce(function() {
    // Throttled scroll events
}, 16)); // ~60fps

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
});

// Resize handler
window.addEventListener('resize', debounce(function() {
    // Handle responsive changes
    const navbar = document.querySelector('.navbar');
    const navbarNav = document.querySelector('.navbar-nav');
    
    if (window.innerWidth > 767 && navbarNav) {
        navbarNav.classList.remove('show');
    }
}, 250));

// Instructions for Instagram Integration
// Animações Sutis de Scroll - Sistema Otimizado
function initSutilScrollAnimations() {
    // Verifica se o dispositivo suporta animações suaves
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;
    
    // Intersection Observer para performance otimizada
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Adiciona delay escalonado para múltiplos elementos
                const delay = entry.target.dataset.delay || 0;
                
                setTimeout(() => {
                    entry.target.classList.add('animate-in');
                    
                    // Remove will-change após animação para economizar recursos
                    setTimeout(() => {
                        entry.target.style.willChange = 'auto';
                    }, 800);
                }, delay);
                
                // Para de observar o elemento após animação
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Seleciona elementos para animação
    const animateElements = document.querySelectorAll(
        '.gallery-card, .gallery-title, .card, section, .photo-gallery-section h2, .photo-gallery-section p'
    );
    
    // Adiciona classes de animação e observa elementos
    animateElements.forEach((element, index) => {
        // Adiciona classes baseadas na posição
        if (element.classList.contains('gallery-card')) {
            element.classList.add('scroll-animate', 'fade-up');
            element.dataset.delay = index * 100; // Delay escalonado
        } else if (element.classList.contains('gallery-title')) {
            element.classList.add('scroll-animate', 'fade-up');
        } else if (element.classList.contains('card')) {
            element.classList.add('scroll-animate', 'scale-in');
            element.dataset.delay = index * 80;
        } else {
            element.classList.add('scroll-animate', 'fade-up');
        }
        
        observer.observe(element);
    });
    
    // Animação especial para elementos específicos
    const specialElements = document.querySelectorAll('.gallery-description, .btn');
    specialElements.forEach((element, index) => {
        element.classList.add('scroll-animate', 'fade-up');
        element.dataset.delay = index * 150;
        observer.observe(element);
    });
}

// Sistema de throttle para scroll events (otimização de performance)
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Parallax sutil para elementos específicos (opcional)
function initSubtleParallax() {
    const parallaxElements = document.querySelectorAll('.carousel-item');
    
    const handleParallax = throttle(() => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.3;
        
        parallaxElements.forEach(element => {
            element.style.transform = `translateY(${rate}px)`;
        });
    }, 16); // ~60fps
    
    window.addEventListener('scroll', handleParallax);
}

console.log(`
🔗 COMO CONECTAR O INSTAGRAM REAL:

1. Acesse: https://developers.facebook.com/
2. Crie um novo app
3. Adicione o produto "Instagram Basic Display"
4. Configure as URLs de redirecionamento
5. Obtenha o Access Token do usuário
6. Substitua 'YOUR_INSTAGRAM_ACCESS_TOKEN' no código
7. Chame loadRealInstagramFeed() ao invés de initInstagramFeed()

📚 Documentação: https://developers.facebook.com/docs/instagram-basic-display-api/
`);

// Inicializa parallax sutil (descomente se desejar)
// document.addEventListener('DOMContentLoaded', initSubtleParallax);