/**
 * Configuration responsive pour les cartes Leaflet
 *
 * Fournit des valeurs de configuration qui s'adaptent automatiquement
 * à la taille de l'écran et aux redimensionnements.
 */

const MOBILE_BREAKPOINT = 800; // px

/**
 * État réactif de la configuration
 */
const responsiveConfig = {
    minZoom: 3,
    isMobile: false,
};

/**
 * Callbacks à appeler lors des changements de configuration
 */
const listeners = [];

/**
 * Calculer et mettre à jour la configuration en fonction de la largeur d'écran
 */
function updateConfig() {
    const width = window.innerWidth;
    const wasMobile = responsiveConfig.isMobile;

    responsiveConfig.isMobile = width < MOBILE_BREAKPOINT;
    responsiveConfig.minZoom = responsiveConfig.isMobile ? 1 : 3;

    // Notifier les listeners si la configuration a changé
    if (wasMobile !== responsiveConfig.isMobile) {
        listeners.forEach(callback => callback(responsiveConfig));
    }
}

/**
 * Obtenir le zoom minimum pour la taille d'écran actuelle
 * @returns {number} - Zoom minimum (1 pour mobile, 3 pour desktop)
 */
export function getMinZoom() {
    return responsiveConfig.minZoom;
}

/**
 * Vérifier si on est sur mobile
 * @returns {boolean}
 */
export function isMobile() {
    return responsiveConfig.isMobile;
}

/**
 * Obtenir toute la configuration responsive
 * @returns {Object}
 */
export function getConfig() {
    return { ...responsiveConfig };
}

/**
 * S'abonner aux changements de configuration
 * @param {Function} callback - Fonction appelée lors des changements
 * @returns {Function} - Fonction pour se désabonner
 */
export function onConfigChange(callback) {
    listeners.push(callback);

    // Retourner une fonction de désabonnement
    return () => {
        const index = listeners.indexOf(callback);
        if (index > -1) {
            listeners.splice(index, 1);
        }
    };
}

/**
 * Initialiser la détection responsive
 */
function init() {
    // Configuration initiale
    updateConfig();

    // Écouter les redimensionnements avec debounce
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateConfig, 150);
    });
}

// Auto-initialisation
init();
