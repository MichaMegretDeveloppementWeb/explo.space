@extends('layouts.admin')

@section('title', 'Gestion des tags')

@section('content')
    <div class="mx-auto max-w-[90em] px-4 py-8 sm:px-6 lg:px-8">
        @livewire('admin.tag.tag-list.tag-list-page')
    </div>
@endsection
