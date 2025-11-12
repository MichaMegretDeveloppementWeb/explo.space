@extends('layouts.admin')

@section('title', 'Paramètres - Administration')

@push('head')
<style>
    /* Cacher la scrollbar sur mobile pour les pills */
    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;  /* Chrome, Safari, Opera */
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-0 py-8 text-[0.9em] md:text-md">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Paramètres</h1>
        <p class="mt-2 text-gray-600">Gérez votre profil et les paramètres de votre compte</p>
    </div>

    <!-- Settings Container (Livewire component) -->
    @livewire('admin.settings.settings-container')
</div>
@endsection
