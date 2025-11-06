import L from 'leaflet';

/**
 * LocationPreview - Carte statique de prévisualisation pour les lieux
 *
 * Affiche une carte Leaflet en lecture seule avec un marqueur fixe.
 * Contrairement à LocationMap (interactif), cette carte désactive toutes les interactions
 * utilisateur sauf les contrôles de zoom basiques.
 */
export class LocationPreview {
    /**
     * @param {string} containerId - ID du conteneur HTML
     * @param {number} latitude - Latitude du lieu
     * @param {number} longitude - Longitude du lieu
     */
    constructor(containerId, latitude, longitude) {
        this.containerId = containerId;
        this.latitude = latitude;
        this.longitude = longitude;
        this.map = null;
        this.marker = null;

        this.initMap();
    }

    /**
     * Initialiser la carte statique
     */
    initMap() {
        try {
            // Créer la carte avec interactions désactivées
            this.map = L.map(this.containerId, {
                center: [this.latitude, this.longitude],
                zoom: 8,
                dragging: true,
                touchZoom: true,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                boxZoom: true,
                keyboard: true,
                zoomControl: true,
                maxBounds: [[-90, -180], [90, 180]],
                maxBoundsViscosity: 1.0,
            });

            const primaryLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: ['a', 'b', 'c', 'd'],
                maxZoom: 19,
                minZoom: 1,
            });

            primaryLayer.on('tileerror', () => {
                console.warn('CartoDB tiles failed, switching to OSM fallback');
                this.map.removeLayer(primaryLayer);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: ['a', 'b', 'c', 'd'],
                    maxZoom: 19,
                    minZoom: 1,
                }).addTo(this.map);
            });

            primaryLayer.addTo(this.map);

            // Ajouter un marqueur fixe (non draggable)
            this.marker = L.marker([this.latitude, this.longitude], {
                draggable: false,
            }).addTo(this.map);

        } catch (error) {
            console.error('✗ Erreur lors de l\'initialisation de LocationPreview:', error);
        }
    }

    /**
     * Détruire la carte (cleanup)
     */
    destroy() {
        if (this.map) {
            this.map.remove();
            this.map = null;
            this.marker = null;
        }
    }
}
