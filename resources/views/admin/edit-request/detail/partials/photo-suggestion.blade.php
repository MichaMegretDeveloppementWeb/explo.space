@props(['editRequest'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                <x-heroicon-o-photo class="h-5 w-5 text-purple-600" />
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Proposition de photos</h2>
                <p class="text-sm text-gray-600">Sélectionnez les photos à ajouter au lieu</p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="px-6 py-5 space-y-4">

        {{-- Instructions ou message de traitement --}}
        @if($editRequest->isAccepted())
            {{-- Message de confirmation si acceptée --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-green-800">
                        <p class="font-medium mb-1">Demande traitée</p>
                        <p>Cette proposition de photos a été traitée et validée le {{ $editRequest->processed_at->format('d/m/Y à H:i') }}.</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Instructions si en attente --}}
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex gap-3">
                    <x-heroicon-o-information-circle class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-purple-800">
                        <p class="font-medium mb-1">Action requise</p>
                        <p>Sélectionnez les photos que vous souhaitez ajouter au lieu, puis cliquez sur <strong>"Appliquer les photos"</strong>. Vous serez redirigé vers le formulaire d'édition avec les photos pré-chargées.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Grid de photos proposées --}}
        @php
            $suggestedPhotoData = $editRequest->suggested_photo_paths ?? [];
            // Extraire le tableau 'photos' depuis la structure suggested_changes
            $suggestedPhotos = $suggestedPhotoData['photos'] ?? [];
            $appliedChanges = $editRequest->applied_changes ?? [];
            $appliedPhotos = $appliedChanges['photos'] ?? [];
        @endphp

        @if(!empty($suggestedPhotos))

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($suggestedPhotos as $index => $filename)

                    @php
                        // Construire le chemin complet: {id}/filename.jpg
                        $photoUrl = Storage::disk('edit_request_photos')->url($editRequest->id . '/' . $filename);
                    @endphp

                    <div wire:key="{{ $filename }}" class="relative group">
                        @if($editRequest->isAccepted())
                            {{-- Badge Appliqué / Non retenu si acceptée --}}
                            <div class="absolute top-2 right-2 z-10">
                                @if(in_array($index, $appliedPhotos, true))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-600 shadow-sm">
                                        <x-heroicon-o-check-circle class="h-3 w-3 mr-1" />
                                        Appliqué
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600 shadow-sm">
                                        <x-heroicon-o-x-mark class="h-3 w-3 mr-1" />
                                        Non retenu
                                    </span>
                                @endif
                            </div>
                        @else
                            {{-- Checkbox de sélection si en attente --}}
                            <label class="absolute top-2 right-2 z-10 cursor-pointer">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedPhotos"
                                    value="{{ $index }}"
                                    class="h-5 w-5 text-purple-600 border-2 border-white rounded shadow-sm focus:ring-purple-500 bg-white/90">
                            </label>
                        @endif

                        {{-- Image --}}
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 transition-all duration-200"
                             :class="$wire.selectedPhotos.includes('{{ $index }}') ? 'border-purple-500 ring-2 ring-purple-500 ring-offset-2' : 'border-gray-200'">
                            <img
                                src="{{ $photoUrl }}"
                                alt="Photo proposée {{ $loop->iteration }}"
                                class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105"
                                loading="lazy">
                        </div>

                        {{-- Numéro de la photo --}}
                        <div class="mt-1 text-xs text-center text-gray-500">
                            Photo {{ $loop->iteration }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Compteur de sélection (uniquement si en attente) --}}
            @if(!$editRequest->isAccepted())
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ count($selectedPhotos) }}</span> photo(s) sélectionnée(s) sur {{ count($suggestedPhotos) }}
                    </div>

                    @if(count($selectedPhotos) > 0)
                        <button
                            type="button"
                            wire:click="$set('selectedPhotos', [])"
                            class="text-sm text-gray-600 hover:text-gray-800 hover:underline">
                            Tout désélectionner
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="$set('selectedPhotos', {{ json_encode(array_keys($suggestedPhotos)) }})"
                            class="text-sm text-purple-600 hover:text-purple-800 hover:underline">
                            Tout sélectionner
                        </button>
                    @endif
                </div>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <x-heroicon-o-photo class="h-12 w-12 mx-auto text-gray-400 mb-3" />
                <p>Aucune photo proposée.</p>
            </div>
        @endif

        {{-- Contact email --}}
        @if($editRequest->contact_email)
            <div class="pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Contact</h3>
                <div class="flex items-center gap-2">
                    <x-heroicon-o-envelope class="h-4 w-4 text-gray-400" />
                    <a href="mailto:{{ $editRequest->contact_email }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        {{ $editRequest->contact_email }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
