/**
 * Point d'entrée pour les scripts de la page de demande de modification
 * Ce fichier est chargé via Vite depuis la vue principale place show
 */

import { initEditRequestMap } from './map.js';
import RecaptchaService from '@/shared/recaptcha-service.js';
import { editRequestForm } from './alpine-form.js';

// Exposer le composant Alpine.js globalement
window.editRequestForm = editRequestForm;

// Variable pour stocker l'instance de la carte
let mapInstance = null;
let mapObserver = null;

// Fonction pour initialiser la carte
function initMap() {
    const mapContainer = document.getElementById('edit-request-map');

    if (mapContainer && !mapContainer.dataset.initialized) {
        console.log('✅ Initializing edit request map');
        mapInstance = initEditRequestMap(mapContainer);
        mapContainer.dataset.initialized = 'true';

        // Déconnecter l'observer une fois la carte initialisée
        if (mapObserver) {
            mapObserver.disconnect();
            mapObserver = null;
        }
    }
}

// Fonction pour observer l'apparition du conteneur de la carte
function observeMapContainer() {
    // Si un observer existe déjà, le déconnecter
    if (mapObserver) {
        mapObserver.disconnect();
    }

    // Créer un MutationObserver qui surveille l'apparition du conteneur
    mapObserver = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            // Vérifier les nœuds ajoutés
            for (const node of mutation.addedNodes) {
                // Si c'est un élément HTML
                if (node.nodeType === Node.ELEMENT_NODE) {
                    // Vérifier si c'est le conteneur de la carte
                    if (node.id === 'edit-request-map') {
                        console.log('📍 Map container detected in DOM');
                        initMap();
                        return;
                    }
                    // Vérifier si le conteneur est dans les enfants du nœud ajouté
                    if (node.querySelector && node.querySelector('#edit-request-map')) {
                        console.log('📍 Map container detected in DOM (nested)');
                        initMap();
                        return;
                    }
                }
            }
        }
    });

    // Observer le document entier pour les changements dans le DOM
    mapObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

    console.log('👀 Map observer started');
}

// Initialiser reCAPTCHA (centralized service) et écouter le checkbox coordinates
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser le service reCAPTCHA centralisé
    const success = RecaptchaService.init();

    if (!success) {
        console.warn('⚠️ Failed to initialize reCAPTCHA for EditRequest');
    } else {
        console.log('✅ reCAPTCHA initialized for EditRequest');
    }

    // Écouter les changements sur le checkbox "coordinates"
    document.addEventListener('change', (event) => {
        // Vérifier si c'est bien le checkbox coordinates
        if (event.target.type === 'checkbox' && event.target.value === 'coordinates') {
            if (event.target.checked) {
                console.log('📍 Coordinates checkbox checked');

                // Vérifier si la carte existe déjà (cas où elle est déjà dans le DOM)
                const existingMap = document.getElementById('edit-request-map');
                if (existingMap) {
                    initMap();
                } else {
                    // Sinon, démarrer l'observer pour détecter quand elle apparaîtra
                    observeMapContainer();
                }
            }
        }
    });
});
