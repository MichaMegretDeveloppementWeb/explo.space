/**
 * Calculateur de zoom pour Leaflet en fonction du rayon de recherche
 *
 * Utilise la vraie géométrie de la carte pour calculer le zoom optimal
 * qui affiche correctement le cercle de recherche à l'écran.
 */

import L from 'leaflet';
import { createGeodesicCircle } from './geodesic-circle.js';

/**
 * Calcule le niveau de zoom optimal pour afficher un rayon donné
 * en tenant compte de la taille réelle de la carte à l'écran
 *
 * @param {number} radiusMeters - Rayon en mètres
 * @param {number} latitude - Latitude du centre (pour correction Mercator)
 * @param {L.Map} map - Instance de la carte Leaflet
 * @returns {number} Niveau de zoom optimal (entre 1 et 18)
 */
export function calculateZoomFromRadius(radiusMeters, latitude, map) {
    // Récupérer les dimensions réelles de la carte en pixels
    const mapSize = map.getSize();
    const mapWidthPixels = mapSize.x;
    const mapHeightPixels = mapSize.y;

    // Utiliser la plus petite dimension pour garantir que le cercle est visible
    const minDimension = Math.min(mapWidthPixels, mapHeightPixels);

    // On veut que le diamètre du cercle occupe ~80% de la plus petite dimension
    // (pour laisser de l'espace autour)
    const targetPixels = minDimension * 0.8;
    const targetRadiusPixels = targetPixels / 2;

    // Calculer le nombre de mètres par pixel à différents niveaux de zoom
    // en tenant compte de la latitude (projection Mercator)
    // Formule : metersPerPixel = (40075016.686 * Math.abs(Math.cos(lat * Math.PI / 180))) / Math.pow(2, zoom + 8)

    // On cherche le zoom où radiusMeters correspond à targetRadiusPixels
    // radiusMeters = metersPerPixel * targetRadiusPixels
    // radiusMeters = (40075016.686 * Math.abs(Math.cos(lat * Math.PI / 180))) / Math.pow(2, zoom + 8) * targetRadiusPixels
    // Math.pow(2, zoom + 8) = (40075016.686 * Math.abs(Math.cos(lat * Math.PI / 180)) * targetRadiusPixels) / radiusMeters
    // zoom + 8 = log2(...)
    // zoom = log2(...) - 8

    const earthCircumference = 40075016.686; // mètres à l'équateur
    const latRad = latitude * Math.PI / 180;
    const metersPerPixelAtZoom0 = (earthCircumference * Math.abs(Math.cos(latRad))) / 256;

    const zoom = Math.log2((metersPerPixelAtZoom0 * targetRadiusPixels) / radiusMeters);

    // Contraindre entre 1 et 18 (limites raisonnables Leaflet)
    const clampedZoom = Math.max(1, Math.min(18, zoom));

    // Arrondir au niveau de zoom le plus proche
    return Math.round(clampedZoom);
}

/**
 * Crée un cercle géodésique pour représenter le rayon de recherche
 * Utilise un cercle géodésique pour cohérence avec showRadiusCircle() dans map.js
 * (utile pour fitBounds si on veut être encore plus précis)
 *
 * @param {number} latitude
 * @param {number} longitude
 * @param {number} radiusMeters
 * @returns {L.Geodesic}
 */
export function createRadiusCircle(latitude, longitude, radiusMeters) {
    return createGeodesicCircle(
        [latitude, longitude],
        radiusMeters,
        {
            fillOpacity: 0,
            opacity: 0,
        }
        // Nombre de points calculé automatiquement selon le rayon (adaptatif)
    );
}

/**
 * Calcule les bounds d'un cercle (alternative au calcul de zoom)
 *
 * @param {number} latitude
 * @param {number} longitude
 * @param {number} radiusMeters
 * @returns {L.LatLngBounds}
 */
export function getCircleBounds(latitude, longitude, radiusMeters) {
    const circle = createRadiusCircle(latitude, longitude, radiusMeters);
    return circle.getBounds();
}
