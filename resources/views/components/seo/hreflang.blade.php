@props(['alternates' => []])

@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('locales.supported', ['fr', 'en']);
@endphp

{{-- Liens hreflang pour toutes les versions disponibles --}}
@if(!empty($alternates))
    @foreach($alternates as $alternate)
        @if(is_array($alternate) && isset($alternate['hreflang'], $alternate['href']))
            <link rel="alternate" hreflang="{{ $alternate['hreflang'] }}" href="{{ $alternate['href'] }}">
        @elseif(is_string($alternate))
            {{-- Support legacy format for compatibility --}}
            <link rel="alternate" hreflang="{{ $loop->index }}" href="{{ $alternate }}">
        @endif
    @endforeach
@else
    {{-- Fallback : générer les hreflangs pour la page actuelle si pas d'alternates spécifiques --}}
    @foreach($supportedLocales as $locale)
        @if($locale === $currentLocale)
            <link rel="alternate" hreflang="{{ $locale }}" href="{{ url()->current() }}">
        @endif
    @endforeach

    {{-- x-default vers la page actuelle si c'est la langue par défaut --}}
    @if($currentLocale === config('locales.default', 'fr'))
        <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">
    @endif
@endif