@extends('layouts.admin')

@section('title', 'Modifier la catégorie')

@section('content')
    <livewire:admin.category.store.category-store-form
        :category-id="$categoryId"
    />
@endsection
