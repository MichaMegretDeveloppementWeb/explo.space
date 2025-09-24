@extends('layouts.web')

@section('title', 'COSMAP - Decouvrez les lieux de la conquete spatiale')
@section('description', 'Explorez un annuaire mondial des objets et lieux lies a la conquete spatiale et a la decouverte de l\'univers. Recherchez autour de vous ou par thematique.')

@section('content')

    @include('web.home.partials.hero')

    @include('web.home.partials.how-it-works')

    @include('web.home.partials.features')

    @include('web.home.partials.community-contribution')

    @include('web.home.partials.featured-places')

    @include('web.home.partials.community-stats')

    @include('web.home.partials.why-cosmap')

    @include('web.home.partials.cta')

@endsection
