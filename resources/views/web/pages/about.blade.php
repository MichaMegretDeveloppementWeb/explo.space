@extends('layouts.web', ['footer' => true])

@section('content')

    @include('web.pages.about.partials.hero')

    @include('web.pages.about.partials.breadcrumb')

    @include('web.pages.about.partials.mission')

    @include('web.pages.about.partials.how-it-works')

    @include('web.pages.about.partials.contribute')

    @include('web.pages.about.partials.philosophy')

@endsection
