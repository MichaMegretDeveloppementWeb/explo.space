@extends('layouts.admin')

@section('title', 'Créer un nouveau lieu')

@push('head')
    @vite(['resources/js/admin/place/store/index.js', 'resources/css/admin/place/store/index.css'])
@endpush

@section('content')
    <livewire:admin.place.store.place-store-form
        :place-id="null"
        :place-request-id="$placeRequestId"
        :edit-request-id="null"
    />
@endsection
