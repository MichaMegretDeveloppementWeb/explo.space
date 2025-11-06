@php
    use App\Enums\RequestStatus;
@endphp

<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4">
        {{-- En-tête avec titre et compteur total --}}
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-700">Filtrer par statut</h3>
                <p class="mt-0.5 text-xs text-gray-500">{{ $statusCounts['all'] }} proposition(s) au total</p>
            </div>

            @if(!empty($status))
                <button wire:click="resetFilters"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <x-heroicon-o-x-mark class="h-3.5 w-3.5" />
                    Effacer les filtres
                </button>
            @endif
        </div>

        {{-- Checkboxes des statuts --}}
        <div class="flex flex-wrap gap-3">
            @foreach([RequestStatus::Submitted, RequestStatus::Pending, RequestStatus::Accepted, RequestStatus::Refused] as $requestStatus)
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           value="{{ $requestStatus->value }}"
                           wire:model.live="status"
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium border
                        {{ in_array($requestStatus->value, $status) ? $requestStatus->badgeClasses() : 'bg-gray-50 text-gray-600 border-gray-200' }}">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        {{ $requestStatus->label() }} ({{ $statusCounts[$requestStatus->value] }})
                    </span>
                </label>
            @endforeach
        </div>
    </div>
</div>
