/**
 * Module principal de la carte Leaflet
 *
 * Responsabilités :
 * - Initialisation de la carte
 * - Chargement des tuiles avec fallback (CartoDB → OSM)
 * - Calcul du bounding box dynamique
 * - Gestion de la stabilisation de la carte (whenStable)
 * - Fournir des méthodes utilitaires pour manipuler la carte
 *
 * Note : Ce module NE gère PAS les événements. Les listeners sont
 * configurés dans index.js pour une meilleure séparation des responsabilités.
 */

import L from 'leaflet';
import { MAP_CONFIG } from './map-config.js';
import { calculateZoomFromRadius, getCircleBounds } from './map-zoom-calculator.js';
import { createGeodesicCircle } from './geodesic-circle.js';

export class PlaceMap {
    /**
     * @param {string} containerId - ID du conteneur DOM
     * @param {Object} options - Options de configuration (override MAP_CONFIG)
     */
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.map = null;
        this.tileLayer = null;
        this.options = { ...MAP_CONFIG, ...options };
        this.isInitialized = false;
        this.useFallbackTiles = false;
        this.lastBoundingBox = null; // Pour éviter les événements redondants
        this.useBoundingBox = false; // Système de bounding box dynamique activé/désactivé
        this.radiusCircle = null; // Cercle visualisant le rayon de recherche (mode proximity)
    }

    /**
     * Initialise la carte Leaflet
     *
     * @param {number} latitude - Latitude initiale
     * @param {number} longitude - Longitude initiale
     * @param {number} zoom - Niveau de zoom initial
     * @param {boolean} useBoundingBox - Activer le système de bounding box dynamique
     * @returns {L.Map} Instance de la carte
     */
    init(latitude = null, longitude = null, zoom = null, useBoundingBox = false) {
        if (this.isInitialized) {
            return this.map;
        }

        // Sauvegarder le mode bounding box
        this.useBoundingBox = useBoundingBox;

        // Utiliser les valeurs fournies ou les valeurs par défaut
        const center = latitude && longitude
            ? [latitude, longitude]
            : this.options.defaultView.center;
        const initialZoom = zoom ?? this.options.defaultView.zoom;

        // Créer la carte
        this.map = L.map(this.containerId, {
            center: center,
            zoom: initialZoom,
            zoomControl: true,
            worldCopyJump: false,        // Empêche le "saut" vers copie principale
            maxBounds: [[-90, -180], [90, 180]],  // Limite aux coordonnées réelles
            maxBoundsViscosity: 1.0,     // Empêche de sortir des limites

        });

        // Ajouter les tuiles avec système de fallback
        this.loadTileLayer();

        this.isInitialized = true;

        return this.map;
    }

    /**
     * Charge la couche de tuiles avec fallback automatique
     */
    loadTileLayer() {
        const primaryConfig = this.options.tileLayer.fallback;
        const fallbackConfig = this.options.tileLayer.fallback;

        // Essayer de charger les tuiles CartoDB
        this.tileLayer = L.tileLayer(primaryConfig.url, {
            attribution: primaryConfig.attribution,
            subdomains: primaryConfig.subdomains,
            maxZoom: primaryConfig.maxZoom,
            minZoom: primaryConfig.minZoom,
        });

        // Gérer l'erreur de chargement des tuiles (fallback vers OSM)
        this.tileLayer.on('tileerror', () => {
            if (!this.useFallbackTiles) {
                this.switchToFallbackTiles();
            }
        });

        this.tileLayer.addTo(this.map);
    }

    /**
     * Bascule vers les tuiles OSM (fallback)
     */
    switchToFallbackTiles() {
        this.useFallbackTiles = true;

        // Supprimer la couche actuelle
        if (this.tileLayer) {
            this.map.removeLayer(this.tileLayer);
        }

        // Ajouter la couche de fallback
        const fallbackConfig = this.options.tileLayer.fallback;
        this.tileLayer = L.tileLayer(fallbackConfig.url, {
            attribution: fallbackConfig.attribution,
            maxZoom: fallbackConfig.maxZoom,
            minZoom: fallbackConfig.minZoom,
        });

        this.tileLayer.addTo(this.map);
    }

    /**
     * Calcule la bounding box actuelle de la carte
     *
     * @returns {Object} { north, south, east, west }
     */
    getBoundingBox() {
        if (!this.map) {
            return null;
        }

        const bounds = this.map.getBounds();

        return {
            north: bounds.getNorth(),
            south: bounds.getSouth(),
            east: bounds.getEast(),
            west: bounds.getWest(),
        };
    }

    /**
     * Définit la vue de la carte
     *
     * @param {number} latitude
     * @param {number} longitude
     * @param {number} zoom
     * @param {boolean} animate - Animer la transition
     */
    setView(latitude, longitude, zoom, animate = true) {
        if (!this.map) {
            console.error('Map not initialized');
            return;
        }

        if (animate) {
            this.map.flyTo([latitude, longitude], zoom, {
                duration: this.options.performance.animationDuration / 1000,
            });
        } else {
            this.map.setView([latitude, longitude], zoom);
        }
    }

    /**
     * Ajuste la vue pour afficher tous les marqueurs
     *
     * @param {Array} markers - Tableau de marqueurs L.Marker
     */
    fitBounds(markers) {
        if (!this.map || !markers || markers.length === 0) {
            return;
        }

        const group = L.featureGroup(markers);
        this.map.fitBounds(group.getBounds(), {
            padding: [50, 50],
        });
    }

    /**
     * Invalide la taille de la carte (utile après changement de taille conteneur)
     */
    invalidateSize() {
        if (this.map) {
            this.map.invalidateSize();
        }
    }

    /**
     * Détruit la carte et libère les ressources
     */
    destroy() {
        if (this.map) {
            this.removeRadiusCircle();
            this.map.remove();
            this.map = null;
            this.tileLayer = null;
            this.isInitialized = false;
        }
    }

    // ========================================
    // GESTION DU CERCLE DE RAYON
    // ========================================

    /**
     * Affiche un cercle visualisant le rayon de recherche
     * Utilise un cercle géodésique pour une précision correcte à toutes les latitudes
     *
     * @param {number} latitude - Centre du cercle
     * @param {number} longitude - Centre du cercle
     * @param {number} radiusMeters - Rayon en mètres
     */
    showRadiusCircle(latitude, longitude, radiusMeters) {
        if (!this.map) {
            console.error('Map not initialized');
            return;
        }

        // Supprimer l'ancien cercle s'il existe
        this.removeRadiusCircle();

        // Créer un cercle géodésique (calcule la vraie distance sur la sphère terrestre)
        // Match le calcul backend ST_Distance_Sphere() pour cohérence visuelle
        this.radiusCircle = createGeodesicCircle(
            [latitude, longitude],
            radiusMeters,
            {
                color: '#3b82f6',        // Bleu (border)
                fillColor: '#3b82f6',    // Bleu (fill)
                fillOpacity: 0.1,        // Très transparent
                weight: 2,               // Épaisseur de la bordure
                opacity: 0.6,            // Opacité de la bordure
            }
            // Nombre de points calculé automatiquement selon le rayon (adaptatif)
        );

        this.radiusCircle.addTo(this.map);
    }

    /**
     * Supprime le cercle de rayon de la carte
     */
    removeRadiusCircle() {
        if (this.radiusCircle && this.map) {
            this.map.removeLayer(this.radiusCircle);
            this.radiusCircle = null;
        }
    }

    /**
     * Met à jour la position et le rayon du cercle
     * Les cercles géodésiques sont recalculés à chaque mise à jour
     *
     * @param {number} latitude
     * @param {number} longitude
     * @param {number} radiusMeters
     */
    updateRadiusCircle(latitude, longitude, radiusMeters) {
        if (!this.map) {
            console.error('Map not initialized');
            return;
        }

        // Les cercles géodésiques sont des polygones calculés,
        // donc on les recrée plutôt que de les mettre à jour
        this.showRadiusCircle(latitude, longitude, radiusMeters);
    }

    /**
     * Retourne l'instance Leaflet
     *
     * @returns {L.Map|null}
     */
    getMap() {
        return this.map;
    }


    // ========================================
    // MÉTHODES DE GESTION DE VUE DYNAMIQUE
    // ========================================

    /**
     * Centre la carte sur une position avec zoom calculé selon le rayon de recherche
     * Retourne une Promise qui se résout quand le mouvement est terminé
     *
     * @param {number} latitude
     * @param {number} longitude
     * @param {number} radiusMeters - Rayon de recherche en mètres
     * @param {boolean} animate - Animation fluide (true par défaut)
     * @returns {Promise<void>}
     */
    centerOnLocation(latitude, longitude, radiusMeters, animate = true) {
        if (!this.map) {
            console.error('Map not initialized');
            return Promise.resolve();
        }

        const zoom = calculateZoomFromRadius(radiusMeters, latitude, this.map);
        this.showRadiusCircle(latitude, longitude, radiusMeters);

        return new Promise((resolve) => {
            // Fonction appelée une seule fois quand le mouvement est terminé
            const handleMoveEnd = () => {
                this.map.off('moveend', handleMoveEnd);
                resolve();
            };

            // Écouter moveend
            this.map.on('moveend', handleMoveEnd);

            // Lancer le mouvement
            if (animate) {
                this.map.flyTo([latitude, longitude], zoom, {
                    duration: this.options.performance.animationDuration / 1000,
                });
            } else {
                this.map.setView([latitude, longitude], zoom);
            }

            // Vérifier si un mouvement est réellement nécessaire
            // Si coords/zoom identiques, résoudre immédiatement
            setTimeout(() => {
                const currentCenter = this.map.getCenter();
                const currentZoom = this.map.getZoom();

                const noMovement = (
                    Math.abs(currentCenter.lat - latitude) < 0.000001 &&
                    Math.abs(currentCenter.lng - longitude) < 0.000001 &&
                    currentZoom === zoom
                );

                if (noMovement) {
                    this.map.off('moveend', handleMoveEnd);
                    resolve();
                }
            }, 10);
        });
    }

    /**
     * Affiche la vue mondiale (mode "Monde entier")
     * Centre [40, 0] avec zoom 3
     * Retourne une Promise qui se résout quand le mouvement est terminé
     *
     * @param {boolean} animate - Animation fluide (true par défaut)
     * @returns {Promise<void>}
     */
    showWorldView(animate = true) {
        if (!this.map) {
            console.error('Map not initialized');
            return Promise.resolve();
        }

        const worldCenter = [40, 0];
        const worldZoom = 3;

        // Supprimer le cercle de rayon (pas de rayon en mode worldwide)
        this.removeRadiusCircle();

        return new Promise((resolve) => {
            // Fonction appelée une seule fois quand le mouvement est terminé
            const handleMoveEnd = () => {
                this.map.off('moveend', handleMoveEnd);
                resolve();
            };

            // Écouter moveend
            this.map.on('moveend', handleMoveEnd);

            // Lancer le mouvement
            if (animate) {
                this.map.flyTo(worldCenter, worldZoom, {
                    duration: this.options.performance.animationDuration / 1000,
                });
            } else {
                this.map.setView(worldCenter, worldZoom);
            }

            // Vérifier si un mouvement est réellement nécessaire
            // Si coords/zoom identiques, résoudre immédiatement
            setTimeout(() => {

                const currentCenter = this.map.getCenter();
                const currentZoom = this.map.getZoom();

                const noMovement = (
                    Math.abs(currentCenter.lat - worldCenter[0]) < 0.000001 &&
                    Math.abs(currentCenter.lng - worldCenter[1]) < 0.000001 &&
                    currentZoom === worldZoom
                );

                if (noMovement) {
                    this.map.off('moveend', handleMoveEnd);
                    resolve();
                }
            }, 100);
        });
    }

    /**
     * Ajuste le niveau de zoom selon le rayon en utilisant les coordonnées sélectionnées
     * Utilisé quand l'user change le rayon avec le curseur
     * Retourne une Promise qui se résout quand le mouvement est terminé
     *
     * @param {number} latitude - Latitude des coordonnées sélectionnées
     * @param {number} longitude - Longitude des coordonnées sélectionnées
     * @param {number} radiusMeters - Nouveau rayon en mètres
     * @param {boolean} animate - Animation (false par défaut pour le rayon)
     * @returns {Promise<void>}
     */
    adjustZoomToRadius(latitude, longitude, radiusMeters, animate = false) {
        if (!this.map) {
            console.error('Map not initialized');
            return Promise.resolve();
        }

        // Calculer le nouveau zoom
        const newZoom = calculateZoomFromRadius(radiusMeters, latitude, this.map);

        // Mettre à jour le cercle de rayon avec les coordonnées sélectionnées
        this.updateRadiusCircle(latitude, longitude, radiusMeters);

        return new Promise((resolve) => {
            // Fonction appelée une seule fois quand le mouvement est terminé
            const handleMoveEnd = () => {
                this.map.off('moveend', handleMoveEnd);
                resolve();
            };

            // Écouter moveend
            this.map.on('moveend', handleMoveEnd);

            // Lancer le mouvement
            if (animate) {
                this.map.flyTo([latitude, longitude], newZoom, {
                    duration: this.options.performance.animationDuration / 1000,
                });
            } else {
                // Zoom instantané ET recentrage sur les coordonnées
                // IMPORTANT: Utiliser setView() et pas juste setZoom() pour recentrer
                this.map.setView([latitude, longitude], newZoom);
            }

            // Vérifier si un mouvement est réellement nécessaire
            // Si coords/zoom identiques, résoudre immédiatement
            setTimeout(() => {
                const currentCenter = this.map.getCenter();
                const currentZoom = this.map.getZoom();

                const noMovement = (
                    Math.abs(currentCenter.lat - latitude) < 0.000001 &&
                    Math.abs(currentCenter.lng - longitude) < 0.000001 &&
                    currentZoom === newZoom
                );

                if (noMovement) {
                    this.map.off('moveend', handleMoveEnd);
                    resolve();
                }
            }, 10);
        });
    }
}
