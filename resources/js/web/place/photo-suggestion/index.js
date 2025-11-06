/**
 * Photo Suggestion Form - Entry Point
 *
 * Manages initialization of:
 * - Photo uploader with drag & drop (deferred until form is visible)
 * - reCAPTCHA v3 (via centralized RecaptchaService)
 */

import { PhotoUploader } from './photo-uploader';
import RecaptchaService from '@/shared/recaptcha-service.js';

/**
 * Manager class to orchestrate photo suggestion form initialization
 */
class PhotoSuggestionManager {
    constructor() {
        this.uploader = null;
        this.observer = null;
        this.recaptcha = RecaptchaService;
    }

    /**
     * Initialize all components
     */
    init() {
        console.log('📸 Photo Suggestion Form: Initializing...');
        this.initializeRecaptcha();
        this.observeDropZone();
        console.log('✅ Photo Suggestion Form: Initialization complete');
    }

    /**
     * Initialize reCAPTCHA v3 (centralized service)
     */
    initializeRecaptcha() {
        try {
            const success = this.recaptcha.init();

            if (!success) {
                console.warn('⚠️ Failed to initialize reCAPTCHA for PhotoSuggestion');
            } else {
                console.log('✅ reCAPTCHA initialized for PhotoSuggestion');
            }
        } catch (error) {
            console.error('❌ Failed to initialize reCAPTCHA:', error);
        }
    }

    /**
     * Initialize photo uploader when drop zone is ready
     */
    initializePhotoUploader() {
        const dropZone = document.querySelector('#photo-drop-zone');
        const fileInput = document.querySelector('#photos');

        if (dropZone && fileInput && !dropZone.dataset.initialized) {
            try {
                console.log('✅ Initializing photo uploader');
                this.uploader = new PhotoUploader('#photo-drop-zone', '#photos');
                dropZone.dataset.initialized = 'true';

                // Disconnect observer once initialized
                if (this.observer) {
                    this.observer.disconnect();
                    this.observer = null;
                }

                console.log('✅ Photo uploader initialized');
            } catch (error) {
                console.error('❌ Failed to initialize photo uploader:', error);
            }
        }
    }

    /**
     * Observe DOM for drop zone appearance (when form opens)
     */
    observeDropZone() {
        // If observer already exists, disconnect it
        if (this.observer) {
            this.observer.disconnect();
        }

        // Create MutationObserver to watch for drop zone appearance
        this.observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                // Check added nodes
                for (const node of mutation.addedNodes) {
                    // If it's an HTML element
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if it's the drop zone
                        if (node.id === 'photo-drop-zone') {
                            console.log('📸 Drop zone detected in DOM');
                            this.initializePhotoUploader();
                            return;
                        }
                        // Check if drop zone is within added node
                        if (node.querySelector && node.querySelector('#photo-drop-zone')) {
                            console.log('📸 Drop zone detected in DOM (nested)');
                            this.initializePhotoUploader();
                            return;
                        }
                    }
                }
            }
        });

        // Observe entire document for changes
        this.observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        console.log('👀 Drop zone observer started');

        // Check if drop zone already exists (if form is already open)
        const existingDropZone = document.querySelector('#photo-drop-zone');
        if (existingDropZone) {
            console.log('📸 Drop zone already in DOM');
            this.initializePhotoUploader();
        }
    }
}

// Initialize manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const manager = new PhotoSuggestionManager();
    manager.init();
});
