@props([
    'title' => 'Tableau de bord',
    'description' => 'Bienvenue dans l\'espace d\'administration d\'Explo.space',
])

<div class="bg-white rounded-md max-w-[90rem] mx-auto">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-6 gap-4">
            {{-- Titre et description --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                    {{ $title }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
            </div>

            {{-- Action rapide --}}
            <div class="flex-shrink-0">
                <a href="{{ route('admin.places.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-all duration-150 shadow-sm hover:shadow-md">
                    <x-heroicon-o-plus class="h-5 w-5" />
                    <span>Nouveau lieu</span>
                </a>
            </div>
        </div>
    </div>
</div>

