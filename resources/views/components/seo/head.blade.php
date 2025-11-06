@props(['seo', 'breadcrumbs' => []])

@php
    /** @var \App\Domain\Seo\DTO\SeoData $seo */
@endphp

{{-- Méta-données de base --}}
<x-seo.meta-tags :seo="$seo"/>

{{-- Open Graph --}}
<x-seo.open-graph :seo="$seo"/>

{{-- Twitter Cards --}}
<x-seo.twitter-cards :seo="$seo"/>

{{-- Hreflang multilingue --}}
<x-seo.hreflang :alternates="$seo->hreflangs"/>

{{-- JSON-LD (tous les schémas) --}}
<x-seo.json-ld :seo="$seo"/>
