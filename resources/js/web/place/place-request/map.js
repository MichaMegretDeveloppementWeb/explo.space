/**
 * Interactive Leaflet map for place request form
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
import { createNormalMarkerIcon } from '../../map/marker-icon-utils.js';

export class PlaceRequestMap {
    constructor(containerId = 'placeRequestMap') {
        this.containerId = containerId;
        this.map = null;
        this.marker = null;

        this.init();
    }

    /**
     * Initialize the map
     */
    init() {
        // Get the container
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.warn(`PlaceRequestMap: Container #${this.containerId} not found`);
            return false;
        }

        // Create the map
        this.createMap(container);

        // Set up event listeners
        this.setupListeners();

        return true;
    }

    /**
     * Create the Leaflet map
     */
    createMap(container) {
        // Default view: Paris, zoom 4 (aucune coordonnée initiale)
        const defaultView = [48.8566, 2.3522];
        const defaultZoom = 4;

        // Create map
        this.map = L.map(container, {
            center: defaultView,
            zoom: defaultZoom,
            maxBounds: [[-90, -180], [90, 180]],
            maxBoundsViscosity: 1.0,
        });

        // Add tile layer (OSM)
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
     * Handle map click - Notify Livewire for reverse geocoding
     */
    handleMapClick(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Update marker locally
        this.setMarker(lat, lng);

        // Notify Livewire for reverse geocoding
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

        // Add new marker (draggable) avec icône custom
        this.marker = L.marker([lat, lng], {
            icon: createNormalMarkerIcon(),
            draggable: true,
        }).addTo(this.map);

        // Handle marker drag
        this.marker.on('dragend', (e) => {
            const position = e.target.getLatLng();

            // Notify Livewire
            if (window.Livewire) {
                window.Livewire.dispatch('map-clicked', {
                    latitude: position.lat,
                    longitude: position.lng,
                });
            }
        });

        // Don't force zoom/pan (keep current view)
    }

    /**
     * Center map on location and place marker
     * Called when address is selected from autocomplete
     */
    centerOnLocation(latitude, longitude) {
        if (!this.map) {
            return;
        }

        this.setMarker(latitude, longitude);
        this.map.setView([latitude, longitude], 13);
    }

    /**
     * Set up event listeners - Listen to Livewire events
     */
    setupListeners() {
        const setupLivewireListeners = () => {
            // Listen for address selection from autocomplete
            window.Livewire.on('address-selected', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (data && data.latitude && data.longitude) {
                    this.setMarker(data.latitude, data.longitude);
                    this.map.setView([data.latitude, data.longitude], 13);
                }
            });

            // Listen for manual coordinate changes
            window.Livewire.on('coordinates-changed', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                if (data && data.latitude && data.longitude) {
                    this.setMarker(data.latitude, data.longitude);
                    this.map.setView([data.latitude, data.longitude], this.map.getZoom()); // Keep current zoom
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
