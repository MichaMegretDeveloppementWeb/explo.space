/**
 * Centralized reCAPTCHA v3 Service (Singleton)
 *
 * Unified service for managing Google reCAPTCHA v3 across all forms.
 * Ensures no conflicts between multiple forms using reCAPTCHA.
 *
 * Usage:
 * ```javascript
 * import { RecaptchaService } from '@/shared/recaptcha-service';
 *
 * const recaptcha = RecaptchaService.getInstance();
 * const token = await recaptcha.getToken('place_request_submit');
 * ```
 *
 * Available actions:
 * - 'place_request_submit'
 * - 'edit_request_submit'
 * - 'photo_suggestion_submit'
 */
export class RecaptchaService {
    static instance = null;

    constructor() {
        if (RecaptchaService.instance) {
            return RecaptchaService.instance;
        }

        this.siteKey = null;
        this.isLoaded = false;
        this.loadingPromise = null;

        RecaptchaService.instance = this;
    }

    /**
     * Get singleton instance
     */
    static getInstance() {
        if (!RecaptchaService.instance) {
            RecaptchaService.instance = new RecaptchaService();
        }
        return RecaptchaService.instance;
    }

    /**
     * Initialize reCAPTCHA service
     */
    init() {
        if (this.isLoaded) {
            console.log('✅ reCAPTCHA already initialized');
            return true;
        }

        // Retrieve site key from meta tag
        this.siteKey = document.querySelector('meta[name="recaptcha-site-key"]')?.content;

        if (!this.siteKey) {
            console.error('❌ reCAPTCHA site key not found in meta tag');
            return false;
        }

        // Check if grecaptcha is loaded
        if (typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
            console.log('✅ reCAPTCHA API already loaded');
            this.isLoaded = true;
            return true;
        }

        console.log('⏳ reCAPTCHA API loading...');
        return true;
    }

    /**
     * Wait for grecaptcha to be ready
     */
    async waitForRecaptcha() {
        if (this.isLoaded && typeof grecaptcha !== 'undefined') {
            return true;
        }

        // If already waiting, return the existing promise
        if (this.loadingPromise) {
            return this.loadingPromise;
        }

        this.loadingPromise = new Promise((resolve, reject) => {
            const timeout = setTimeout(() => {
                reject(new Error('reCAPTCHA loading timeout'));
            }, 10000); // 10 seconds timeout

            const checkRecaptcha = () => {
                if (typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
                    clearTimeout(timeout);
                    grecaptcha.ready(() => {
                        this.isLoaded = true;
                        this.loadingPromise = null;
                        console.log('✅ reCAPTCHA ready');
                        resolve(true);
                    });
                } else {
                    setTimeout(checkRecaptcha, 100);
                }
            };

            checkRecaptcha();
        });

        return this.loadingPromise;
    }

    /**
     * Get reCAPTCHA token for specific action
     *
     * @param {string} action - Action name (e.g., 'place_request_submit')
     * @returns {Promise<string>} - reCAPTCHA token
     */
    async getToken(action) {
        if (!this.siteKey) {
            console.error('❌ reCAPTCHA not initialized');
            throw new Error('reCAPTCHA not initialized');
        }

        try {
            // Wait for grecaptcha to be ready
            await this.waitForRecaptcha();

            // Execute reCAPTCHA
            const token = await grecaptcha.execute(this.siteKey, { action });

            console.log(`✅ reCAPTCHA token obtained for action: ${action}`);
            return token;

        } catch (error) {
            console.error('❌ reCAPTCHA execution failed:', error);
            throw error;
        }
    }

    /**
     * Validate that reCAPTCHA is properly configured
     */
    isConfigured() {
        return this.siteKey !== null;
    }

    /**
     * Get current site key
     */
    getSiteKey() {
        return this.siteKey;
    }
}

// Create global instance for backward compatibility with Alpine.js
if (typeof window !== 'undefined') {
    const service = RecaptchaService.getInstance();

    window.recaptcha = {
        getToken: (action) => service.getToken(action),
        isConfigured: () => service.isConfigured(),
    };
}

// Export singleton instance as default
export default RecaptchaService.getInstance();
