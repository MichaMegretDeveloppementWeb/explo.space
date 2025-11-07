@extends('layouts.admin')

@section('title', 'Modifier le lieu')

@push('head')
    @vite(['resources/js/admin/place/store/index.js', 'resources/css/admin/place/store/index.css'])
@endpush

@section('content')
    <livewire:admin.place.store.place-store-form
        :place-id="$place->id"
        :place-request-id="null"
        :edit-request-id="$editRequestId"
    />
@endsection
