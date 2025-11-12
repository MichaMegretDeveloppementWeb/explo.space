/**
 * Alpine.js component for Contact Form with reCAPTCHA v3 integration
 *
 * Usage in Blade:
 * <form x-data="contactForm" @submit.prevent="handleSubmit">
 *
 * This component handles:
 * - Form submission with reCAPTCHA token
 * - Token retrieval via RecaptchaService
 * - Error handling
 * - Integration with Livewire component
 */
export function contactForm() {
    return {
        /**
         * Handle form submission
         * - Retrieve reCAPTCHA token
         * - Call Livewire submit method with token
         * - Handle errors gracefully
         */
        async handleSubmit() {
            try {
                // Get reCAPTCHA token from centralized service
                const token = await window.recaptcha.getToken('contact_form_submit');

                // Call Livewire submit method with token
                this.$wire.call('submit', token);
            } catch (error) {
                // Handle reCAPTCHA errors (network issues, invalid site key, etc.)
                console.error('reCAPTCHA error:', error);
                this.$wire.call('handleRecaptchaError', error.message);
            }
        }
    };
}
