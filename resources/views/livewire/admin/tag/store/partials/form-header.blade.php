{{-- Breadcrumb --}}
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                <x-heroicon-o-home class="w-4 h-4 mr-2" />
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                <a href="{{ route('admin.tags.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                    Tags
                </a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400" />
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                    {{ $mode === 'create' ? 'Nouveau tag' : 'Éditer' }}
                </span>
            </div>
        </li>
    </ol>
</nav>

{{-- Header --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-5 {{ $mode === 'edit' ? 'flex items-start justify-between' : '' }}">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">
                @if($mode === 'create')
                    Créer un nouveau tag
                @else
                    Modifier le tag
                    @if(!empty($translations['fr']['name']))
                        <span class="text-gray-600">: {{ $translations['fr']['name'] }}</span>
                    @endif
                @endif
            </h1>

            @if($mode === 'edit' && $tag)
                <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                    <div class="flex items-center">
                        <x-heroicon-o-calendar class="w-4 h-4 mr-1.5" />
                        <span>Créé le {{ $tag->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($tag->updated_at && !$tag->created_at->eq($tag->updated_at))
                        <div class="flex items-center">
                            <x-heroicon-o-clock class="w-4 h-4 mr-1.5" />
                            <span>Modifié le {{ $tag->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        @if($mode === 'edit')
            <div class="ml-4">
                <button
                    type="button"
                    wire:click="confirmDeleteModal"
                    class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <x-heroicon-o-trash class="w-5 h-5 mr-2" />
                    Supprimer
                </button>
            </div>
        @endif
    </div>
</div>
