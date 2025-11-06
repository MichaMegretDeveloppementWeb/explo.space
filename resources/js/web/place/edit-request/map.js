/**
 * Gestion de la carte Leaflet pour le formulaire de demande de modification
 * Permet de modifier les coordonnées GPS via un marqueur draggable
 */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

/**
 * Configuration des tiles (CartoDB Positron avec fallback OSM)
 */
const TILE_CONFIG = {
    primary: {
        url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: ['a', 'b', 'c', 'd'],
        maxZoom: 19,
        minZoom: 2,
    },
    fallback: {
        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        minZoom: 2,
    },
};

/**
 * Initialiser la carte de demande de modification
 * @param {HTMLElement} container - Conteneur de la carte
 */
export function initEditRequestMap(container) {
    // Récupérer les coordonnées initiales depuis les attributs data
    const initialLat = parseFloat(container.dataset.lat);
    const initialLng = parseFloat(container.dataset.lng);

    // Vérifier que les coordonnées sont valides
    if (isNaN(initialLat) || isNaN(initialLng)) {
        console.error('Invalid coordinates for edit request map');
        return;
    }

    // Créer la carte centrée sur les coordonnées actuelles
    const map = L.map(container, {
        center: [initialLat, initialLng],
        zoom: 15,
        maxBounds: [
            [-90, -180], // Sud-Ouest
            [90, 180],   // Nord-Est
        ],
        maxBoundsViscosity: 1.0,
    });

    // Ajouter les tiles CartoDB avec fallback OSM
    addTileLayer(map);

    // Créer un marqueur draggable
    const marker = L.marker([initialLat, initialLng], {
        draggable: true,
        autoPan: true,
    }).addTo(map);

    // Popup d'instruction
    marker.bindPopup('Déplacez le marqueur pour modifier la position').openPopup();

    // Mettre à jour Livewire lors du déplacement du marqueur
    marker.on('dragend', function(event) {
        const position = event.target.getLatLng();
        updateLivewireCoordinates(position.lat, position.lng);
    });

    // Écouter les changements depuis les inputs Livewire
    listenToLivewireChanges(map, marker, container);

    // Invalider la taille au cas où le conteneur change de dimension
    setTimeout(() => {
        map.invalidateSize();
    }, 100);
}

/**
 * Ajouter les tiles avec fallback automatique
 * @param {L.Map} map - Instance de la carte Leaflet
 */
function addTileLayer(map) {
    const primaryConfig = TILE_CONFIG.primary;
    const fallbackConfig = TILE_CONFIG.fallback;

    // Essayer d'abord CartoDB
    const primaryLayer = L.tileLayer(primaryConfig.url, {
        attribution: primaryConfig.attribution,
        subdomains: primaryConfig.subdomains,
        maxZoom: primaryConfig.maxZoom,
        minZoom: primaryConfig.minZoom,
    });

    // Gérer les erreurs de chargement
    primaryLayer.on('tileerror', () => {
        console.warn('CartoDB tiles failed, switching to OSM fallback');

        // Retirer la couche primaire
        map.removeLayer(primaryLayer);

        // Ajouter la couche fallback
        L.tileLayer(fallbackConfig.url, {
            attribution: fallbackConfig.attribution,
            maxZoom: fallbackConfig.maxZoom,
            minZoom: fallbackConfig.minZoom,
        }).addTo(map);
    });

    primaryLayer.addTo(map);
}

/**
 * Mettre à jour les coordonnées dans Livewire
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 */
function updateLivewireCoordinates(lat, lng) {
    // Arrondir à 6 décimales pour éviter la précision excessive
    const roundedLat = Math.round(lat * 1000000) / 1000000;
    const roundedLng = Math.round(lng * 1000000) / 1000000;

    // Utiliser Livewire pour mettre à jour les valeurs
    if (window.Livewire) {
        // Trouver le composant Livewire parent
        const component = window.Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );

        if (component) {
            component.set('new_values.coordinates.lat', roundedLat);
            component.set('new_values.coordinates.lng', roundedLng);
        }
    }
}

/**
 * Écouter les changements depuis les inputs Livewire et mettre à jour le marqueur
 * @param {L.Map} map - Instance de la carte
 * @param {L.Marker} marker - Marqueur draggable
 * @param {HTMLElement} container - Conteneur de la carte
 */
function listenToLivewireChanges(map, marker, container) {
    // Créer un MutationObserver pour détecter les changements d'attributs data
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' &&
                (mutation.attributeName === 'data-lat' || mutation.attributeName === 'data-lng')) {

                const newLat = parseFloat(container.dataset.lat);
                const newLng = parseFloat(container.dataset.lng);

                if (!isNaN(newLat) && !isNaN(newLng)) {
                    const newPosition = L.latLng(newLat, newLng);
                    marker.setLatLng(newPosition);
                    map.panTo(newPosition);
                }
            }
        });
    });

    observer.observe(container, {
        attributes: true,
        attributeFilter: ['data-lat', 'data-lng'],
    });
}
