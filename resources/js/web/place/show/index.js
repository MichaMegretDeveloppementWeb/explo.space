/**
 * Point d'entrée pour la page de détail de lieu (web)
 * Charge les modules carrousel photos et carte Leaflet
 */

import { initPhotoGallery } from './photo-gallery.js';
import { initPlaceMap } from './place-map.js';

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser la galerie photos si présente
    const galleryModal = document.getElementById('photoGalleryModal');
    if (galleryModal) {
        initPhotoGallery();
    }

    // Initialiser la carte Leaflet si présente
    const mapContainer = document.getElementById('placeMap');
    if (mapContainer) {
        initPlaceMap();
    }
});
