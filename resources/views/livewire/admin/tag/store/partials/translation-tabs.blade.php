@php
    $supportedLocales = config('locales.supported', ['fr', 'en']);
@endphp

<div class="border-t border-gray-200">

    {{-- Tab Headers --}}
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex -mb-px px-6" aria-label="Tabs">
            @foreach ($supportedLocales as $locale)
                <button type="button"
                        @click="activeTab = '{{ $locale }}'"
                        :class="{
                            'border-blue-500 text-blue-600': activeTab === '{{ $locale }}',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $locale }}'
                        }"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-150 inline-flex items-center gap-2">
                    {{ strtoupper($locale) }}

                    {{-- Badge erreur validation --}}
                    @if ($errors->has("translations.{$locale}.*"))
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                            !
                        </span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab Contents --}}
    @foreach ($supportedLocales as $locale)
        <div x-show="activeTab === '{{ $locale }}'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="p-6 space-y-6">

            {{-- Auto-translate button --}}
            <div class="flex justify-center">
                <button type="button"
                        wire:click="initiateTranslation('{{ $locale }}')"
                        wire:loading.attr="disabled"
                        wire:target="initiateTranslation"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-75 disabled:cursor-not-allowed">

                    {{-- Icon normal --}}
                    <span wire:loading.remove wire:target="initiateTranslation">
                        <x-heroicon-o-language class="w-4 h-4" />
                    </span>

                    {{-- Loading spinner --}}
                    <span wire:loading wire:target="initiateTranslation">
                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                    </span>

                    {{-- Text --}}
                    <span wire:loading.remove wire:target="initiateTranslation">Traduire vers <b>{{ $locale === 'fr' ? 'EN' : 'FR' }}</b></span>
                    <span wire:loading wire:target="initiateTranslation">Traduction en cours...</span>
                </button>
            </div>

            {{-- Name --}}
            <x-admin.form.input
                label="Nom"
                name="translations_{{ $locale }}_name"
                wire:model.live.debounce.500ms="translations.{{ $locale }}.name"
                :required="true"
                :error="$errors->first('translations.' . $locale . '.name')"
            />

            {{-- Slug --}}
            <x-admin.form.input
                label="Slug"
                name="translations_{{ $locale }}_slug"
                wire:model="translations.{{ $locale }}.slug"
                :required="true"
                helperText="Format: lettres minuscules, chiffres et tirets uniquement"
                :error="$errors->first('translations.' . $locale . '.slug')"
            />

            {{-- Description --}}
            <x-admin.form.textarea
                label="Description"
                name="translations_{{ $locale }}_description"
                wire:model="translations.{{ $locale }}.description"
                rows="4"
                helperText="{{ strlen($translations[$locale]['description'] ?? '') }}/2000 caractères"
                :error="$errors->first('translations.' . $locale . '.description')"
            />

        </div>
    @endforeach
</div>
