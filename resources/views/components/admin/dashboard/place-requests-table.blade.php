@props([
    'requests',
    'title' => 'Dernières propositions de lieux',
])

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="px-6 @if($requests->isNotEmpty()) pr-0 @endif py-5 border-b border-gray-200 flex items-center justify-between gap-2">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $requests->count() }} proposition(s) récente(s)</p>
        </div>
        @if($requests->isNotEmpty())
            <a href="{{ route('admin.place-requests.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 min-w-max p-3">
                Voir tout
                <x-heroicon-o-chevron-right class="h-4 w-4" />
            </a>
        @endif
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        @if($requests->isNotEmpty())
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lieu
                        </th>
                        <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $request)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 cursor-pointer group"
                        onclick="window.location.href='{{ route('admin.place-requests.show', $request->id) }}'">
                        {{-- Lieu --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                {{-- Icône lieu --}}
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                    <x-heroicon-o-map-pin class="h-5 w-5 text-blue-600" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $request->title }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate md:hidden">
                                        {{ $request->contact_email }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Contact (caché sur mobile) --}}
                        <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-envelope class="h-4 w-4 text-gray-400" />
                                <span class="text-sm text-gray-600">{{ $request->contact_email }}</span>
                            </div>
                        </td>

                        {{-- Statut --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-admin.badge-status :status="$request->status" />
                        </td>

                        {{-- Date (cachée sur mobile et tablette) --}}
                        <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-calendar class="h-4 w-4 text-gray-400" />
                                <span>{{ $request->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            {{-- Empty state --}}
            <div class="text-center py-12 px-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <x-heroicon-o-inbox class="h-8 w-8 text-gray-400" />
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune proposition de lieu récente</h3>
                <p class="text-sm text-gray-500">Les dernières propositions des visiteurs apparaîtront ici.</p>
            </div>
        @endif
    </div>
</div>
