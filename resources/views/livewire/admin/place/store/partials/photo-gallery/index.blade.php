{{-- Alpine.js : validation limites PHP critiques (upload_max_filesize + post_max_size) --}}
<div class="space-y-6"
     x-data="{
        uploadMaxSizeMB: {{ round(min(\App\Helpers\UploadHelper::getPhpUploadMaxSizeKB(), config('upload.images.max_size_kb')) / 1024, 2) }},
        postMaxSizeMB: {{ round(min(\App\Helpers\UploadHelper::getPostMaxSizeKB(), config('upload.images.max_size_kb') * config('upload.images.max_files')) / 1024, 2) }},
        phpLimitError: null,
        errorMessages: {
            fileTooLarge: '{{ __('errors/upload.file_too_large') }}',
            totalTooLarge: '{{ __('errors/upload.total_too_large') }}'
        },

        validateBeforeUpload(event) {
            // Clear l'erreur Alpine.js
            this.phpLimitError = null;

            @this.call('clearPendingPhotosErrors');

            const files = event.target.files;
            if (!files || files.length === 0) {
                return;
            }

            let totalSizeMB = 0;

            // Vérifier chaque fichier individuellement et calculer le total
            for (let i = 0; i < files.length; i++) {
                const fileSizeMB = files[i].size / (1024 * 1024);
                totalSizeMB += fileSizeMB;

                // Vérification 1 : Fichier individuel > upload_max_filesize
                if (fileSizeMB > this.uploadMaxSizeMB) {
                    this.phpLimitError = this.errorMessages.fileTooLarge
                        .replace(':filename', files[i].name)
                        .replace(':size', fileSizeMB.toFixed(1))
                        .replace(':max', this.uploadMaxSizeMB.toFixed(1));
                    event.target.value = '';
                    event.stopImmediatePropagation(); // BLOQUE wire:model.live
                    return;
                }
            }

            // Vérification 2 : Total tous fichiers > post_max_size
            if (totalSizeMB > this.postMaxSizeMB) {
                this.phpLimitError = this.errorMessages.totalTooLarge
                    .replace(':size', totalSizeMB.toFixed(1))
                    .replace(':max', this.postMaxSizeMB.toFixed(1));
                event.target.value = '';
                event.stopImmediatePropagation(); // BLOQUE wire:model.live
                return;
            }

        }
     }">

    @include('livewire.admin.place.store.partials.photo-gallery.existing-photos')

    @include('livewire.admin.place.store.partials.photo-gallery.upload-area')

    @include('livewire.admin.place.store.partials.photo-gallery.pending-photos-gallery')

    @include('livewire.admin.place.store.partials.photo-gallery.info-notice')

    @include('livewire.admin.place.store.partials.photo-gallery.translation-modal')
</div>
</div>
