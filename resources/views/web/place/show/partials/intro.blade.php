{{-- Section Introduction - Breadcrumb + Tags + Court résumé --}}
<section class="bg-white pt-12 pb-0">
    <div class="max-w-3xl mx-auto px-6">
        {{-- Breadcrumb minimaliste --}}
        <nav class="mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                <li>
                    <a href="{{ localRoute('home') }}" class="hover:text-gray-900 transition-colors">
                        {{ __('web/common.navigation.home') }}
                    </a>
                </li>
                <li><span class="text-gray-300">/</span></li>
                <li>
                    <a href="{{ localRoute('explore') }}" class="hover:text-gray-900 transition-colors">
                        {{ __('web/common.navigation.explore') }}
                    </a>
                </li>
                <li><span class="text-gray-300">/</span></li>
                <li class="text-gray-900 font-medium">{{ $place->title }}</li>
            </ol>
        </nav>

        {{-- Tags centrés --}}
        @if(count($place->tags) > 0)
            <div class="flex flex-wrap items-center justify-center gap-2 mb-8">
                @foreach($place->tags as $tag)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        {{ $tag['name'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</section>
