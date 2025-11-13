import { getMinZoom } from '../../../../shared/map-responsive-config.js';

// Récupérer la config depuis PHP (injectée par PlaceSearchConfig::getJsConfig())
const phpMapConfig = window.PlaceSearchConfig?.map || {};

export const MAP_CONFIG = {
    /**
     * Configuration des tuiles de carte
     */
    tileLayer: {
        primary: {
            url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
            attribution: '© OpenStreetMap contributors © CARTO',
            subdomains: ['a', 'b', 'c', 'd'],
            maxZoom: 19,
            // minZoom responsive : 1 sur mobile (< 800px), 3 sur desktop
            get minZoom() { return getMinZoom(); },
        },
        fallback: {
            url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            subdomains: ['a', 'b', 'c', 'd'],
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            // minZoom responsive : 1 sur mobile (< 800px), 3 sur desktop
            get minZoom() { return getMinZoom(); },
        },
    },

    /**
     * Configuration du clustering (Leaflet.markercluster)
     */
    clustering: {
        maxClusterRadius: 110,
        disableClusteringAtZoom: 18,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        iconCreateFunction: function(cluster) {
            const count = cluster.getChildCount();
            let size = 'small';

            if (count > 100) {
                size = 'large';
            } else if (count > 10) {
                size = 'medium';
            }

            return L.divIcon({
                html: `<div><span style="color: white;">${count}</span></div>`,
                className: `marker-cluster marker-cluster-${size}`,
                iconSize: L.point(40, 40),
            });
        },
    },

    /**
     * Configuration des marqueurs
     */
    markers: {
        useDefaultLeafletIcons: true,
    },

    /**
     * Configuration de performance
     */
    performance: {
        maxMarkersBeforeClustering: 2,
        boundsUpdateDebounce: 100, // Augmenté de 300ms à 800ms pour éviter trop de requêtes
        animationDuration: 250,
    },

    /**
     * Configuration initiale de la carte
     */
    defaultView: {
        center: [48.8566, 2.3522], // Paris par défaut
        zoom: 6,
    },

    /**
     * Limites de la carte (monde entier)
     * Synchronisées depuis config/map.php pour cohérence avec validation backend
     */
    worldBounds: phpMapConfig.boundingBox || {
        // Fallback si window.PlaceSearchConfig non disponible
        north: 85,
        south: -85,
        east: 180,
        west: -180,
    },
};
