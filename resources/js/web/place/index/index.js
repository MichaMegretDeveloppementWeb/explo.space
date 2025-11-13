import { initializeGeolocation } from '../../../components/geolocation.js';
import { PlaceMap } from './map/map.js';
import { PlaceMapMarkers } from './map/map-markers.js';
import { PlaceMapSync } from './map/map-sync.js';
import { calculateZoomFromRadius } from './map/map-zoom-calculator.js';

/**
 * PlaceExplorer - Orchestrateur principal de la page d'exploration
 *
 * Responsabilités :
 * - Calcul dynamique de la hauteur de la navbar
 * - Initialisation et orchestration de la carte Leaflet (workflow async séquentiel)
 * - Gestion des événements carte → Livewire (bounding box, zoom, déplacement)
 * - Synchronisation bidirectionnelle carte ↔ liste des résultats
 * - Configuration des listeners Livewire pour les actions utilisateur
 *
 * Architecture :
 * - Workflow async garanti : PlaceList ready → emit initial bounds → setup listeners
 * - Double debounce sur les changements de carte (300ms interne + 800ms final)
 * - Aucun listener configuré avant l'émission de la boundingBox initiale (évite race conditions)
 * - Séparation des responsabilités : PlaceMap gère la carte, index.js gère les événements
 *
 * Note : L'interactivité (filtres, bottom sheet mobile, etc.) est gérée
 * par les composants Livewire et Alpine.js directement dans les vues Blade.
 */

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    initializeGeolocation();

    // Initialiser PlaceExplorer seulement sur la page explorer
    if (document.getElementById('place-explorer-container')) {
        new PlaceExplorer();
    }

});

class PlaceExplorer {
    constructor() {
        this.navbarHeight = 64; // Hauteur par défaut
        this.placeMap = null;
        this.mapMarkers = null;
        this.mapSync = null;
        this.isInitializing = true; // Flag pour ignorer événements pendant l'init
        this.isFilterChangeInProgress = false; // Flag pour éviter doubles événements
        this.lastDispatchedBounds = null; // Dernière bounding box dispatchée (éviter requêtes redondantes)
        this.internalDebounceTimer = null; // Premier debounce (300ms)
        this.finalDebounceTimer = null; // Second debounce (800ms)
        this.isListUpdateInProgress = false; // Flag pour empêcher dispatch pendant mise à jour en cours
        this.pendingListUpdate = null; // Stocke la dernière bounding box en attente si update en cours
        this.init();
    }

    async init() {
        this.setupNavbarHeight();
        this.setupLivewireListeners(); // ⚠️ AVANT initializeMap() pour écouter initial-map-bounds
        await this.initializeMap();
    }


    /**
     * Calculer et appliquer dynamiquement la hauteur de la navbar
     *
     * Cette méthode :
     * 1. Détecte automatiquement la navbar (plusieurs sélecteurs possibles)
     * 2. Calcule sa hauteur réelle
     * 3. Applique calc(100vh - hauteurNavbar) au conteneur principal
     * 4. Expose la hauteur via variable CSS --navbar-height
     * 5. Se recalcule automatiquement lors du resize et au load complet
     */
    setupNavbarHeight() {
        const updateNavbarHeight = () => {
            // Essayer plusieurs sélecteurs pour trouver la navbar
            const navbar = document.querySelector('nav') ||
                document.querySelector('[data-navbar]') ||
                document.querySelector('header') ||
                document.querySelector('.navbar');

            if (navbar) {
                this.navbarHeight = navbar.offsetHeight;
            } else {
                // Fallback si pas de navbar trouvée
                this.navbarHeight = 64;
            }

            // Appliquer la hauteur au conteneur principal
            const container = document.getElementById('place-explorer-container');
            if (container) {
                container.style.height = `calc(100vh - ${this.navbarHeight}px)`;
                document.documentElement.style.setProperty('--navbar-height', this.navbarHeight + 'px');
            }
        };

        // Initialisation
        updateNavbarHeight();

        // Re-calculer lors du redimensionnement
        window.addEventListener('resize', updateNavbarHeight);

        // Re-calculer après chargement complet (images, fonts, etc.)
        window.addEventListener('load', updateNavbarHeight);
    }


    /**
     * Initialiser la carte Leaflet avec workflow async séquentiel
     *
     * Workflow garanti :
     * 1. Créer et initialiser la carte
     * 2. whenReady() : Appliquer vue + créer marqueurs/sync + afficher carte
     * 3. waitForPlaceList() : Attendre que PlaceList soit prêt
     * 4. emitInitialBounds() : Émettre boundingBox initiale (après stabilisation)
     * 5. setupMapChangeListeners() : Configurer les listeners (APRÈS initial bounds)
     *
     * Lit les attributs data-* du conteneur de carte pour obtenir :
     * - latitude, longitude, radius
     * - liste initiale des lieux (coordonnées pour les marqueurs)
     * - mode bounding box (activé/désactivé)
     */
    async initializeMap() {
        const mapContainer = document.getElementById('place-map');
        const mapLoader = document.getElementById('map-loader');

        if (!mapContainer) {
            console.warn('⚠️ Map container not found');
            return;
        }

        // Lire les données initiales depuis les attributs data-*
        const initialLat = parseFloat(mapContainer.dataset.latitude) || null;
        const initialLng = parseFloat(mapContainer.dataset.longitude) || null;
        const initialRadius = parseInt(mapContainer.dataset.radius, 10) || null;
        const places = this.parseMapPlaces(mapContainer.dataset.places);
        const useBoundingBox = mapContainer.dataset.useBoundingBox === 'true';

        // Créer l'instance de la carte (carte masquée avec opacity-0)
        this.placeMap = new PlaceMap('place-map');
        this.placeMap.init(initialLat, initialLng, null, useBoundingBox);

        // Attendre que la carte soit complètement prête
        await new Promise((resolve) => {
            this.placeMap.getMap().whenReady(async () => {
                // Si on a coordonnées + radius, calculer et appliquer le zoom optimal (mode proximity)
                if (initialLat && initialLng && initialRadius) {
                    const optimalZoom = calculateZoomFromRadius(initialRadius, initialLat, this.placeMap.getMap());
                    this.placeMap.getMap().setView([initialLat, initialLng], optimalZoom, { animate: false });
                    this.placeMap.showRadiusCircle(initialLat, initialLng, initialRadius);
                } else {
                    // Mode worldwide : appliquer la vue mondiale sans animation ET attendre sa fin
                    await this.placeMap.showWorldView(false);
                }

                // Créer les gestionnaires de marqueurs et sync
                this.mapMarkers = new PlaceMapMarkers(this.placeMap);
                this.mapSync = new PlaceMapSync(this.placeMap, this.mapMarkers);
                this.mapSync.init('place-list-results');

                // Charger les marqueurs selon le mode
                if (useBoundingBox) {
                    // Mode A : Bounding box dynamique ACTIVÉ
                    const bounds = this.placeMap.getMap().getBounds();
                    const boundingBox = {
                        north: bounds.getNorth(),
                        south: bounds.getSouth(),
                        east: bounds.getEast(),
                        west: bounds.getWest()
                    };

                    if (window.Livewire) {
                        window.Livewire.dispatch('initial-map-bounds', {boundingBox});
                    }
                } else {
                    // Mode B : Bounding box dynamique DÉSACTIVÉ
                    if (places && places.length > 0) {
                        this.mapMarkers.updateMarkers(places);
                    }
                }

                // Afficher la carte avec fade-in et masquer le loader
                setTimeout(() => {
                    mapContainer.classList.remove('opacity-0');
                    mapContainer.classList.add('opacity-100');
                    if (mapLoader) {
                        mapLoader.classList.add('opacity-0');
                        setTimeout(() => {
                            mapLoader.style.display = 'none';
                        }, 300);
                    }
                }, 100);

                resolve();
            });
        });

        // === WORKFLOW ASYNC SÉQUENTIEL GARANTI ===

        // 1. Attendre que PlaceList soit prêt
        await this.waitForPlaceList();

        // 2. Émettre la boundingBox initiale (après stabilisation de la carte)
        this.emitInitialBounds();

        // 3. Configurer les listeners pour les changements futurs
        this.setupMapChangeListeners();

        // 4. Désactiver le flag d'initialisation après un délai de sécurité
        // Permet d'ignorer les événements moveend/zoomend tardifs émis par Leaflet
        setTimeout(() => {
            this.isInitializing = false;
        }, 200);
    }


    /**
     * Parse la liste des lieux depuis l'attribut data-places
     *
     * @param {string} placesJson - JSON string
     * @returns {Array}
     */
    parseMapPlaces(placesJson) {
        if (!placesJson) {
            return [];
        }

        try {
            return JSON.parse(placesJson);
        } catch (e) {
            console.error('Failed to parse places data', e);
            return [];
        }
    }


    /**
     * Attend que le composant Livewire PlaceList soit complètement chargé et prêt
     *
     * Utilise un MutationObserver pour détecter l'apparition de l'élément #place-list-results
     * (unique au composant PlaceList) dans le DOM. Plus performant qu'un polling setInterval.
     *
     * @returns {Promise<void>}
     */
    async waitForPlaceList() {
        return new Promise((resolve) => {
            // Vérifier si PlaceList est déjà présent (cas où il se charge très vite)
            const existingElement = document.getElementById('place-list-results');
            if (existingElement) {
                setTimeout(resolve, 100);
                return;
            }

            const maxWaitTime = 5000;
            const startTime = Date.now();
            let timeoutId = null;

            // MutationObserver pour détecter l'apparition de PlaceList
            const observer = new MutationObserver((mutations, obs) => {
                const placeListElement = document.getElementById('place-list-results');

                if (placeListElement) {
                    obs.disconnect();
                    clearTimeout(timeoutId);
                    setTimeout(resolve, 100);
                    return;
                }

                if (Date.now() - startTime > maxWaitTime) {
                    obs.disconnect();
                    clearTimeout(timeoutId);
                    console.warn('⚠️ PlaceList component not found after 5s');
                    resolve();
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            timeoutId = setTimeout(() => {
                observer.disconnect();
                const placeListElement = document.getElementById('place-list-results');
                if (!placeListElement) {
                    console.warn('⚠️ PlaceList component not found after timeout');
                }
                resolve();
            }, maxWaitTime);
        });
    }


    /**
     * Émet la boundingBox initiale vers PlaceList
     *
     * Cette méthode :
     * 1. Récupère les bounds actuelles de la carte (déjà stabilisée par showWorldView())
     * 2. Émet l'événement 'initial-list-bounds' vers Livewire
     *
     * @returns {Promise<void>}
     */
    async emitInitialBounds() {
        const bounds = this.placeMap.getMap().getBounds();
        const boundingBox = {
            north: bounds.getNorth(),
            south: bounds.getSouth(),
            east: bounds.getEast(),
            west: bounds.getWest()
        };

        if (window.Livewire) {
            window.Livewire.dispatch('initial-list-bounds', { boundingBox });
        }
    }


    /**
     * Configure l'écouteur UNIQUE de changements de bounds pour PlaceList
     *
     * Double debounce :
     * - 300ms : debounce interne (éviter trop d'appels pendant le mouvement)
     * - 800ms : debounce final avant émission Livewire (réduire requêtes backend)
     *
     * Protections :
     * - Ignore les événements pendant l'initialisation (isInitializing = true)
     * - Ignore les événements pendant les changements de filtres (isFilterChangeInProgress = true)
     * - Ignore les micro-mouvements via comparaison de bounding box
     */
    setupMapChangeListeners() {
        this.placeMap.getMap().on('moveend zoomend', () => {
            // Ignorer pendant l'initialisation (évite moveend tardifs de Leaflet)
            if (this.isInitializing) {
                return;
            }

            // Ignorer si changement de filtres en cours
            if (this.isFilterChangeInProgress) {
                return;
            }

            // Premier debounce : 300ms
            clearTimeout(this.internalDebounceTimer);

            this.internalDebounceTimer = setTimeout(() => {
                // Second debounce : 800ms
                clearTimeout(this.finalDebounceTimer);

                this.finalDebounceTimer = setTimeout(() => {
                    this.dispatchBoundingBoxChanged();
                }, 600);
            }, 200);
        });
    }



    /**
     * Dispatcher le changement de bounding box vers Livewire
     *
     * Optimisations :
     * - Ne dispatch que si la bounding box a significativement changé (>0.0001° ≈ 11m)
     * - Empêche les dispatchs multiples si une mise à jour est déjà en cours
     * - Stocke la dernière demande en attente pour la traiter après la mise à jour actuelle
     */
    dispatchBoundingBoxChanged(){
        const bounds = this.placeMap.getMap().getBounds();
        const boundingBox = {
            north: bounds.getNorth(),
            south: bounds.getSouth(),
            east: bounds.getEast(),
            west: bounds.getWest()
        };

        // Vérifier si les bounds ont significativement changé (>0.0001° ≈ 11m)
        if (this.lastDispatchedBounds) {
            const hasSignificantChange = (
                Math.abs(this.lastDispatchedBounds.north - boundingBox.north) > 0.0001 ||
                Math.abs(this.lastDispatchedBounds.south - boundingBox.south) > 0.0001 ||
                Math.abs(this.lastDispatchedBounds.east - boundingBox.east) > 0.0001 ||
                Math.abs(this.lastDispatchedBounds.west - boundingBox.west) > 0.0001
            );

            if (!hasSignificantChange) {
                return; // Micro-mouvement, ignorer
            }
        }

        // ⚠️ Si une mise à jour de liste est déjà en cours, stocker cette demande en attente
        if (this.isListUpdateInProgress) {
            this.pendingListUpdate = boundingBox; // Écrase les demandes précédentes
            return; // Ne pas dispatcher maintenant
        }

        // ✅ Dispatcher immédiatement (aucune mise à jour en cours)
        this.actuallyDispatchListUpdate(boundingBox);
    }

    /**
     * Dispatcher réellement la mise à jour de liste vers Livewire
     *
     * Méthode centralisée utilisée pour :
     * - Les mouvements de carte (boundingBox seul)
     * - Les changements de filtres (boundingBox + filters)
     *
     * @param {Object} boundingBox - Bounding box à dispatcher
     * @param {Object|null} filters - Filtres optionnels (null si mouvement carte)
     */
    actuallyDispatchListUpdate(boundingBox, filters = null) {
        // Marquer qu'une mise à jour est en cours
        this.isListUpdateInProgress = true;

        // Sauvegarder les bounds actuelles
        this.lastDispatchedBounds = { ...boundingBox };

        if (window.Livewire) {
            const mapContainer = document.getElementById('place-map');
            const useBoundingBox = mapContainer?.dataset.useBoundingBox === 'true';

            // Dispatcher avec ou sans filtres selon le contexte
            const payload = filters
                ? { boundingBox, filters }
                : { boundingBox };

            window.Livewire.dispatch('update-list-bounds', payload);

            if (useBoundingBox) {
                window.Livewire.dispatch('update-map-bounds', payload);
            }
        }
    }



    /**
     * Configure les écouteurs d'événements Livewire
     *
     * Événements écoutés :
     * - coordinates-updated : Livewire envoie de nouvelles coordonnées
     * - map-view-changed : Changer la vue de la carte (zoom, centre)
     * - map-resize-needed : Recalculer la taille de la carte
     */
    setupLivewireListeners() {
        const registerListeners = () => {
            // Mise à jour des marqueurs depuis Livewire
            Livewire.on('coordinates-updated', (event) => {
                const coordinates = event.coordinates || event[0]?.coordinates || [];

                if (this.mapMarkers) {
                    this.mapMarkers.updateMarkers(coordinates);
                }
            });

            // Synchronisation filtres + vue
            Livewire.on('sync-filters-view', (event) => {
                const { viewAction, filters, coordinates, useBoundingBox } = event[0] || event;
                this.handleFilterChange(viewAction, filters, coordinates, useBoundingBox);
            });

            // 🔑 Écouter la fin de mise à jour de PlaceList
            Livewire.on('list-update-complete', () => {
                this.isListUpdateInProgress = false;

                // Si une mise à jour était en attente, la lancer maintenant
                if (this.pendingListUpdate) {
                    const pending = this.pendingListUpdate;
                    this.pendingListUpdate = null;

                    // Gérer les deux cas : objet simple (mouvement carte) ou objet avec filters (changement filtres)
                    if (pending.filters !== undefined) {
                        // Cas changement de filtres : { boundingBox, filters }
                        this.actuallyDispatchListUpdate(pending.boundingBox, pending.filters);
                    } else {
                        // Cas mouvement de carte : boundingBox directement
                        this.actuallyDispatchListUpdate(pending);
                    }
                }
            });
        };

        // Écouter le toggle de la sidebar (desktop uniquement)
        // Redimensionne la carte après l'animation de collapse/expand
        window.addEventListener('sidebar:toggled', () => {
            if (this.placeMap) {
                this.placeMap.invalidateSize();
            }
        });

        // Gérer les deux cas : Livewire déjà initialisé OU pas encore initialisé
        if (window.Livewire) {
            // Cas 1 : Livewire déjà initialisé, enregistrer immédiatement
            registerListeners();
        } else {
            // Cas 2 : Livewire pas encore initialisé, attendre l'événement
            document.addEventListener('livewire:init', registerListeners);
        }
    }


    /**
     * Gérer le changement de filtres avec synchronisation complète
     *
     * @param {Object} viewAction - Action de vue à appliquer
     * @param {Object} filters - Filtres validés
     * @param {Array|null} coordinates - Coordonnées si useBoundingBox = false
     * @param {boolean} useBoundingBox - Mode de chargement
     */
    async handleFilterChange(viewAction, filters, coordinates, useBoundingBox) {
        try {
            clearTimeout(this.internalDebounceTimer);
            clearTimeout(this.finalDebounceTimer);
            this.isFilterChangeInProgress = true;

            if (!useBoundingBox && coordinates) {
                this.mapMarkers.updateMarkers(coordinates);
            }

            if (viewAction.action === 'center-on-location') {
                await this.placeMap.centerOnLocation(
                    viewAction.latitude,
                    viewAction.longitude,
                    viewAction.radius,
                    true
                );
            } else if (viewAction.action === 'adjust-zoom') {
                await this.placeMap.adjustZoomToRadius(
                    viewAction.latitude,
                    viewAction.longitude,
                    viewAction.radius,
                    false
                );
            } else if (viewAction.action === 'show-world-view') {
                await this.placeMap.showWorldView(true);
            }

            const bounds = this.placeMap.getMap().getBounds();
            const boundingBox = {
                north: bounds.getNorth(),
                south: bounds.getSouth(),
                east: bounds.getEast(),
                west: bounds.getWest()
            };

            // ⚠️ Si une mise à jour de liste est déjà en cours, stocker cette demande en attente
            if (this.isListUpdateInProgress) {
                // Stocker la boundingBox ET les filtres pour traitement ultérieur
                this.pendingListUpdate = { boundingBox, filters };
            } else {
                // ✅ Utiliser la méthode centralisée avec filtres
                this.actuallyDispatchListUpdate(boundingBox, filters);
            }

            setTimeout(() => {
                this.isFilterChangeInProgress = false;
            }, 500);

        } catch (error) {
            console.error('❌ Error in handleFilterChange:', error);
            this.isFilterChangeInProgress = false;
        }
    }

}
