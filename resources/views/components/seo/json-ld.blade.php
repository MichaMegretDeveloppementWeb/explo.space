@props(['seo'])

@php
    /** @var \App\Domain\Seo\DTO\SeoData $seo */
@endphp

{{-- Affichage de tous les schémas JSON-LD --}}
@foreach($seo->jsonLdSchemas as $schema)
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endforeach
