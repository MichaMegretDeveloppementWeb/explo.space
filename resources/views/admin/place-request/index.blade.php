@extends('layouts.admin')

@section('title', 'Propositions de lieux')

@section('content')
    <div class="mx-auto max-w-[90em] px-4 py-8 sm:px-6 lg:px-8">
        @livewire('admin.place-request.place-request-list.place-request-list-page')
    </div>
@endsection
