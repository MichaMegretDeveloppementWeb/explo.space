// import { LocationPreviewDouble } from './location-preview.js';

/**
 * Point d'entrée Vite pour la page de détail des demandes de modification/signalement
 *
 * TODO Phase 6: Initialiser la carte avec double marker (position actuelle + position proposée)
 * pour les EditRequests qui contiennent des modifications de coordonnées GPS.
 */
document.addEventListener('DOMContentLoaded', () => {

    const mapContainer = document.getElementById('location-preview-map');

    if (mapContainer) {
        // Récupérer les coordonnées originales et proposées
        const originalLat = parseFloat(mapContainer.dataset.originalLat);
        const originalLng = parseFloat(mapContainer.dataset.originalLng);
        const proposedLat = parseFloat(mapContainer.dataset.proposedLat);
        const proposedLng = parseFloat(mapContainer.dataset.proposedLng);

        // Vérifier que toutes les coordonnées sont valides
        if (!isNaN(originalLat) && !isNaN(originalLng) && !isNaN(proposedLat) && !isNaN(proposedLng)) {
            // TODO Phase 6: Implémenter LocationPreviewDouble
            // new LocationPreviewDouble('location-preview-map', {
            //     original: { lat: originalLat, lng: originalLng },
            //     proposed: { lat: proposedLat, lng: proposedLng }
            // });

            console.info('Double marker GPS sera implémenté en Phase 6');
        } else {
            console.warn('Coordonnées GPS invalides pour la prévisualisation de carte');
        }
    }
});
