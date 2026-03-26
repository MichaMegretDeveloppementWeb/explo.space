@php
    $isUser = $message->role === \App\Enums\AutofillMessageRole::User;
@endphp

<div class="flex {{ $isUser ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-[80%] rounded-2xl px-4 py-3 {{ $isUser ? 'bg-gray-900 text-white' : 'bg-gray-50 text-gray-700' }}">
        <p class="text-[15px] leading-relaxed">{{ $message->payload['text'] ?? '' }}</p>
    </div>
</div>
