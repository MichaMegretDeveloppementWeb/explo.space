/**
 * Module de synchronisation carte ↔ liste
 *
 * Responsabilités :
 * - Survol liste → highlight marqueur correspondant
 * - Clic liste → centrer carte sur marqueur
 * - Clic marqueur → scroll + highlight liste (géré dans map-markers.js)
 */

import L from 'leaflet';

export class PlaceMapSync {
    /**
     * @param {PlaceMap} mapInstance - Instance de PlaceMap
     * @param {PlaceMapMarkers} markersInstance - Instance de PlaceMapMarkers
     */
    constructor(mapInstance, markersInstance) {
        this.mapInstance = mapInstance;
        this.markersInstance = markersInstance;
        this.map = mapInstance.getMap();
        this.listContainer = null;
        this.highlightedMarker = null;
        this.originalIcon = null;
        this.highlightedCluster = null;
    }

    /**
     * Initialise la synchronisation avec le conteneur de liste
     *
     * @param {string} listContainerId - ID du conteneur de la liste
     */
    init(listContainerId) {
        this.listContainer = document.getElementById(listContainerId);

        if (!this.listContainer) {
            console.error(`List container not found: ${listContainerId}`);
            return;
        }

        this.setupListeners();
    }

    /**
     * Configure les événements de synchronisation
     */
    setupListeners() {
        // Délégation d'événements sur le conteneur parent (pour gérer Livewire updates)
        this.listContainer.addEventListener('mouseenter', (e) => {
            // Trouver l'élément de lieu le plus proche
            const placeItem = e.target.closest('[data-place-id]');
            if (placeItem) {
                this.onListItemHover(placeItem);
            }
        }, true); // true = capture phase pour traverser les éléments enfants

        // Reset highlight quand on quitte complètement le conteneur de liste
        this.listContainer.addEventListener('mouseleave', () => {
            this.resetHighlight();
        });

        this.listContainer.addEventListener('click', (e) => {
            // Vérifier si c'est un clic sur un bouton "centrer sur la carte"
            const centerButton = e.target.closest('[data-center-map]');
            if (centerButton) {
                const placeId = parseInt(centerButton.dataset.centerMap, 10);
                this.centerMapOnPlace(placeId);
                e.preventDefault();
                return;
            }

            // Sinon, clic sur l'élément entier
            const placeItem = e.target.closest('[data-place-id]');
            if (placeItem && !e.target.closest('a')) {
                // Ne pas intercepter les clics sur les liens
                const placeId = parseInt(placeItem.dataset.placeId, 10);
                this.centerMapOnPlace(placeId);
            }
        });

        // Écouter les événements Livewire de mise à jour de la liste
        const registerLivewireListener = () => {
            Livewire.on('places-updated', () => {
                this.resetHighlight();
            });
        };

        // Gérer les deux cas : Livewire déjà initialisé OU pas encore initialisé
        if (window.Livewire) {
            registerLivewireListener();
        } else {
            document.addEventListener('livewire:init', registerLivewireListener);
        }
    }

    /**
     * Gère le survol d'un élément de la liste
     *
     * @param {HTMLElement} placeItem
     */
    onListItemHover(placeItem) {
        const placeId = parseInt(placeItem.dataset.placeId, 10);

        if (!placeId) {
            return;
        }

        // Obtenir le parent visible (marqueur ou cluster)
        const visibleParent = this.markersInstance.getVisibleParent(placeId);

        if (visibleParent) {
            // Vérifier si c'est un cluster ou un marqueur individuel
            if (this.markersInstance.isCluster(visibleParent)) {
                this.highlightCluster(visibleParent);
            } else {
                this.highlightMarker(visibleParent);
            }
        }
    }

    /**
     * Met en évidence un marqueur
     *
     * @param {L.Marker} marker
     */
    highlightMarker(marker) {
        // Réinitialiser le highlight précédent
        this.resetHighlight();

        // Sauvegarder l'icône originale
        this.originalIcon = marker.getIcon();
        this.highlightedMarker = marker;

        // Créer une icône agrandie pour le highlight
        const highlightIcon = L.icon({
            iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
            iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [32, 52], // Plus grand que l'original (25, 41)
            iconAnchor: [16, 52],
            popupAnchor: [1, -44],
            shadowSize: [52, 52],
        });

        marker.setIcon(highlightIcon);

        // Faire "rebondir" légèrement le marqueur avec setZIndexOffset
        marker.setZIndexOffset(1000);

        // Ajouter une surbrillance visuelle (outline + shadow)
        const markerIcon = marker._icon;
        if (markerIcon) {
            markerIcon.classList.add('marker-highlighted');
            markerIcon.style.filter = 'drop-shadow(0 0 10px rgba(59, 130, 246, 1))';
        }
    }

    /**
     * Met en évidence un cluster
     *
     * @param {L.MarkerCluster} cluster
     */
    highlightCluster(cluster) {
        // Réinitialiser le highlight précédent
        this.resetHighlight();

        // Sauvegarder la référence au cluster
        this.highlightedCluster = cluster;

        // Accéder à l'élément DOM du cluster
        const clusterIcon = cluster._icon;

        if (clusterIcon) {
            // Ajouter une classe CSS pour le highlight (pulsation + shadow via CSS)
            clusterIcon.classList.add('cluster-highlighted');

            // Ajouter un outline pour renforcer le highlight visuel
            clusterIcon.style.outline = '3px solid rgba(59, 130, 246, 0.6)';
            clusterIcon.style.outlineOffset = '2px';
            clusterIcon.style.zIndex = '1000';
        }
    }

    /**
     * Réinitialise le highlight (marqueur ou cluster)
     */
    resetHighlight() {
        // Réinitialiser le marqueur si présent
        if (this.highlightedMarker && this.originalIcon) {
            this.highlightedMarker.setIcon(this.originalIcon);
            this.highlightedMarker.setZIndexOffset(0);

            // Nettoyer les effets visuels
            const markerIcon = this.highlightedMarker._icon;
            if (markerIcon) {
                markerIcon.classList.remove('marker-highlighted');
                markerIcon.style.filter = '';
            }

            this.highlightedMarker = null;
            this.originalIcon = null;
        }

        // Réinitialiser le cluster si présent
        if (this.highlightedCluster) {
            const clusterIcon = this.highlightedCluster._icon;

            if (clusterIcon) {
                // Retirer les effets de highlight
                clusterIcon.classList.remove('cluster-highlighted');
                clusterIcon.style.outline = '';
                clusterIcon.style.outlineOffset = '';
                clusterIcon.style.zIndex = '';
            }

            this.highlightedCluster = null;
        }
    }

    /**
     * Centre la carte sur un lieu spécifique
     *
     * @param {number} placeId
     */
    centerMapOnPlace(placeId) {
        // Utiliser la méthode focusOnMarker du module markers
        this.markersInstance.focusOnMarker(placeId, 10); // Zoom à 15

        // Émettre un événement Livewire pour mettre à jour l'état
        if (window.Livewire) {
            window.Livewire.dispatch('map-centered-on-place', { placeId });
        }
    }

    /**
     * Nettoie les listeners et réinitialise l'état
     */
    destroy() {
        this.resetHighlight();
        this.listContainer = null;
    }

    /**
     * Réinitialise la synchronisation avec un nouveau conteneur
     *
     * @param {string} listContainerId
     */
    reinit(listContainerId) {
        this.destroy();
        this.init(listContainerId);
    }
}
