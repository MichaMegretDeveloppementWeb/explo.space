/**
 * Module de gestion des marqueurs et clustering
 *
 * Responsabilités :
 * - Ajout/suppression de marqueurs
 * - Activation du clustering (> 50 marqueurs)
 * - Gestion du clic sur marqueur (ouverture modale de prévisualisation)
 * - Utilisation des icônes Leaflet par défaut
 */

import L from 'leaflet';
import 'leaflet.markercluster';
import { MAP_CONFIG } from './map-config.js';

export class PlaceMapMarkers {
    /**
     * @param {PlaceMap} mapInstance - Instance de PlaceMap
     */
    constructor(mapInstance) {
        this.map = mapInstance.getMap();
        this.mapInstance = mapInstance;
        this.markers = [];
        this.markerClusterGroup = null;
        this.regularMarkerGroup = null;
        this.options = MAP_CONFIG;
        this.useClusteringCurrently = false;
    }

    /**
     * Met à jour les marqueurs sur la carte
     *
     * @param {Array} places - Tableau d'objets { id, latitude, longitude, title }
     */
    updateMarkers(places) {
        if (!this.map) {
            console.error('Map not initialized');
            return;
        }

        // Nettoyer les marqueurs existants
        this.clearMarkers();

        // Vérifier s'il faut activer le clustering
        const shouldCluster = places.length >= this.options.performance.maxMarkersBeforeClustering;

        if (shouldCluster) {
            this.createClusteredMarkers(places);
        } else {
            this.createRegularMarkers(places);
        }
    }

    /**
     * Crée des marqueurs avec clustering
     *
     * @param {Array} places
     */
    createClusteredMarkers(places) {
        this.useClusteringCurrently = true;

        // Créer le groupe de clustering
        this.markerClusterGroup = L.markerClusterGroup({
            maxClusterRadius: this.options.clustering.maxClusterRadius,
            disableClusteringAtZoom: this.options.clustering.disableClusteringAtZoom,
            spiderfyOnMaxZoom: this.options.clustering.spiderfyOnMaxZoom,
            showCoverageOnHover: this.options.clustering.showCoverageOnHover,
            zoomToBoundsOnClick: this.options.clustering.zoomToBoundsOnClick,
            iconCreateFunction: this.options.clustering.iconCreateFunction,
        });

        // Ajouter les marqueurs au groupe
        places.forEach(place => {
            const marker = this.createMarker(place);
            if (marker) {
                this.markerClusterGroup.addLayer(marker);
                this.markers.push(marker);
            }
        });

        // Ajouter le groupe à la carte
        this.map.addLayer(this.markerClusterGroup);
    }

    /**
     * Crée des marqueurs sans clustering
     *
     * @param {Array} places
     */
    createRegularMarkers(places) {
        this.useClusteringCurrently = false;

        // Créer un groupe simple de marqueurs
        this.regularMarkerGroup = L.featureGroup();

        places.forEach(place => {
            const marker = this.createMarker(place);
            if (marker) {
                this.regularMarkerGroup.addLayer(marker);
                this.markers.push(marker);
            }
        });

        // Ajouter le groupe à la carte
        this.map.addLayer(this.regularMarkerGroup);
    }

    /**
     * Crée un marqueur individuel
     *
     * @param {Object} place - { id, latitude, longitude, title }
     * @returns {L.Marker|null}
     */
    createMarker(place) {
        if (!place.latitude || !place.longitude) {
            console.warn('Invalid place coordinates', place);
            return null;
        }

        // Utiliser l'icône Leaflet par défaut
        const marker = L.marker([place.latitude, place.longitude], {
            icon: L.icon({
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41],
            }),
        });

        // Stocker les données du lieu dans le marqueur
        marker.placeData = place;

        // Événement au clic : ouverture modale de prévisualisation
        marker.on('click', () => {
            this.onMarkerClick(place);
        });

        return marker;
    }

    /**
     * Gère le clic sur un marqueur
     *
     * Émet un événement Livewire qui sera capté par PlacePreviewModal
     * pour ouvrir la modale de prévisualisation du lieu
     *
     * @param {Object} place - Données du lieu { id, latitude, longitude }
     */
    onMarkerClick(place) {
        if (window.Livewire) {
            window.Livewire.dispatch('marker-clicked', { placeId: place.id });
        }
    }


    /**
     * Nettoie tous les marqueurs
     */
    clearMarkers() {
        // Supprimer le groupe de clustering
        if (this.markerClusterGroup) {
            this.map.removeLayer(this.markerClusterGroup);
            this.markerClusterGroup = null;
        }

        // Supprimer le groupe de marqueurs réguliers
        if (this.regularMarkerGroup) {
            this.map.removeLayer(this.regularMarkerGroup);
            this.regularMarkerGroup = null;
        }

        // Vider le tableau de marqueurs
        this.markers = [];
        this.useClusteringCurrently = false;
    }

    /**
     * Trouve un marqueur par ID de lieu
     *
     * @param {number} placeId
     * @returns {L.Marker|null}
     */
    findMarkerByPlaceId(placeId) {
        return this.markers.find(marker => marker.placeData && marker.placeData.id === placeId) || null;
    }

    /**
     * Centre la carte sur un marqueur spécifique
     *
     * @param {number} placeId
     * @param {number} zoom - Niveau de zoom (optionnel)
     */
    focusOnMarker(placeId, zoom = null) {
        const marker = this.findMarkerByPlaceId(placeId);

        if (marker) {
            const coords = marker.getLatLng();
            const currentZoom = this.map.getZoom();
            const targetZoom = zoom ?? Math.max(currentZoom, 13);

            this.mapInstance.setView(coords.lat, coords.lng, targetZoom, true);
        }
    }

    /**
     * Retourne le nombre de marqueurs actuels
     *
     * @returns {number}
     */
    getMarkerCount() {
        return this.markers.length;
    }

    /**
     * Vérifie si le clustering est actif
     *
     * @returns {boolean}
     */
    isClusteringActive() {
        return this.useClusteringCurrently;
    }

    /**
     * Obtient le parent visible d'un marqueur (le marqueur lui-même ou son cluster)
     *
     * @param {number} placeId
     * @returns {L.Marker|L.MarkerCluster|null}
     */
    getVisibleParent(placeId) {
        const marker = this.findMarkerByPlaceId(placeId);

        if (!marker) {
            return null;
        }

        // Si clustering inactif, retourner le marqueur directement
        if (!this.useClusteringCurrently || !this.markerClusterGroup) {
            return marker;
        }

        // Utiliser getVisibleParent() de markerClusterGroup
        // Retourne le marqueur s'il est visible, ou le cluster qui le contient
        return this.markerClusterGroup.getVisibleParent(marker);
    }

    /**
     * Vérifie si un élément est un cluster
     *
     * @param {L.Marker|L.MarkerCluster} element
     * @returns {boolean}
     */
    isCluster(element) {
        return element && element instanceof L.MarkerCluster;
    }
}
