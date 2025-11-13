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

        // Initialiser les définitions SVG globales
        this.initSvgDefs();
    }

    /**
     * Initialise les définitions SVG globales (filtres, dégradés, etc.)
     * Une seule fois pour éviter la duplication
     */
    initSvgDefs() {
        // Vérifier si les defs existent déjà
        if (document.getElementById('explo-map-svg-defs')) {
            return;
        }

        // Créer un élément SVG caché avec toutes les définitions
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('id', 'explo-map-svg-defs');
        svg.style.position = 'absolute';
        svg.style.width = '0';
        svg.style.height = '0';
        svg.style.visibility = 'hidden';

        svg.innerHTML = `
            <defs>
                <!-- Filtre d'ombre portée pour marqueurs featured -->
                <filter id="marker-shadow-featured" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur in="SourceAlpha" stdDeviation="1"/>
                    <feOffset dx="0" dy="1" result="offsetblur"/>
                    <feComponentTransfer>
                        <feFuncA type="linear" slope="0.3"/>
                    </feComponentTransfer>
                    <feMerge>
                        <feMergeNode/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>

                <!-- Filtre d'ombre portée pour marqueurs normaux -->
                <filter id="marker-shadow-normal" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur in="SourceAlpha" stdDeviation="1"/>
                    <feOffset dx="0" dy="1" result="offsetblur"/>
                    <feComponentTransfer>
                        <feFuncA type="linear" slope="0.3"/>
                    </feComponentTransfer>
                    <feMerge>
                        <feMergeNode/>
                        <feMergeNode in="SourceGraphic"/>
                    </feMerge>
                </filter>
            </defs>
        `;

        // Ajouter au body
        document.body.appendChild(svg);
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
            iconCreateFunction: (cluster) => this.createClusterIcon(cluster),
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
     * Crée une icône pour un cluster
     *
     * @param {L.MarkerCluster} cluster
     * @returns {L.DivIcon}
     */
    createClusterIcon(cluster) {
        const count = cluster.getChildCount();
        let size = 'small';
        let circleSize = 40;
        let fontSize = '13px';
        let innerSize = 34;

        if (count > 100) {
            size = 'large';
            circleSize = 56;
            fontSize = '17px';
            innerSize = 48;
        } else if (count > 10) {
            size = 'medium';
            circleSize = 48;
            fontSize = '15px';
            innerSize = 40;
        }

        // Vérifier si le cluster contient au moins un lieu emblématique
        const hasFeatured = cluster.getAllChildMarkers().some(marker =>
            marker.placeData && marker.placeData.is_featured
        );

        // Couleurs selon le type
        const mainColor = hasFeatured ? '#9333ea' : '#3b82f6';
        const lightColor = hasFeatured ? '#a855f7' : '#60a5fa';
        const darkColor = hasFeatured ? '#7c3aed' : '#2563eb';

        // Badge étoile élégant si featured
        const starBadge = hasFeatured ? `
            <div style="
                position: absolute;
                top: -3px;
                right: -3px;
                background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%);
                border: 2.5px solid white;
                border-radius: 50%;
                width: 22px;
                height: 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            ">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
        ` : '';

        const html = `
            <div style="position: relative; width: ${circleSize}px; height: ${circleSize}px;">
                <!-- Cercle extérieur avec dégradé -->
                <div style="
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, ${lightColor} 0%, ${mainColor} 100%);
                    border-radius: 50%;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25), 0 0 0 3px rgba(255, 255, 255, 0.3);
                "></div>

                <!-- Cercle intérieur avec le nombre -->
                <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: ${innerSize}px;
                    height: ${innerSize}px;
                    background: ${darkColor};
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 700;
                    font-size: ${fontSize};
                    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
                ">
                    ${count}
                </div>

                ${starBadge}
            </div>
        `;

        return L.divIcon({
            html: html,
            className: 'marker-cluster-custom',
            iconSize: L.point(circleSize, circleSize),
        });
    }

    /**
     * Récupère l'icône appropriée selon le type de lieu
     *
     * @param {Object} place - { id, latitude, longitude, is_featured }
     * @returns {L.Icon}
     */
    getMarkerIcon(place) {
        // Marqueur violet avec étoile pour les lieux emblématiques
        if (place.is_featured) {
            return this.createFeaturedIcon(27, 42);
        }

        // Marqueur bleu avec point blanc pour les lieux normaux
        return this.createNormalIcon(27, 42);
    }

    /**
     * Crée une icône bleue avec point blanc pour les lieux normaux
     *
     * @param {number} width - Largeur de l'icône
     * @param {number} height - Hauteur de l'icône
     * @returns {L.DivIcon}
     */
    createNormalIcon(width, height) {
        const svg = `
            <svg width="${width}" height="${height}" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
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

        return L.divIcon({
            html: svg,
            className: 'normal-marker-icon',
            iconSize: [width, height],
            iconAnchor: [width/2, height],
            popupAnchor: [0, -height + 5],
        });
    }

    /**
     * Crée une icône violette avec étoile blanche pour les lieux emblématiques
     *
     * @param {number} width - Largeur de l'icône
     * @param {number} height - Hauteur de l'icône
     * @returns {L.DivIcon}
     */
    createFeaturedIcon(width, height) {
        const svg = `
            <svg width="${width}" height="${height}" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
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

        return L.divIcon({
            html: svg,
            className: 'featured-marker-icon',
            iconSize: [width, height],
            iconAnchor: [width/2, height],
            popupAnchor: [0, -height + 5],
        });
    }

    /**
     * Crée un marqueur individuel
     *
     * @param {Object} place - { id, latitude, longitude, is_featured }
     * @returns {L.Marker|null}
     */
    createMarker(place) {
        if (!place.latitude || !place.longitude) {
            console.warn('Invalid place coordinates', place);
            return null;
        }

        // Créer le marqueur avec l'icône appropriée
        const marker = L.marker([place.latitude, place.longitude], {
            icon: this.getMarkerIcon(place),
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
