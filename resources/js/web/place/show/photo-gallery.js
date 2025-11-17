/**
 * Galerie photos style Apple
 * Fullscreen, transitions fluides avec translation, navigation clavier + tactile
 */

let currentPhotoIndex = 0;
let photos = [];
let touchStartX = 0;
let touchEndX = 0;

/**
 * Initialiser la galerie photos
 */
export function initPhotoGallery() {
    const modal = document.getElementById('photoGalleryModal');
    const photosData = modal?.dataset.photos;

    if (!photosData) {
        console.warn('No photos data found');
        return;
    }

    try {
        photos = JSON.parse(photosData);
    } catch (e) {
        console.error('Failed to parse photos data:', e);
        return;
    }

    // Écouter les touches clavier
    document.addEventListener('keydown', handleKeyPress);

    // Écouter les événements tactiles sur la modal complète (pas juste l'image)
    if (modal) {
        modal.addEventListener('touchstart', handleTouchStart, { passive: true });
        modal.addEventListener('touchend', handleTouchEnd, { passive: true });
    }

    // Exposer les fonctions globalement pour les onclick dans Blade
    window.openPhotoGallery = openPhotoGallery;
    window.closePhotoGallery = closePhotoGallery;
    window.navigatePhoto = navigatePhoto;
}

/**
 * Ouvrir la galerie à un index spécifique
 */
function openPhotoGallery(index) {
    currentPhotoIndex = index;
    const modal = document.getElementById('photoGalleryModal');

    // Afficher la modal
    modal.classList.remove('hidden');

    // Empêcher le scroll du body
    document.body.style.overflow = 'hidden';

    // Afficher la photo (sans transition pour l'ouverture)
    updatePhoto(false);

    // Animation d'entrée (fade in)
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
}

/**
 * Fermer la galerie
 */
function closePhotoGallery() {
    const modal = document.getElementById('photoGalleryModal');

    // Animation de sortie (fade out)
    modal.style.opacity = '0';

    setTimeout(() => {
        modal.classList.add('hidden');
        // Rétablir le scroll du body
        document.body.style.overflow = '';
    }, 300);
}

/**
 * Naviguer entre les photos
 */
function navigatePhoto(direction) {
    if (direction === 'next') {
        currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
    } else if (direction === 'prev') {
        currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
    }

    updatePhoto(true, direction);
}

/**
 * Mettre à jour l'affichage de la photo avec effet de translation
 */
function updatePhoto(withTransition = true, direction = null) {
    const photo = photos[currentPhotoIndex];
    const modalPhoto = document.getElementById('modalPhoto');
    const photoCounter = document.getElementById('photoCounter');

    if (!photo || !modalPhoto) {
        return;
    }

    if (withTransition && direction) {
        // Effet de translation : sortie
        const translateOut = direction === 'next' ? '-translate-x-20' : 'translate-x-20';
        modalPhoto.style.transform = translateOut;
        modalPhoto.style.opacity = '0';
        modalPhoto.style.transition = 'transform 0.3s ease, opacity 0.3s ease';

        setTimeout(() => {
            // Changer l'image avec alt_text traduit ou fallback
            modalPhoto.src = photo.url;
            modalPhoto.alt = photo.alt_text || `Image ${currentPhotoIndex + 1}`;

            // Positionner l'image du côté opposé
            const translateIn = direction === 'next' ? 'translate-x-20' : '-translate-x-20';
            modalPhoto.style.transform = translateIn;
            modalPhoto.style.opacity = '0';

            // Forcer le reflow
            void modalPhoto.offsetWidth;

            // Effet de translation : entrée
            setTimeout(() => {
                modalPhoto.style.transform = 'translate-x-0';
                modalPhoto.style.opacity = '1';
            }, 10);
        }, 300);
    } else {
        // Pas de transition (ouverture initiale) avec alt_text traduit ou fallback
        modalPhoto.src = photo.url;
        modalPhoto.alt = photo.alt_text || `Image ${currentPhotoIndex + 1}`;
        modalPhoto.style.transform = 'translate-x-0';
        modalPhoto.style.opacity = '1';
        modalPhoto.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
    }

    // Mettre à jour le compteur
    if (photoCounter) {
        photoCounter.textContent = `${currentPhotoIndex + 1} / ${photos.length}`;
    }

    // Gérer la visibilité des boutons de navigation
    const prevBtn = document.getElementById('prevPhoto');
    const nextBtn = document.getElementById('nextPhoto');

    if (prevBtn && nextBtn) {
        // Toujours afficher les boutons (navigation circulaire)
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }
}

/**
 * Gérer les touches clavier
 */
function handleKeyPress(event) {
    const modal = document.getElementById('photoGalleryModal');

    // Vérifier si la modal est ouverte
    if (modal?.classList.contains('hidden')) {
        return;
    }

    switch (event.key) {
        case 'Escape':
            closePhotoGallery();
            break;
        case 'ArrowLeft':
            navigatePhoto('prev');
            break;
        case 'ArrowRight':
            navigatePhoto('next');
            break;
    }
}

/**
 * Gérer le début du toucher
 */
function handleTouchStart(event) {
    // Ne capturer que les touches sur la zone principale (pas sur les boutons)
    if (event.target.closest('button')) {
        return;
    }
    touchStartX = event.changedTouches[0].screenX;
}

/**
 * Gérer la fin du toucher (swipe)
 */
function handleTouchEnd(event) {
    // Ne capturer que les touches sur la zone principale (pas sur les boutons)
    if (event.target.closest('button')) {
        return;
    }
    touchEndX = event.changedTouches[0].screenX;
    handleSwipe();
}

/**
 * Détecter la direction du swipe
 */
function handleSwipe() {
    const swipeThreshold = 50; // Distance minimale pour un swipe valide (en pixels)
    const diff = touchStartX - touchEndX;

    if (Math.abs(diff) < swipeThreshold) {
        return; // Mouvement trop faible, on ignore
    }

    if (diff > 0) {
        // Swipe vers la gauche → photo suivante
        navigatePhoto('next');
    } else {
        // Swipe vers la droite → photo précédente
        navigatePhoto('prev');
    }
}
