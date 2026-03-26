<div class="flex items-baseline gap-2.5 py-1">
    <p class="text-[15px] text-gray-400">{{ rtrim($message->payload['text'] ?? 'Traitement en cours', '.') }}</p>
    <span class="flex items-end gap-[3px] pb-[3px]">
        <span class="inline-block h-[3px] w-[3px] rounded-full bg-gray-400 animate-[progress-bounce_2s_ease-in-out_infinite_0ms]"></span>
        <span class="inline-block h-[3px] w-[3px] rounded-full bg-gray-400 animate-[progress-bounce_2s_ease-in-out_infinite_150ms]"></span>
        <span class="inline-block h-[3px] w-[3px] rounded-full bg-gray-400 animate-[progress-bounce_2s_ease-in-out_infinite_300ms]"></span>
    </span>
</div>

<style>
    @keyframes progress-bounce {
        0%, 100% { transform: translateY(0); }
        15% { transform: translateY(-6px); }
        30% { transform: translateY(0); }
        /* 30% to 100% = rest period between wave cycles */
    }
</style>
