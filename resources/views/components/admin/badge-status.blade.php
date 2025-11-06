@props(['status'])

@php
    use App\Enums\RequestStatus;

    // S'assurer que $status est bien un RequestStatus
    if (is_string($status)) {
        $status = RequestStatus::from($status);
    }
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex w-max items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ' . $status->badgeClasses()]) }}>
    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
        <circle cx="4" cy="4" r="3"/>
    </svg>
    {{ $status->label() }}
</span>
