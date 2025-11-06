/**
 * Photo upload via drag & drop for photo suggestions
 *
 * Features:
 * - Drag & drop files onto upload zone
 * - Visual feedback during drag
 * - Automatic assignment to Livewire input
 */
export class PhotoUploader {
    constructor(dropZoneSelector = '#photo-drop-zone', inputSelector = '#photos') {
        this.dropZone = null;
        this.fileInput = null;
        this.dropZoneSelector = dropZoneSelector;
        this.inputSelector = inputSelector;

        this.init();
    }

    /**
     * Initialize drag & drop functionality
     */
    init() {
        this.dropZone = document.querySelector(this.dropZoneSelector);
        this.fileInput = document.querySelector(this.inputSelector);

        if (!this.dropZone || !this.fileInput) {
            console.warn('PhotoUploader: Drop zone or file input not found');
            return;
        }

        this.setupEventListeners();
        this.addStyles();
    }

    /**
     * Set up drag & drop event listeners
     */
    setupEventListeners() {
        // Prevent default drag behaviors on document
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, this.preventDefaults, false);
        });

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, () => this.highlight(), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, () => this.unhighlight(), false);
        });

        // Handle dropped files
        this.dropZone.addEventListener('drop', (e) => this.handleDrop(e), false);

        // Add click handler to trigger file input (except on label/input itself)
        this.dropZone.addEventListener('click', (e) => {
            // Don't trigger if clicking on the label or input directly
            // (they already handle the click properly)
            if (e.target.tagName === 'LABEL' || e.target.tagName === 'INPUT') {
                return;
            }

            // If clicking anywhere else in the drop zone, trigger the file input
            this.fileInput.click();
        });
    }

    /**
     * Prevent default drag behaviors
     */
    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    /**
     * Highlight drop zone
     */
    highlight() {
        this.dropZone.classList.add('drop-zone-active');
    }

    /**
     * Remove highlight from drop zone
     */
    unhighlight() {
        this.dropZone.classList.remove('drop-zone-active');
    }

    /**
     * Handle file drop
     */
    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        this.handleFiles(files);
    }

    /**
     * Handle and validate files
     */
    handleFiles(files) {
        // Convert FileList to Array
        const filesArray = Array.from(files);

        // Create new FileList with the dropped files
        const dataTransfer = new DataTransfer();

        filesArray.forEach(file => {
            dataTransfer.items.add(file);
        });

        this.fileInput.files = dataTransfer.files;

        // Trigger Livewire update
        this.fileInput.dispatchEvent(new Event('input', { bubbles: true }));
        this.fileInput.dispatchEvent(new Event('change', { bubbles: true }));

        console.log(`PhotoUploader: ${filesArray.length} file(s) selected via drag & drop`);
    }

    /**
     * Add CSS styles for drag feedback
     */
    addStyles() {
        if (document.getElementById('photo-uploader-styles')) {
            return; // Already added
        }

        const styles = document.createElement('style');
        styles.id = 'photo-uploader-styles';
        styles.textContent = `
            .drop-zone-active {
                border-color: #3b82f6 !important;
                background-color: #eff6ff !important;
            }

            .drop-zone-active svg {
                color: #3b82f6 !important;
            }
        `;

        document.head.appendChild(styles);
    }

    /**
     * Destroy uploader (cleanup)
     */
    destroy() {
        if (this.dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.dropZone.removeEventListener(eventName, this.preventDefaults);
            });
        }
    }
}
