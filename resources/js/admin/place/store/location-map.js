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
        this.initialCoords = null;
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
        // Remove existing marker
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }

        // Add new marker (draggable)
        this.marker = L.marker([lat, lng], {
            draggable: true,
        }).addTo(this.map);

        // Handle marker drag
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

        // Ne pas forcer le zoom/pan (garde la vue actuelle)
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
