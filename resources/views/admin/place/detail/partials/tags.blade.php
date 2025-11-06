@props(['place', 'tagCount'])

<x-admin.place.detail.info-card
    title="Tags"
    icon="heroicon-o-tag"
    :count="$tagCount">

    <x-admin.place.detail.tag-list
        :items="$place->tags"
        type="tag"
        locale="fr" />
</x-admin.place.detail.info-card>
