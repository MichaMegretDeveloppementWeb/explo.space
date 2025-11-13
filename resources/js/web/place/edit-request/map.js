/**
 * Gestion de la carte Leaflet pour le formulaire de demande de modification
 * Permet de modifier les coordonnées GPS via un marqueur draggable
 */

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { createNormalMarkerIcon } from '../../../shared/marker-icon-utils.js';
import { getMinZoom } from '../../../shared/map-responsive-config.js';

/**
 * Configuration des tiles (CartoDB Positron avec fallback OSM)
 */
const TILE_CONFIG = {
    primary: {
        url: 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: ['a', 'b', 'c', 'd'],
        maxZoom: 19,
        // minZoom responsive : 1 sur mobile (< 800px), 3 sur desktop
        get minZoom() { return getMinZoom(); },
    },
    fallback: {
        url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
        // minZoom responsive : 1 sur mobile (< 800px), 3 sur desktop
        get minZoom() { return getMinZoom(); },
    },
};

/**
 * Initialiser la carte de demande de modification
 * @param {HTMLElement} container - Conteneur de la carte
 */
export function initEditRequestMap(container) {
    // Récupérer l'élément de données (séparé du container avec wire:ignore)
    const dataElement = document.getElementById('edit-request-map-data');
    if (!dataElement) {
        console.error('Map data element not found');
        return;
    }

    // Récupérer les coordonnées initiales depuis l'élément de données
    const initialLat = parseFloat(dataElement.dataset.lat);
    const initialLng = parseFloat(dataElement.dataset.lng);

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

    // Créer un marqueur draggable avec icône custom
    const marker = L.marker([initialLat, initialLng], {
        icon: createNormalMarkerIcon(),
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

    // Écouter les changements depuis les inputs Livewire (observer l'élément de données)
    listenToLivewireChanges(map, marker, dataElement);

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
 * Mettre à jour les coordonnées dans Livewire via événement
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 */
function updateLivewireCoordinates(lat, lng) {
    // Arrondir à 6 décimales pour éviter la précision excessive
    const roundedLat = Math.round(lat * 1000000) / 1000000;
    const roundedLng = Math.round(lng * 1000000) / 1000000;

    // Émettre événement pour afficher le loader
    window.dispatchEvent(new CustomEvent('map-geocoding-started'));

    // Dispatch événement Livewire pour appeler la méthode du composant
    window.Livewire.dispatch('update-coordinates-from-map', {
        lat: roundedLat,
        lng: roundedLng
    });
}

/**
 * Écouter les changements depuis les inputs Livewire et mettre à jour le marqueur
 * @param {L.Map} map - Instance de la carte
 * @param {L.Marker} marker - Marqueur draggable
 * @param {HTMLElement} dataElement - Élément de données (mis à jour par Livewire)
 */
function listenToLivewireChanges(map, marker, dataElement) {
    // Écouter les événements Livewire hooks pour détecter les mises à jour du composant
    document.addEventListener('livewire:update', (event) => {
        // Après chaque mise à jour Livewire, vérifier si les coordonnées ont changé
        const newLat = parseFloat(dataElement.dataset.lat);
        const newLng = parseFloat(dataElement.dataset.lng);

        if (!isNaN(newLat) && !isNaN(newLng)) {
            const currentPosition = marker.getLatLng();

            // Mettre à jour uniquement si les coordonnées ont réellement changé
            // (pour éviter boucle infinie avec drag marker)
            if (Math.abs(currentPosition.lat - newLat) > 0.000001 ||
                Math.abs(currentPosition.lng - newLng) > 0.000001) {
                const newPosition = L.latLng(newLat, newLng);
                marker.setLatLng(newPosition);
                map.panTo(newPosition);
            }
        }
    });

    // Aussi utiliser MutationObserver en backup pour les navigateurs qui ne supportent pas bien Livewire hooks
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' &&
                (mutation.attributeName === 'data-lat' || mutation.attributeName === 'data-lng')) {

                const newLat = parseFloat(dataElement.dataset.lat);
                const newLng = parseFloat(dataElement.dataset.lng);

                if (!isNaN(newLat) && !isNaN(newLng)) {
                    const currentPosition = marker.getLatLng();

                    // Mettre à jour uniquement si les coordonnées ont réellement changé
                    if (Math.abs(currentPosition.lat - newLat) > 0.000001 ||
                        Math.abs(currentPosition.lng - newLng) > 0.000001) {
                        const newPosition = L.latLng(newLat, newLng);
                        marker.setLatLng(newPosition);
                        map.panTo(newPosition);
                    }
                }
            }
        });
    });

    observer.observe(dataElement, {
        attributes: true,
        attributeFilter: ['data-lat', 'data-lng'],
    });
}
