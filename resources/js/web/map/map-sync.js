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

        // Vérifier si c'est un lieu emblématique (via placeData stocké)
        const isFeatured = marker.placeData && marker.placeData.is_featured;

        let highlightIcon;

        if (isFeatured) {
            // Créer une version agrandie de l'icône featured
            const svg = `
                <svg width="32" height="52" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                    <!-- Pin violet -->
                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                          fill="#9333ea"
                          stroke="#ffffff00"
                          stroke-width="1.5"
                          filter="url(#marker-shadow-featured)"/>

                    <!-- Cercle blanc au centre pour contraste -->
                    <circle cx="12.5" cy="12.5" r="7" fill="white"/>

                    <!-- Étoile violette au centre -->
                    <path d="M12.5 7.5 L13.8 11.2 L17.8 11.2 L14.5 13.5 L15.8 17.2 L12.5 14.9 L9.2 17.2 L10.5 13.5 L7.2 11.2 L11.2 11.2 Z"
                          fill="#9333ea"
                          stroke="#6b21a8"
                          stroke-width="0.5"/>
                </svg>
            `;

            highlightIcon = L.divIcon({
                html: svg,
                className: 'featured-marker-icon highlighted',
                iconSize: [32, 52],
                iconAnchor: [16, 52],
                popupAnchor: [0, -47],
            });
        } else {
            // Créer une version agrandie de l'icône normale (bleue)
            const svg = `
                <svg width="32" height="52" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                    <!-- Pin bleu -->
                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                          fill="#3b82f6"
                          stroke="#ffffff00"
                          stroke-width="1.5"
                          filter="url(#marker-shadow-normal)"/>

                    <!-- Point blanc au centre -->
                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                </svg>
            `;

            highlightIcon = L.divIcon({
                html: svg,
                className: 'normal-marker-icon highlighted',
                iconSize: [32, 52],
                iconAnchor: [16, 52],
                popupAnchor: [0, -47],
            });
        }

        marker.setIcon(highlightIcon);

        // Faire "rebondir" légèrement le marqueur avec setZIndexOffset
        marker.setZIndexOffset(1000);

        // Ajouter une surbrillance visuelle subtile
        const markerIcon = marker._icon;
        if (markerIcon) {
            markerIcon.classList.add('marker-highlighted');
            // Utiliser une ombre portée plus nette et définie (moins de flou)
            const shadowColor = isFeatured ? 'rgba(147, 51, 234, 0.6)' : 'rgba(59, 130, 246, 0.6)';
            markerIcon.style.filter = `drop-shadow(0 3px 6px ${shadowColor})`;
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
            // Ajouter une classe CSS pour l'animation de halo pulsant
            clusterIcon.classList.add('cluster-highlighted');
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
