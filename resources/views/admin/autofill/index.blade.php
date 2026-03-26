@extends('layouts.admin')

@section('title', 'Remplissage automatique')

@push('head')
    <style>
        body.autofill-page { background-color: #ffffff; }
        body.autofill-page > main { padding-top: 0; padding-bottom: 0; }
    </style>
    <script>document.addEventListener('DOMContentLoaded', () => document.body.classList.add('autofill-page'));</script>
@endpush

@section('content')
    <div x-data="{ tab: 'chat' }" class="mx-auto max-w-3xl px-4">
        {{-- Tabs --}}
        <div class="sticky top-0 z-10 flex gap-1 bg-white pb-2 pt-5">
            <button @click="tab = 'chat'"
                    :class="tab === 'chat' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-700'"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors">
                Assistant
            </button>
            <button @click="tab = 'history'"
                    :class="tab === 'history' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-700'"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors">
                Historique
            </button>
        </div>

        {{-- Chat tab --}}
        <div x-show="tab === 'chat'" x-cloak class="py-6">
            @livewire('admin.autofill.autofill-chat')
        </div>

        {{-- History tab --}}
        <div x-show="tab === 'history'" x-cloak class="py-8">
            @livewire('admin.autofill.autofill-history')
        </div>
    </div>
@endsection
