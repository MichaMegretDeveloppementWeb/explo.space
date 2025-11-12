@props([
    'items' => [], // Collection de Category
])

@if($items->isEmpty())
    <p class="text-sm text-gray-500 italic">Aucune catégorie</p>
@else
    <div class="flex flex-wrap gap-2">
        @foreach($items as $item)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">
                {{ $item->name }}
            </span>
        @endforeach
    </div>
@endif
