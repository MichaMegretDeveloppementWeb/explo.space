@extends('layouts.admin')

@section('title', 'Gestion des lieux')

@section('content')
    <div class="mx-auto max-w-[90em] px-0 py-8 sm:px-6 lg:px-8">
        @livewire('admin.place.place-list.place-list-page')
    </div>
@endsection
