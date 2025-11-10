@extends('layouts.admin')

@section('title', 'Modifier le tag')

@section('content')
    <livewire:admin.tag.store.tag-store-form
        :tag-id="$tagId"
    />
@endsection
