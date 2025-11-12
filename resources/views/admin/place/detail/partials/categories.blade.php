@props(['place', 'categoryCount'])

<x-admin.place.detail.info-card
    title="Catégories"
    icon="heroicon-o-folder"
    :count="$categoryCount">

    <x-admin.place.detail.category-list
        :items="$place->categories" />
</x-admin.place.detail.info-card>
