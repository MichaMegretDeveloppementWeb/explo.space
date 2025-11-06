@extends('layouts.web')

@push('head')
    @vite(['resources/js/web/place/index/index.js', 'resources/css/web/place/index/index.css'])
    <script>
        window.PlaceSearchConfig = @json(\App\Support\Config\PlaceSearchConfig::getJsConfig());
    </script>
@endpush

@section('content')
    <div class="bg-gray-50">

        {{-- Contenu principal avec composant Livewire --}}
        <div class="mx-auto">

            <livewire:web.place.index.place-explorer :$filters/>

        </div>
    </div>
@endsection
