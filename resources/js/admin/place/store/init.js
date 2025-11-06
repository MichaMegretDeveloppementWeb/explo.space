/**
 * Initialize all admin place form modules
 *
 * This is the entry point for the admin place create/edit form.
 * It initializes:
 * - Location map (Leaflet)
 * - Address autocomplete (Nominatim)
 * - Photo drag & drop sorting (Alpine.js component)
 * - Photo drag & drop upload
 *
 * Notes:
 * - Slug auto-generation is now handled by Livewire in ManagesTranslations trait
 * - Validation scroll & tab switching is now handled by Alpine.js in translation-tabs.blade.php
 */
import { LocationMap } from './location-map.js';
import { photoSortable } from './photo-sortable.js';
import { PhotoUploader } from './photo-uploader.js';

class AdminPlaceFormManager {
    constructor() {
        this.locationMap = null;
        this.photoUploader = null;
        this.init();
    }

    /**
     * Initialize all modules
     */
    init() {
        // Wait for DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initModules());
        } else {
            this.initModules();
        }
    }

    /**
     * Initialize individual modules
     */
    initModules() {
        // 1. Location Map (only if container exists)
        try {
            this.locationMap = new LocationMap('admin-location-map');
        } catch (error) {
            console.error('✗ LocationMap failed:', error);
        }

        // 2. Photo Sortable is now handled by Alpine.js component (x-data="photoSortable()")
        // No need to initialize here - it's automatically initialized by Alpine.js in the view

        // 3. Photo Uploader (drag & drop upload)
        try {
            this.photoUploader = new PhotoUploader('#photo-drop-zone', '#photos');
        } catch (error) {
            console.error('✗ PhotoUploader failed:', error);
        }
    }

    /**
     * Destroy all modules (cleanup)
     */
    destroy() {
        if (this.locationMap) {
            this.locationMap.destroy();
        }
        if (this.photoUploader) {
            this.photoUploader.destroy();
        }
    }
}

// Initialize on page load
const formManager = new AdminPlaceFormManager();

// Expose globally for debugging
window.AdminPlaceFormManager = formManager;

// Export for use in other modules if needed
export default AdminPlaceFormManager;
