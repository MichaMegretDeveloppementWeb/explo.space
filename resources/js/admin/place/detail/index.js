import { LocationPreview } from './location-preview.js';

/**
 * Point d'entrée Vite pour la page de détail des lieux
 *
 * Initialise la carte de prévisualisation si le conteneur existe et
 * contient des coordonnées valides.
 */
document.addEventListener('DOMContentLoaded', () => {

    const mapContainer = document.getElementById('place-location-preview');

    if (mapContainer) {
        const lat = parseFloat(mapContainer.dataset.latitude);
        const lng = parseFloat(mapContainer.dataset.longitude);

        // Vérifier que les coordonnées sont valides
        if (!isNaN(lat) && !isNaN(lng)) {
            new LocationPreview('place-location-preview', lat, lng);
        } else {
            console.warn('Coordonnées GPS invalides pour la prévisualisation de carte');
        }
    }
});
