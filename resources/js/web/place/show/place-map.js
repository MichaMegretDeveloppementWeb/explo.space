/**
 * Carte Leaflet pour la page de détail de lieu
 * Similaire à la carte admin mais pour affichage public
 */

import L from 'leaflet';
import { createNormalMarkerIcon } from '../../map/marker-icon-utils.js';

let map = null;

/**
 * Initialiser la carte Leaflet
 */
export function initPlaceMap() {
    const container = document.getElementById('placeMap');

    if (!container) {
        console.warn('Map container not found');
        return;
    }

    // Récupérer les données depuis les attributs data
    const latitude = parseFloat(container.dataset.latitude);
    const longitude = parseFloat(container.dataset.longitude);
    const title = container.dataset.title;

    if (isNaN(latitude) || isNaN(longitude)) {
        console.error('Invalid coordinates');
        return;
    }

    // Créer la carte centrée sur le lieu
    map = L.map('placeMap', {
        center: [latitude, longitude],
        zoom: 10,
        zoomControl: true,
        scrollWheelZoom: true,
        dragging: true,
    });

    // Ajouter les tiles CartoDB Positron avec fallback OSM
    addTileLayer();

    // Ajouter un marqueur pour le lieu avec icône custom
    const marker = L.marker([latitude, longitude], {
        icon: createNormalMarkerIcon()
    }).addTo(map);

}

/**
 * Ajouter les tiles avec fallback
 */
function addTileLayer() {
    const cartoDbConfig = {
        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: ['a', 'b', 'c', 'd'],
        maxZoom: 19,
        minZoom: 2,
    };

    const osmConfig = {
        url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: ['a', 'b', 'c', 'd'],
        maxZoom: 19,
        minZoom: 2,
    };

    // Essayer CartoDB d'abord
    const primaryLayer = L.tileLayer(cartoDbConfig.url, {
        attribution: cartoDbConfig.attribution,
        subdomains: cartoDbConfig.subdomains,
        maxZoom: cartoDbConfig.maxZoom,
        minZoom: cartoDbConfig.minZoom,
    });

    // Gérer les erreurs et passer à OSM
    primaryLayer.on('tileerror', () => {
        console.warn('CartoDB tiles failed, switching to OSM fallback');

        // Retirer la couche CartoDB
        map.removeLayer(primaryLayer);

        // Ajouter la couche OSM
        L.tileLayer(osmConfig.url, {
            attribution: osmConfig.attribution,
            maxZoom: osmConfig.maxZoom,
            minZoom: osmConfig.minZoom,
        }).addTo(map);
    });

    primaryLayer.addTo(map);
}

/**
 * Cleanup (si nécessaire)
 */
export function destroyPlaceMap() {
    if (map) {
        map.remove();
        map = null;
    }
}
