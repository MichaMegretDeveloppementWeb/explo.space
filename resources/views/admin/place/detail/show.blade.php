@extends('layouts.admin')

@section('title', $place->translations->first()?->title ?? "Lieu #{$place->id}")

@vite(['resources/js/admin/place/detail/index.js'])

@section('content')
    {{-- Header sticky --}}
    @include('admin.place.detail.partials.header', ['place' => $place])

    {{-- Content --}}
    <div class="max-w-7xl mx-auto mt-6">
        <div class="space-y-6">

            {{-- Traductions (Livewire) - Infos principales --}}
            @livewire('admin.place.place-translation-viewer', ['place' => $place])

            {{-- Grid 2 colonnes pour Localisation + Workflow --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Localisation --}}
                @include('admin.place.detail.partials.location', ['place' => $place])

                {{-- Traçabilité & Workflow --}}
                @include('admin.place.detail.partials.workflow', ['place' => $place])
            </div>

            {{-- Photos --}}
            @include('admin.place.detail.partials.photos', [
                'place' => $place,
                'photoCount' => $photo_count
            ])

            {{-- Grid 2 colonnes pour Tags + Catégories --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Tags --}}
                @include('admin.place.detail.partials.tags', [
                    'place' => $place,
                    'tagCount' => $tag_count
                ])

                {{-- Catégories --}}
                @include('admin.place.detail.partials.categories', [
                    'place' => $place,
                    'categoryCount' => $category_count
                ])
            </div>
        </div>
    </div>
@endsection
