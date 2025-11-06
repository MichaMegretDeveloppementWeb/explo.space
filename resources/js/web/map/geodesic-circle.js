/**
 * Helper pour créer un cercle géodésique avec leaflet.geodesic
 *
 * Calcule les points d'un cercle sur une sphère (Earth) en utilisant
 * la formule de destination géodésique (Haversine inverse)
 */

import L from 'leaflet';
import 'leaflet.geodesic';

/**
 * Crée un cercle géodésique (distance réelle sur la sphère terrestre)
 *
 * @param {Array} center - [latitude, longitude] du centre
 * @param {number} radiusMeters - Rayon en mètres
 * @param {Object} options - Options de style Leaflet
 * @param {number} steps - Nombre de points pour le cercle (défaut adaptatif selon rayon)
 * @returns {L.Geodesic} Polygone géodésique représentant le cercle
 */
export function createGeodesicCircle(center, radiusMeters, options = {}, steps = null) {
    const [latitude, longitude] = center;
    const points = [];

    // Calculer automatiquement le nombre de points selon le rayon si non spécifié
    // Plus le rayon est grand, plus on a besoin de points pour un cercle lisse
    if (steps === null) {
        const radiusKm = radiusMeters / 1000;
        if (radiusKm <= 500) {
            steps = 64;   // Petits cercles : 64 points suffisent
        } else if (radiusKm <= 2000) {
            steps = 96;   // Moyens cercles : 96 points pour plus de précision
        } else if (radiusKm <= 5000) {
            steps = 128;  // Grands cercles : 128 points
        } else {
            steps = 180;  // Très grands cercles : 180 points (maximum)
        }
    }

    // Rayon de la Terre en mètres (utiliser le rayon moyen pour précision)
    const earthRadius = 6371000;

    // Distance angulaire en radians
    const angularDistance = radiusMeters / earthRadius;

    // Latitude et longitude du centre en radians
    const lat1 = latitude * Math.PI / 180;
    const lon1 = longitude * Math.PI / 180;

    // Calculer les points du cercle
    // IMPORTANT : i < steps (pas <=) pour éviter la duplication du point 0°/360°
    // qui causerait des artefacts de remplissage (motifs transparents)
    for (let i = 0; i < steps; i++) {
        // Angle de direction (bearing) de 0° à 359.x°
        const bearing = (360 / steps) * i;
        const bearingRad = bearing * Math.PI / 180;

        // Formule de destination géodésique (Haversine inverse)
        // Calcule le point à distance angulaire dans la direction bearing
        const lat2 = Math.asin(
            Math.sin(lat1) * Math.cos(angularDistance) +
            Math.cos(lat1) * Math.sin(angularDistance) * Math.cos(bearingRad)
        );

        const lon2 = lon1 + Math.atan2(
            Math.sin(bearingRad) * Math.sin(angularDistance) * Math.cos(lat1),
            Math.cos(angularDistance) - Math.sin(lat1) * Math.sin(lat2)
        );

        // Convertir en degrés
        let latDeg = lat2 * 180 / Math.PI;
        let lonDeg = lon2 * 180 / Math.PI;

        // IMPORTANT: Normaliser la longitude dans [-180, 180]
        // Sans cette normalisation, les longitudes peuvent dépasser ±180° et causer des artefacts
        while (lonDeg > 180) lonDeg -= 360;
        while (lonDeg < -180) lonDeg += 360;

        // Contraindre la latitude dans [-90, 90] (cas extrêmes près des pôles)
        latDeg = Math.max(-90, Math.min(90, latDeg));

        points.push([latDeg, lonDeg]);
    }

    // Créer un polygone Leaflet standard avec les points géodésiques calculés
    // Note: On utilise L.polygon() au lieu de L.geodesic() pour éviter les artefacts
    // de remplissage. Les points sont calculés géodésiquement (distance réelle),
    // mais reliés par des lignes droites Mercator. Avec 64-96 points, la différence
    // visuelle est imperceptible, mais le remplissage est stable.
    return L.polygon(points, {
        color: options.color || '#3b82f6',
        fillColor: options.fillColor || '#3b82f6',
        fillOpacity: options.fillOpacity !== undefined ? options.fillOpacity : 0.1,
        weight: options.weight || 2,
        opacity: options.opacity !== undefined ? options.opacity : 0.6,
        smoothFactor: 1,  // Simplification des lignes (1 = aucune simplification)
    });
}
