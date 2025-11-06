@props(['place', 'photoCount'])

<x-admin.place.detail.info-card
    title="Photos"
    icon="heroicon-o-photo"
    :count="$photoCount">

    <x-admin.place.detail.photo-gallery :photos="$place->photos" :place-id="$place->id" />

</x-admin.place.detail.info-card>
