/**
 * Drag & drop photo reordering using SortableJS
 *
 * Features:
 * - Reorder existing photos by dragging
 * - Update photo order in Livewire component
 * - Visual feedback during drag
 * - Alpine.js compatible
 */
import Sortable from 'sortablejs';

/**
 * Alpine.js component factory for photo sorting
 * Usage: x-data="photoSortable()"
 */
export function photoSortable() {
    return {
        sortableInstance: null,

        /**
         * Initialize Sortable.js on the grid
         */
        initSortable() {
            const grid = document.getElementById('existing-photos-grid');
            if (!grid) {
                console.warn('PhotoSortable: Grid #existing-photos-grid not found (normal for create mode)');
                return;
            }

            this.sortableInstance = Sortable.create(grid, {
                animation: 150,
                handle: '.sortable-handle',
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                chosenClass: 'sortable-chosen',
                forceFallback: true,
                fallbackClass: 'sortable-fallback',
                swapThreshold: 0.65,

                onEnd: (evt) => {
                    // Récupérer l'ordre actuel des photos
                    const items = grid.querySelectorAll('.sortable-item');
                    const orderMap = {};

                    items.forEach((item, index) => {
                        const photoId = parseInt(item.dataset.photoId);
                        if (!isNaN(photoId)) {
                            orderMap[photoId] = index; // Nouvelle position (sort_order)
                        }
                    });

                    // Envoyer l'ordre au backend Livewire
                    if (Object.keys(orderMap).length > 0 && this.$wire) {
                        this.$wire.call('updatePhotoOrder', orderMap);
                    }
                }
            });

            // Ajouter les styles CSS dynamiquement
            this.addStyles();
        },

        /**
         * Ajouter les styles CSS pour le drag & drop
         */
        addStyles() {
            if (document.getElementById('photo-sortable-styles')) {
                return; // Déjà ajouté
            }

            const styles = document.createElement('style');
            styles.id = 'photo-sortable-styles';
            styles.textContent = `
                .sortable-ghost {
                    opacity: 0.4;
                    background: #e5e7eb;
                }

                .sortable-drag {
                    opacity: 1;
                    cursor: grabbing !important;
                }

                .sortable-chosen {
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                }

                .sortable-fallback {
                    opacity: 0;
                }
            `;

            document.head.appendChild(styles);
        },

        /**
         * Détruire l'instance Sortable
         */
        destroy() {
            if (this.sortableInstance) {
                this.sortableInstance.destroy();
                this.sortableInstance = null;
            }
        }
    };
}

// Exposer globalement pour Alpine.js
if (typeof window !== 'undefined') {
    window.photoSortable = photoSortable;
}
