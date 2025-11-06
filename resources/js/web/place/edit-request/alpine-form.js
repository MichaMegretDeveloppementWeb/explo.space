/**
 * Alpine.js component pour le formulaire EditRequest
 *
 * Responsabilités :
 * - Intercepter la soumission du formulaire
 * - Obtenir le token reCAPTCHA via window.recaptcha.getToken('edit_request_submit')
 * - Soumettre avec le token via Livewire (un seul appel)
 * - Gérer les erreurs proprement
 *
 * Fallback : Si Alpine échoue, wire:submit.prevent="submit" prend le relais
 * (soumission sans token → erreur de validation affichée)
 */

export function editRequestForm() {
    return {
        /**
         * Gérer la soumission du formulaire
         */
        async handleSubmit() {
            try {
                // Vérifier que window.recaptcha.getToken existe
                if (!window.recaptcha || typeof window.recaptcha.getToken !== 'function') {
                    // Fallback : appeler submit sans token
                    this.$wire.call('submit');
                    return;
                }

                // Obtenir le token reCAPTCHA avec action spécifique (Promise)
                const token = await window.recaptcha.getToken('edit_request_submit');

                // Soumettre avec le token (déclenche automatiquement wire:loading)
                this.$wire.call('submit', token);

            } catch (error) {
                // Erreur lors de l'obtention du token
                console.error('❌ reCAPTCHA error in Alpine:', error);

                // Appeler la méthode d'erreur Livewire
                this.$wire.call('handleRecaptchaError', error.message);
            }
        }
    };
}
