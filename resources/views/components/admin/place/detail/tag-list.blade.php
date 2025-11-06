@props([
    'items' => [], // Collection de Tag ou Category
    'type' => 'tag', // tag | category
    'locale' => 'fr',
])

@if($items->isEmpty())
    <p class="text-sm text-gray-500 italic">Aucun élément</p>
@else
    <div class="flex flex-wrap gap-2">
        @foreach($items as $item)
            @php
                $translation = $item->translations->firstWhere('locale', $locale);
                $name = $translation?->name ?? "Sans nom ({$locale})";
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">
                {{ $name }}
            </span>
        @endforeach
    </div>
@endif
