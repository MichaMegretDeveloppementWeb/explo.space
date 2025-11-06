@props(['placeRequest'])

<x-admin.place.detail.info-card
    title="Traçabilité & Workflow"
    icon="heroicon-o-clock">

    <dl>

        {{-- Date de soumission --}}
        <x-admin.place.detail.attribute-row
            label="Date de soumission"
            :value="$placeRequest->created_at->format('d/m/Y à H:i')" />

        {{-- Vue par admin --}}
        @if($placeRequest->viewed_at)
            <x-admin.place.detail.attribute-row label="Première consultation" type="custom">
                <div class="space-y-1">
                    <p class="text-sm text-gray-900">
                        {{ $placeRequest->viewed_at->format('d/m/Y à H:i') }}
                    </p>
                    @if($placeRequest->viewedByAdmin)
                        <p class="text-xs text-gray-500">
                            par {{ $placeRequest->viewedByAdmin->name }}
                        </p>
                    @endif
                </div>
            </x-admin.place.detail.attribute-row>
        @else
            <x-admin.place.detail.attribute-row
                label="Première consultation"
                value="Pas encore consultée" />
        @endif

        {{-- Statut actuel --}}
        <x-admin.place.detail.attribute-row label="Statut actuel" type="badge">
            <x-admin.badge-status :status="$placeRequest->status" />
        </x-admin.place.detail.attribute-row>

        {{-- Traitement --}}
        @if($placeRequest->processed_at)
            <x-admin.place.detail.attribute-row
                :label="$placeRequest->isAccepted() ? 'Date de validation' : 'Date de refus'"
                type="custom">
                <div class="space-y-1">
                    <p class="text-sm text-gray-900">
                        {{ $placeRequest->processed_at->format('d/m/Y à H:i') }}
                    </p>
                    @if($placeRequest->processedByAdmin)
                        <p class="text-xs text-gray-500">
                            par {{ $placeRequest->processedByAdmin->name }}
                        </p>
                    @endif
                </div>
            </x-admin.place.detail.attribute-row>
        @endif

        {{-- Raison admin (si refusé) --}}
        @if($placeRequest->isRefused())
            <x-admin.place.detail.attribute-row label="Raison du refus" type="custom">
                <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-900">
                        {!! nl2br(e($placeRequest->admin_reason ?? 'Aucune raison fournie')) !!}
                    </p>
                </div>
            </x-admin.place.detail.attribute-row>
        @endif

        {{-- Lieu créé (si accepté) --}}
        @if($placeRequest->isAccepted() && $placeRequest->place)
            <x-admin.place.detail.attribute-row label="Lieu créé" type="link">
                <a href="{{ route('admin.places.show', $placeRequest->place->id) }}"
                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline">
                    <span>Voir le lieu créé</span>
                    <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                </a>
            </x-admin.place.detail.attribute-row>
        @endif
    </dl>

    {{-- Délai de traitement (si traité) --}}
    @if($placeRequest->processed_at)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <x-heroicon-o-clock class="h-5 w-5 text-gray-400" />
                <p>
                    <span class="font-medium">Délai de traitement :</span>
                    {{ $placeRequest->created_at->diffForHumans($placeRequest->processed_at, true) }}
                </p>
            </div>
        </div>
    @endif
</x-admin.place.detail.info-card>
