{{-- Header --}}
<div class="mb-6">
    {{-- Badge + Breadcrumb --}}
    <div class="flex items-center gap-2 mb-6">
        @if ($mode === 'create' && $placeRequestId)
            {{-- Badge subtil "Proposition" --}}
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-sm font-medium">
                <x-heroicon-s-user class="h-3.5 w-3.5" />
                Proposition utilisateur
            </span>
            <span class="text-gray-300">·</span>
            <span class="text-sm text-gray-500 font-medium">Demande #{{ $placeRequestId }}</span>
        @elseif ($mode === 'create')
            <span class="inline-flex items-center gap-1.5 py-1 rounded-md bg-gray-100 text-gray-700 text-sm font-medium">
                <x-heroicon-o-plus class="h-3.5 w-3.5" />
                Création
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 py-1 rounded-md bg-gray-100 text-gray-700 text-sm font-medium">
                <x-heroicon-o-pencil-square class="h-3.5 w-3.5" />
                Édition
            </span>
        @endif
    </div>

    {{-- Titre principal --}}
    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
        @if ($mode === 'create' && $placeRequestId)
            Validation d'une proposition de lieu
        @elseif ($mode === 'create')
            Créer un nouveau lieu
        @else
            Modifier le lieu
        @endif
    </h1>

    {{-- Sous-titre + Card infos pour PlaceRequest --}}
    @if ($mode === 'create' && $placeRequestId)
        <p class="mt-2 text-sm text-gray-500">
            Vérifiez et complétez les informations proposées avant de publier
        </p>

        @php
            $placeRequestData = \App\Models\PlaceRequest::find($placeRequestId);
        @endphp

        @if($placeRequestData)
            <div class="mt-6 bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-5 py-4">

                    <div class="flex flex-col md:flex-row items-start gap-4">

                        {{-- Icône --}}
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                <x-heroicon-o-information-circle class="h-5 w-5 text-blue-600" />
                            </div>
                        </div>

                        {{-- Contenu --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 mb-1">
                                Proposition soumise par un visiteur
                            </h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                Vous pouvez modifier toutes les informations et assigner des tags/catégories avant de valider la publication.
                            </p>

                            {{-- Métadonnées --}}
                            <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-gray-500">
                                @if($placeRequestData->contact_email)
                                    <div class="flex items-center gap-1.5">
                                        <x-heroicon-o-envelope class="h-4 w-4 text-gray-400" />
                                        <a href="mailto:{{ $placeRequestData->contact_email }}" class="hover:text-blue-600 transition-colors">
                                            {{ $placeRequestData->contact_email }}
                                        </a>
                                    </div>
                                @endif

                                @if($placeRequestData->created_at)
                                    <div class="flex items-center gap-1.5">
                                        <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                        <span>Proposé le {{ $placeRequestData->created_at->format('d/m/Y à H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @elseif ($mode === 'create')
        <p class="mt-2 text-sm text-gray-500">
            Renseignez toutes les informations nécessaires pour créer un nouveau lieu
        </p>
    @else
        <p class="mt-2 text-sm text-gray-500">
            Modifiez les informations de ce lieu
        </p>
    @endif
</div>
