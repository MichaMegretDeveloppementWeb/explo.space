@extends('layouts.web', ['footer' => true])

@push('head')
    {{-- Google reCAPTCHA v3 --}}
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>

    {{-- Contact page JavaScript (Alpine.js component + reCAPTCHA) --}}
    @vite(['resources/js/web/contact/index.js'])
@endpush


@section('content')

    @include('web.pages.contact.partials.hero')

    @include('web.pages.contact.partials.breadcrumb')

    @include('web.pages.contact.partials.contact-info')

    @include('web.pages.contact.partials.contact-form')

@endsection
