@extends('layouts.admin')

@section('title', 'Créer une nouvelle catégorie')

@section('content')
    <livewire:admin.category.store.category-store-form
        :category-id="null"
    />
@endsection
