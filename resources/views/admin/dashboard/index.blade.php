@extends('layouts.admin')

@section('title', 'Tableau de bord - Administration')

@section('content')
{{-- Header moderne --}}
<x-admin.dashboard.header
    title="Tableau de bord"
    description="Bienvenue dans l'espace d'administration d'Explo.space"
/>

<div class="max-w-[90rem] mx-auto py-8">
    <div class="space-y-8">
        {{-- Cartes de statistiques - Demandes en attente --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Propositions de lieux --}}
            <x-admin.dashboard.stat-card
                title="Propositions de lieux en attente"
                :value="$stats['pending_place_requests']"
                icon-color="orange"
                :link="route('admin.place-requests.index', ['status' => 'pending,submitted'])"
            >
                <x-slot name="icon">
                    <x-heroicon-o-map-pin class="h-6 w-6" />
                </x-slot>
            </x-admin.dashboard.stat-card>

            {{-- Demandes modifications --}}
            <x-admin.dashboard.stat-card
                title="Modifications/Signalements en attente"
                :value="$stats['pending_edit_requests']"
                icon-color="orange"
                link="#"
            >
                <x-slot name="icon">
                    <x-heroicon-o-pencil-square class="h-6 w-6" />
                </x-slot>
            </x-admin.dashboard.stat-card>
        </div>

        {{-- Cartes de statistiques - Contenu principal --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Lieux enregistrés --}}
            <x-admin.dashboard.stat-card
                title="Lieux"
                :value="$stats['total_places']"
                icon-color="blue"
                :link="route('admin.places.index')"
                class="md:col-span-full lg:col-auto"
            >
                <x-slot name="icon">
                    <x-heroicon-o-map-pin class="h-6 w-6" />
                </x-slot>
            </x-admin.dashboard.stat-card>

            {{-- Tags actifs --}}
            <x-admin.dashboard.stat-card
                title="Tags"
                :value="$stats['total_tags']"
                icon-color="purple"
                link="#"
            >
                <x-slot name="icon">
                    <x-heroicon-o-tag class="h-6 w-6" />
                </x-slot>
            </x-admin.dashboard.stat-card>

            {{-- Catégories --}}
            <x-admin.dashboard.stat-card
                title="Catégories"
                :value="$stats['total_categories']"
                icon-color="indigo"
                link="#"
            >
                <x-slot name="icon">
                    <x-heroicon-o-folder class="h-6 w-6" />
                </x-slot>
            </x-admin.dashboard.stat-card>
        </div>

        {{-- Tables des demandes récentes en attente (empilées verticalement) --}}
        <div class="space-y-6">
            {{-- Dernières propositions de lieux --}}
            <x-admin.dashboard.place-requests-table
                :requests="$recentPlaceRequests"
                title="Dernières propositions de lieux"
            />

            {{-- Dernières demandes de modifications/signalements --}}
            <x-admin.dashboard.edit-requests-table
                :requests="$recentEditRequests"
                title="Dernières demandes de modifications/signalements"
            />
        </div>
    </div>
</div>
@endsection
