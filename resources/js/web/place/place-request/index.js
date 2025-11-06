import { PlaceRequestMap } from './map.js';
import { placeRequestForm } from './alpine-form.js';
import { PhotoUploader } from './photo-uploader.js';
import RecaptchaService from '@/shared/recaptcha-service.js';

/**
 * PlaceRequestManager - Main orchestrator for place request form
 *
 * Responsibilities:
 * - Initialize and coordinate Leaflet map
 * - Initialize and coordinate reCAPTCHA v3 (via centralized RecaptchaService)
 * - Register Alpine.js components
 *
 * Architecture:
 * - PlaceRequestMap: manages map and markers with Livewire sync
 * - RecaptchaService: centralized reCAPTCHA service (singleton)
 * - alpine-form.js: Alpine component for form submission with reCAPTCHA
 * - Validation scroll: handled by Alpine.js in place-request-form.blade.php
 * - index.js: orchestrates the modules
 */

// Register Alpine component - Pattern safe pour timing d'initialisation
const registerAlpineComponent = () => {
    if (window.Alpine && typeof window.Alpine.data === 'function') {
        window.Alpine.data('placeRequestForm', placeRequestForm);
    } else {
        console.warn('⚠️ Alpine.data not available');
    }
};

// Si Alpine est déjà initialisé, enregistrer directement
if (window.Alpine) {
    registerAlpineComponent();
} else {
    // Sinon, attendre l'événement alpine:init
    document.addEventListener('alpine:init', registerAlpineComponent);
}

// Initialize map and reCAPTCHA after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize only on place request page
    const mapContainer = document.getElementById('placeRequestMap');

    if (mapContainer) {
        new PlaceRequestManager();
    }
});

class PlaceRequestManager {
    constructor() {
        this.map = null;
        this.recaptcha = null;
        this.photoUploader = null;
        this.init();
    }

    init() {
        // Initialize Leaflet map
        this.initializeMap();

        // Initialize reCAPTCHA (centralized service)
        this.initializeRecaptcha();

        // Initialize photo uploader
        this.initializePhotoUploader();
    }

    /**
     * Initialize Leaflet map
     */
    initializeMap() {
        this.map = new PlaceRequestMap('placeRequestMap');

        if (!this.map.map) {
            console.warn('⚠️ Failed to initialize map');
        }
    }

    /**
     * Initialize reCAPTCHA v3 (centralized service)
     */
    initializeRecaptcha() {
        this.recaptcha = RecaptchaService;

        const success = this.recaptcha.init();

        if (!success) {
            console.warn('⚠️ Failed to initialize reCAPTCHA');
        } else {
            console.log('✅ reCAPTCHA initialized for PlaceRequest');
        }
    }

    /**
     * Initialize photo uploader
     */
    initializePhotoUploader() {
        try {
            this.photoUploader = new PhotoUploader('#photo-drop-zone', '#pendingPhotos');
        } catch (error) {
            console.error('✗ PhotoUploader failed:', error);
        }
    }
}
