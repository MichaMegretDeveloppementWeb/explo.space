/**
 * Contact Page Manager
 *
 * Orchestrates all functionality for the contact page:
 * - reCAPTCHA v3 initialization
 * - Alpine.js component registration
 */

import RecaptchaService from '@/shared/recaptcha-service.js';
import { contactForm } from './alpine-form.js';

// Register Alpine component - Pattern safe pour timing d'initialisation
const registerAlpineComponent = () => {
    if (window.Alpine && typeof window.Alpine.data === 'function') {
        window.Alpine.data('contactForm', contactForm);
        console.log('✅ Alpine component "contactForm" registered');
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

// Initialize reCAPTCHA after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize only on contact page
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        new ContactPageManager();
    }
});

class ContactPageManager {
    constructor() {
        this.recaptcha = null;
        this.init();
    }

    /**
     * Initialize the contact page
     */
    init() {
        this.initializeRecaptcha();
    }

    /**
     * Initialize reCAPTCHA v3 service (centralized singleton)
     */
    initializeRecaptcha() {
        this.recaptcha = RecaptchaService;

        const success = this.recaptcha.init();

        if (!success) {
            console.warn('⚠️ Failed to initialize reCAPTCHA');
        } else {
            console.log('✅ reCAPTCHA initialized for Contact page');
        }
    }
}

export default ContactPageManager;
