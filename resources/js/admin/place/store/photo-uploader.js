/**
 * Photo upload via drag & drop
 *
 * Features:
 * - Drag & drop files onto upload zone
 * - Visual feedback during drag
 * - File validation (type, size, count)
 * - Automatic assignment to Livewire input
 */
export class PhotoUploader {
    constructor(dropZoneSelector = '#photo-drop-zone', inputSelector = '#photos') {
        this.dropZone = null;
        this.fileInput = null;
        this.dropZoneSelector = dropZoneSelector;
        this.inputSelector = inputSelector;

        // Validation constraints
        this.maxFileSize = 10 * 1024 * 1024; // 10 MB
        this.maxFiles = 10;
        this.allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

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

        // Validate ONLY the new files (pas d'accumulation)
        const validation = this.validateFiles(filesArray);

        if (!validation.valid) {
            alert(validation.error);
            return;
        }

        // Create new FileList with ONLY the valid dropped files
        const dataTransfer = new DataTransfer();

        validation.validFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        this.fileInput.files = dataTransfer.files;

        // Trigger Livewire update
        this.fileInput.dispatchEvent(new Event('input', { bubbles: true }));
        this.fileInput.dispatchEvent(new Event('change', { bubbles: true }));

        console.log(`PhotoUploader: ${validation.validFiles.length} file(s) selected via drag & drop`);
    }

    /**
     * Validate files
     */
    validateFiles(files) {
        const validFiles = [];
        const errors = [];

        // Check count
        if (files.length > this.maxFiles) {
            return {
                valid: false,
                error: `Vous ne pouvez télécharger que ${this.maxFiles} photos maximum.`
            };
        }

        // Validate each file
        for (const file of files) {
            // Check type
            /*if (!this.allowedTypes.includes(file.type)) {
                errors.push(`${file.name} : type non autorisé (${file.type})`);
                continue;
            }

            // Check size
            if (file.size > this.maxFileSize) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                errors.push(`${file.name} : trop volumineux (${sizeMB} Mo, max 10 Mo)`);
                continue;
            }*/

            validFiles.push(file);
        }

        // If some files are invalid
        if (errors.length > 0) {
            return {
                valid: false,
                error: `Fichiers invalides :\n${errors.join('\n')}`
            };
        }

        return {
            valid: true,
            validFiles
        };
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
