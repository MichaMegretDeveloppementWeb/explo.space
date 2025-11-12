@extends('layouts.web', ['footer' => true])

@section('content')
    @include('web.pages.privacy.partials.hero')
    @include('web.pages.privacy.partials.breadcrumb')
    @include('web.pages.privacy.partials.content')
@endsection
