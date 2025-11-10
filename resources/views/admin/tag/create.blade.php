@extends('layouts.admin')

@section('title', 'Créer un nouveau tag')

@section('content')
    <livewire:admin.tag.store.tag-store-form
        :tag-id="null"
    />
@endsection
