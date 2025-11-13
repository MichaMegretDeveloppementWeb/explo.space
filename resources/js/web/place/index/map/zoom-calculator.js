/**
 * Zoom Calculator for Place Exploration Map
 *
 * Calcule le niveau de zoom approprié en fonction du rayon de recherche
 * pour offrir une vue optimale de la zone d'exploration.
 *
 * Logique :
 * - Plus le rayon est grand, plus le zoom doit être faible (vue éloignée)
 * - Plus le rayon est petit, plus le zoom doit être élevé (vue rapprochée)
 *
 * Limites Leaflet :
 * - Min zoom: 2 (vue mondiale très éloignée)
 * - Max zoom: 18 (vue très rapprochée, niveau rue)
 *
 * @see docs/leaflet_architecture.md Section 3.2 pour la configuration des limites
 */

/**
 * Configuration du calculateur de zoom
 */
const ZOOM_CONFIG = {
    // Limites Leaflet
    MIN_ZOOM: 2,
    MAX_ZOOM: 18,

    // Table de correspondance rayon (mètres) → niveau de zoom optimal
    // Calibrée pour offrir une vue confortable incluant la zone de recherche
    RADIUS_TO_ZOOM: [
        { radiusMeters: 1000, zoom: 14 },      // 1 km → zoom 14 (quartier)
        { radiusMeters: 5000, zoom: 12 },      // 5 km → zoom 12 (ville)
        { radiusMeters: 10000, zoom: 11 },     // 10 km → zoom 11 (grande ville)
        { radiusMeters: 25000, zoom: 10 },     // 25 km → zoom 10 (agglomération)
        { radiusMeters: 50000, zoom: 9 },      // 50 km → zoom 9 (métropole)
        { radiusMeters: 100000, zoom: 8 },     // 100 km → zoom 8 (région)
        { radiusMeters: 200000, zoom: 7 },     // 200 km → zoom 7 (grande région)
        { radiusMeters: 500000, zoom: 6 },     // 500 km → zoom 6 (pays moyen)
        { radiusMeters: 1000000, zoom: 5 },    // 1000 km → zoom 5 (grand pays)
        { radiusMeters: 1500000, zoom: 4 },    // 1500 km → zoom 4 (continent)
    ],

    // Zoom par défaut pour vue mondiale (mode worldwide)
    WORLD_VIEW_ZOOM: 3,
};

/**
 * Calculer le niveau de zoom optimal pour un rayon donné
 *
 * Utilise une interpolation linéaire entre les paliers définis dans RADIUS_TO_ZOOM
 * pour obtenir un niveau de zoom fluide et progressif.
 *
 * @param {number} radiusMeters - Rayon de recherche en mètres
 * @returns {number} Niveau de zoom calculé (entre MIN_ZOOM et MAX_ZOOM)
 *
 * @example
 * calculateZoomForRadius(200000) // → 7 (rayon 200km = région)
 * calculateZoomForRadius(150000) // → ~7.5 (interpolation entre 100km et 200km)
 */
export function calculateZoomForRadius(radiusMeters) {
    // Valider l'input
    if (!radiusMeters || radiusMeters <= 0) {
        console.warn('Invalid radius for zoom calculation:', radiusMeters);
        return ZOOM_CONFIG.WORLD_VIEW_ZOOM;
    }

    const table = ZOOM_CONFIG.RADIUS_TO_ZOOM;

    // Si le rayon est inférieur ou égal au plus petit palier → zoom max de la table
    if (radiusMeters <= table[0].radiusMeters) {
        return Math.min(table[0].zoom, ZOOM_CONFIG.MAX_ZOOM);
    }

    // Si le rayon est supérieur ou égal au plus grand palier → zoom min de la table
    if (radiusMeters >= table[table.length - 1].radiusMeters) {
        return Math.max(table[table.length - 1].zoom, ZOOM_CONFIG.MIN_ZOOM);
    }

    // Interpolation linéaire entre les deux paliers encadrants
    for (let i = 0; i < table.length - 1; i++) {
        const lower = table[i];
        const upper = table[i + 1];

        if (radiusMeters >= lower.radiusMeters && radiusMeters <= upper.radiusMeters) {
            // Calcul de l'interpolation linéaire
            const ratio = (radiusMeters - lower.radiusMeters) / (upper.radiusMeters - lower.radiusMeters);
            const zoomDiff = upper.zoom - lower.zoom;
            const calculatedZoom = lower.zoom + ratio * zoomDiff;

            // Arrondir à 1 décimale pour fluidité
            return Math.round(calculatedZoom * 10) / 10;
        }
    }

    // Fallback (ne devrait jamais arriver)
    console.warn('Zoom calculation fallback for radius:', radiusMeters);
    return ZOOM_CONFIG.WORLD_VIEW_ZOOM;
}

/**
 * Obtenir le zoom pour la vue mondiale
 *
 * @returns {number} Niveau de zoom pour vue mondiale
 */
export function getWorldViewZoom() {
    return ZOOM_CONFIG.WORLD_VIEW_ZOOM;
}

/**
 * Valider qu'un niveau de zoom est dans les limites Leaflet
 *
 * @param {number} zoom - Niveau de zoom à valider
 * @returns {number} Niveau de zoom clamped entre MIN_ZOOM et MAX_ZOOM
 */
export function clampZoom(zoom) {
    return Math.max(ZOOM_CONFIG.MIN_ZOOM, Math.min(zoom, ZOOM_CONFIG.MAX_ZOOM));
}

/**
 * Obtenir les informations de configuration pour debugging
 *
 * @returns {object} Configuration du calculateur
 */
export function getZoomConfig() {
    return ZOOM_CONFIG;
}
