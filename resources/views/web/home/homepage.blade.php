@extends('layouts.web', ['footer' => true])

@push('head')
    @vite(['resources/js/web/homepage/index.js', 'resources/css/web/homepage/index.css'])
@endpush

@section('content')

    @include('web.home.partials.hero')

    @include('web.home.partials.how-it-works')

    @include('web.home.partials.features')

    @include('web.home.partials.community-contribution')

    @include('web.home.partials.featured-places')

    @include('web.home.partials.latest-places')

    @include('web.home.partials.community-stats')

    @include('web.home.partials.why-cosmap')

    @include('web.home.partials.cta')

@endsection
