@extends('layouts.admin')

@section('title', 'Gestion des demandes')

@section('content')
    <div class="mx-auto max-w-[90em] px-4 py-8 sm:px-6 lg:px-8">
        @livewire('admin.edit-request.edit-request-list.edit-request-list-page')
    </div>
@endsection
