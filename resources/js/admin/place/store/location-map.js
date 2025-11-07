/**
 * Interactive Leaflet map for admin place form
 *
 * Features:
 * - Click to set coordinates
 * - Display marker at current coordinates
 * - Draggable marker
 * - Bidirectional sync with Livewire via events
 *
 * Communication:
 * - JS → Livewire: 'map-clicked' (latitude, longitude)
 * - Livewire → JS: 'address-selected' (latitude, longitude)
 * - Livewire → JS: 'coordinates-changed' (latitude, longitude)
 */
import L from 'leaflet';

export class LocationMap {
    constructor(containerId = 'admin-location-map') {
        this.containerId = containerId;
        this.map = null;
        this.marker = null;
        this.oldMarker = null; // Marker pour anciennes coordonnées (EditRequest)
        this.initialCoords = null;
        this.originalCoords = null; // Anciennes coordonnées (EditRequest)
        this.latInput = null;
        this.lngInput = null;

        this.init();
    }

    /**
     * Initialize the map
     */
    init() {
        // Get the container
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.warn(`LocationMap: Container #${this.containerId} not found`);
            return;
        }

        // Get coordinate inputs
        this.latInput = document.getElementById('latitude');
        this.lngInput = document.getElementById('longitude');

        if (!this.latInput || !this.lngInput) {
            console.warn('LocationMap: Latitude or Longitude input not found');
            return;
        }

        // Get initial coordinates from data attributes
        this.initialCoords = this.getInitialCoordinatesFromContainer(container);

        // Create the map
        this.createMap(container);

        // Set up event listeners
        this.setupListeners();

        // Place initial marker when map is ready
        this.map.whenReady(() => {
            if (this.isValidCoordinate(this.initialCoords)) {
                this.setMarker(this.initialCoords.lat, this.initialCoords.lng);
                console.log('✓ Initial marker placed at:', this.initialCoords);
            } else {
                console.log('ℹ No initial coordinates, marker not placed');
            }
        });
    }

    /**
     * Get initial coordinates from container data attributes
     */
    getInitialCoordinatesFromContainer(container) {
        const lat = parseFloat(container.dataset.latitude) || 0;
        const lng = parseFloat(container.dataset.longitude) || 0;

        // Récupérer également les anciennes coordonnées (EditRequest)
        const originalLat = parseFloat(container.dataset.originalLatitude);
        const originalLng = parseFloat(container.dataset.originalLongitude);

        if (!isNaN(originalLat) && !isNaN(originalLng) && originalLat !== 0 && originalLng !== 0) {
            this.originalCoords = { lat: originalLat, lng: originalLng };
        }

        return { lat, lng };
    }

    /**
     * Check if coordinates are valid (not 0,0)
     */
    isValidCoordinate(coords) {
        return coords.lat !== 0 && coords.lng !== 0;
    }

    /**
     * Create the Leaflet map
     */
    createMap(container) {
        // Default view (world center if no coordinates)
        const defaultView = this.isValidCoordinate(this.initialCoords)
            ? [this.initialCoords.lat, this.initialCoords.lng]
            : [20, 0];

        const defaultZoom = 3;

        // Create map
        this.map = L.map(container, {
            center: defaultView,
            zoom: defaultZoom,
            maxBounds: [[-90, -180], [90, 180]],
            maxBoundsViscosity: 1.0,
        });

        // Add tile layer (CartoDB Positron with OSM fallback)
        this.addTileLayer();

        // Click to set coordinates
        this.map.on('click', (e) => this.handleMapClick(e));
    }

    /**
     * Add tile layer with fallback
     */
    addTileLayer() {
        const primaryLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: ['a', 'b', 'c', 'd'],
            maxZoom: 19,
            minZoom: 2,
        });

        primaryLayer.on('tileerror', () => {
            console.warn('CartoDB tiles failed, switching to OSM fallback');
            this.map.removeLayer(primaryLayer);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
                minZoom: 2,
            }).addTo(this.map);
        });

        primaryLayer.addTo(this.map);
    }

    /**
     * Handle map click - Simplifié : juste notifier Livewire
     */
    handleMapClick(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Mettre à jour le marker localement
        this.setMarker(lat, lng);

        // Notifier Livewire pour reverse geocoding
        if (window.Livewire) {
            window.Livewire.dispatch('map-clicked', {
                latitude: lat,
                longitude: lng,
            });
        }
    }

    /**
     * Set marker on map
     */
    setMarker(lat, lng) {
        // Remove existing new marker
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }

        // Si nous avons des anciennes coordonnées (EditRequest), créer le marker ancien
        if (this.originalCoords && !this.oldMarker) {
            // Marker ANCIEN (gris, style pin comme carte exploration)
            const oldIconSvg = `
                <svg width="27" height="42" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                          fill="#9CA3AF"
                          stroke="#ffffff"
                          stroke-width="1.5"/>
                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                </svg>
            `;

            const oldIcon = L.divIcon({
                className: 'old-marker-icon',
                html: oldIconSvg,
                iconSize: [27, 42],
                iconAnchor: [13.5, 42],
                popupAnchor: [0, -37],
            });

            this.oldMarker = L.marker([this.originalCoords.lat, this.originalCoords.lng], {
                icon: oldIcon,
                draggable: false,
            }).addTo(this.map);

            // Popup ancienne position - toujours ouvert
            this.oldMarker.bindPopup('Ancienne position', {
                closeButton: false,
                closeOnClick: false,
                autoClose: false,
                className: 'simple-marker-popup'
            }).openPopup();
        }

        // Marker NOUVEAU (bleu, style pin comme carte exploration)
        const markerOptions = {
            draggable: true,
        };

        // Ajouter l'icône custom uniquement si nous avons des anciennes coordonnées
        if (this.originalCoords) {
            const newIconSvg = `
                <svg width="27" height="42" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                          fill="#3b82f6"
                          stroke="#ffffff"
                          stroke-width="1.5"/>
                    <circle cx="12.5" cy="12.5" r="5" fill="white"/>
                </svg>
            `;

            markerOptions.icon = L.divIcon({
                className: 'new-marker-icon',
                html: newIconSvg,
                iconSize: [27, 42],
                iconAnchor: [13.5, 42],
                popupAnchor: [0, -37],
            });
        }

        this.marker = L.marker([lat, lng], markerOptions).addTo(this.map);

        if (this.originalCoords) {
            // Popup nouvelle position - toujours ouvert initialement
            this.marker.bindPopup('Nouvelle position', {
                closeButton: false,
                closeOnClick: false,
                autoClose: false,
                className: 'simple-marker-popup'
            }).openPopup();
        }

        // Handle marker drag
        this.marker.on('dragstart', (e) => {
            // Fermer le popup au début du drag
            if (this.originalCoords && this.marker.getPopup()) {
                this.marker.closePopup();
            }
        });

        this.marker.on('dragend', (e) => {
            const position = e.target.getLatLng();

            // Notifier Livewire
            if (window.Livewire) {
                window.Livewire.dispatch('map-clicked', {
                    latitude: position.lat,
                    longitude: position.lng,
                });
            }
        });

        // Si double marker, fit bounds pour montrer les deux
        if (this.originalCoords) {
            const bounds = L.latLngBounds(
                [this.originalCoords.lat, this.originalCoords.lng],
                [lat, lng]
            );
            this.map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    /**
     * Set up event listeners - Écouter les événements Livewire
     */
    setupListeners() {
        const setupLivewireListeners = () => {
            // Écouter sélection d'adresse depuis l'autocomplétion
            window.Livewire.on('address-selected', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (data && data.latitude && data.longitude) {
                    this.setMarker(data.latitude, data.longitude);
                    this.map.setView([data.latitude, data.longitude], 8);
                }
            });

            // Écouter modification manuelle des coordonnées
            window.Livewire.on('coordinates-changed', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (data && data.latitude && data.longitude) {
                    this.setMarker(data.latitude, data.longitude);
                    this.map.setView([data.latitude, data.longitude], this.map.getZoom()); // Conserver le zoom actuel
                }
            });
        };

        // Execute immediately if Livewire is already loaded, otherwise wait for the event
        if (window.Livewire) {
            setupLivewireListeners();
        } else {
            document.addEventListener('livewire:init', setupLivewireListeners);
        }
    }

    /**
     * Destroy the map instance
     */
    destroy() {
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    }
}
