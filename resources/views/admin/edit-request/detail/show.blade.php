@extends('layouts.admin')

@section('title', $editRequest->getTypeLabel() . " : " . $editRequest->place->translate('fr')->title)

@vite(['resources/js/admin/edit-request/detail/index.js'])

@section('content')
    {{-- Livewire Component (inclut header + content + modale) --}}
    @livewire('admin.edit-request.detail.edit-request-detail', [
        'editRequest' => $editRequest
    ])
@endsection
