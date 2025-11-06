@extends('layouts.admin')

@section('title', "Proposition : {$placeRequest->title}")

@vite(['resources/js/admin/place-request/detail/index.js'])

@section('content')
    {{-- Livewire Component (inclut header + content + modale) --}}
    @livewire('admin.place-request.detail.place-request-detail', [
        'placeRequest' => $placeRequest,
        'photoCount' => $photo_count
    ])
@endsection
