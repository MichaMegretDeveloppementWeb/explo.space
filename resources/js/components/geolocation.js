/**
 * Geolocation utility for place exploration
 */
export class GeolocationManager {
    constructor() {
        this.isSupported = this.checkSupport();
        this.isLoading = false;
    }

    /**
     * Check if geolocation is supported
     */
    checkSupport() {
        return 'geolocation' in navigator;
    }

    /**
     * Get current position with error handling
     */
    async getCurrentPosition() {

        if (!this.isSupported) {
            const error = new Error('geolocation.unsupported');
            error.messageKey = 'unsupported';
            throw error;
        }

        if (this.isLoading) {
            const error = new Error('geolocation.already_in_progress');
            error.messageKey = 'already_in_progress';
            throw error;
        }

        this.isLoading = true;

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    resolve,
                    reject,
                    {
                        enableHighAccuracy: true,
                        timeout: 15000, // 15 seconds
                        maximumAge: 300000 // 5 minutes cache
                    }
                );
            });

            return {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy
            };
        } catch (error) {
            throw this.handleGeolocationError(error);
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Handle geolocation errors with user-friendly messages
     * Messages will be provided by Livewire component with proper translations
     */
    handleGeolocationError(error) {
        let messageKey;

        switch (error.code) {
            case error.PERMISSION_DENIED:
                messageKey = 'permission_denied';
                break;
            case error.POSITION_UNAVAILABLE:
                messageKey = 'position_unavailable';
                break;
            case error.TIMEOUT:
                messageKey = 'request_timeout';
                break;
            default:
                messageKey = 'unknown';
                break;
        }

        // Return error with message key for Livewire to handle translation
        const errorObj = new Error(`geolocation.${messageKey}`);
        errorObj.geolocationCode = error.code;
        errorObj.messageKey = messageKey;
        return errorObj;
    }

    /**
     * Watch position changes (for future use)
     */
    watchPosition(callback, errorCallback = null) {
        if (!this.isSupported) {
            if (errorCallback) errorCallback(new Error('Geolocation not supported'));
            return null;
        }

        return navigator.geolocation.watchPosition(
            callback,
            errorCallback || this.handleGeolocationError.bind(this),
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 60000 // 1 minute
            }
        );
    }

    /**
     * Clear position watch
     */
    clearWatch(watchId) {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
        }
    }
}

/**
 * Initialize geolocation for Livewire components
 */
export function initializeGeolocation() {
    const geolocationManager = new GeolocationManager();

    function setupGeolocationListener() {

        Livewire.on('requestGeolocation', function (event) {
            handleGeolocationRequest(geolocationManager, event);
        });

    }



    // Vérifier si Livewire est déjà initialisé
    if (typeof Livewire !== 'undefined') {
        setupGeolocationListener();
    } else {
        document.addEventListener('livewire:init', () => {
            setupGeolocationListener();
        });
    }

}

/**
 * Handle geolocation request from Livewire event
 */
async function handleGeolocationRequest(manager, params) {
    const { componentId } = params;

    try {
        const position = await manager.getCurrentPosition();

        Livewire.dispatch('geolocationSuccess', {
            data: {
                latitude: position.latitude,
                longitude: position.longitude,
                accuracy: position.accuracy
            }
        });

    } catch (error) {
        console.error("Geolocation error:", error);

        // Dispatch error event with data wrapped correctly
        Livewire.dispatch('geolocationError', {
            data: {
                message: error.message
            }
        });
    }
}

