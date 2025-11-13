/**
 * Utilitaires pour créer des icônes de marqueurs Leaflet custom (SVG)
 *
 * Permet d'éviter la dépendance aux images PNG natives de Leaflet
 * et d'assurer la cohérence visuelle sur toute l'application.
 */

import L from 'leaflet';

/**
 * Initialise les définitions SVG globales (filtres d'ombre)
 * À appeler une seule fois au chargement de l'application
 */
export function initSvgDefs() {
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
        </defs>
    `;

    // Ajouter au body
    document.body.appendChild(svg);
}

/**
 * Crée une icône bleue avec point blanc pour les lieux normaux
 * Style identique à celui utilisé sur /explore
 *
 * @param {number} width - Largeur de l'icône (défaut: 27)
 * @param {number} height - Hauteur de l'icône (défaut: 42)
 * @returns {L.DivIcon}
 */
export function createNormalMarkerIcon(width = 27, height = 42) {
    // Initialiser les SVG defs si pas encore fait
    initSvgDefs();

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
 * @param {number} width - Largeur de l'icône (défaut: 27)
 * @param {number} height - Hauteur de l'icône (défaut: 42)
 * @returns {L.DivIcon}
 */
export function createFeaturedMarkerIcon(width = 27, height = 42) {
    // Initialiser les SVG defs si pas encore fait
    initSvgDefs();

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
 * Crée une icône rouge pour les anciennes coordonnées (EditRequest)
 * Utilisé pour afficher les coordonnées originales avant modification
 *
 * @param {number} width - Largeur de l'icône (défaut: 27)
 * @param {number} height - Hauteur de l'icône (défaut: 42)
 * @returns {L.DivIcon}
 */
export function createOldMarkerIcon(width = 27, height = 42) {
    // Initialiser les SVG defs si pas encore fait
    initSvgDefs();

    const svg = `
        <svg width="${width}" height="${height}" viewBox="-1 0 28 41" xmlns="http://www.w3.org/2000/svg">
            <!-- Pin rouge -->
            <path d="M12.5 0C5.6 0 0 5.6 0 12.5c0 1.9 0.4 3.7 1.2 5.3l11.3 23.2l11.3-23.2c0.8-1.6 1.2-3.4 1.2-5.3C25 5.6 19.4 0 12.5 0z"
                  fill="#ef4444"
                  stroke="#ffffff00"
                  stroke-width="1.5"
                  filter="url(#marker-shadow-normal)"/>

            <!-- Point blanc au centre -->
            <circle cx="12.5" cy="12.5" r="5" fill="white"/>
        </svg>
    `;

    return L.divIcon({
        html: svg,
        className: 'old-marker-icon',
        iconSize: [width, height],
        iconAnchor: [width/2, height],
        popupAnchor: [0, -height + 5],
    });
}
