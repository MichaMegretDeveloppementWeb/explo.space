@props(['place', 'categoryCount'])

<x-admin.place.detail.info-card
    title="Catégories"
    icon="heroicon-o-folder"
    :count="$categoryCount">

    <x-admin.place.detail.tag-list
        :items="$place->categories"
        type="category"
        locale="fr" />
</x-admin.place.detail.info-card>
