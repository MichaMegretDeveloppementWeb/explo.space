@extends('layouts.web', ['footer' => true])

@section('content')

    @include('web.pages.legal.partials.hero')

    @include('web.pages.legal.partials.breadcrumb')

    @include('web.pages.legal.partials.content')

@endsection
