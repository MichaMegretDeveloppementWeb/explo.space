@extends('layouts.web', ['footer' => true])

{{-- Scripts JS pour carrousel et carte (chargés via Vite) --}}
@vite([
    'resources/js/web/place/show/index.js',
    'resources/js/web/place/edit-request/index.js',
    'resources/js/web/place/photo-suggestion/index.js'
])

@section('content')
    {{-- Architecture verticale modulaire (style Apple) --}}

    {{-- Hero immersif --}}
    @include('web.place.show.partials.hero')

    {{-- Introduction : Breadcrumb + Tags --}}
    @include('web.place.show.partials.intro')

    {{-- Description --}}
    @include('web.place.show.partials.description')

    {{-- Informations pratiques --}}
    @include('web.place.show.partials.practical-info')

    {{-- Galerie photos --}}
    @include('web.place.show.partials.photo-gallery')

    {{-- Photo Suggestion Form --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @livewire('web.place.photo-suggestion.photo-suggestion-form', ['place' => $place])
    </div>

    {{-- Localisation : Carte + Coordonnées --}}
    @include('web.place.show.partials.location')

    {{-- Actions utilisateur --}}
    @include('web.place.show.partials.actions')

    {{-- Métadonnées --}}
    @include('web.place.show.partials.metadata')

    {{-- Modal Galerie Photos --}}
    @include('web.place.show.partials.photo-modal')

@endsection
