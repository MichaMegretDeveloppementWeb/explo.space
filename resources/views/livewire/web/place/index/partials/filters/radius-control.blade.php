{{-- Radius control (common desktop + mobile) --}}
@php use App\Support\Config\PlaceSearchConfig; @endphp
@if($latitude && $longitude)
    <div class="space-y-2"
         x-data="{
             localRadius: {{ $radius }},
             debounceTimer: null,

             updateRadius() {
                 // Annuler le timer précédent
                 clearTimeout(this.debounceTimer);

                 // Créer un nouveau timer (800ms debounce)
                 this.debounceTimer = setTimeout(() => {
                     // Envoyer à Livewire après le debounce
                     $wire.set('radius', this.localRadius);
                 }, 800);
             },

             // Synchroniser avec les changements Livewire externes (ex: changement d'adresse)
             init() {
                 $watch('$wire.radius', value => {
                     this.localRadius = value;
                 });
             }
         }">
        <div class="flex items-center justify-between px-2">
            <span
                class="text-sm font-medium text-gray-700">{{ __('web/pages/explore.livewire.radius_search_label') }}</span>
            <span class="text-sm text-gray-500"
                  x-text="Math.round(localRadius/1000) + ' {{ __('web/pages/explore.livewire.radius_unit') }}'"></span>
        </div>
        <div class="px-2 relative">
            <div class="relative">
                {{-- Range input géré 100% par Alpine (zéro latence) --}}
                <input type="range"
                       x-model.number="localRadius"
                       @input="updateRadius()"
                       min="{{ PlaceSearchConfig::RADIUS_MIN }}"
                       max="{{ PlaceSearchConfig::RADIUS_MAX }}"
                       step="{{ PlaceSearchConfig::RADIUS_STEP }}"
                       class="w-full">
            </div>
        </div>
    </div>
@endif
