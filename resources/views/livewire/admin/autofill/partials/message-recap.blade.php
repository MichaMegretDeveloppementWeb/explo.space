@php
    $payload = $message->payload;
    $saved = $payload['saved'] ?? 0;
    $failed = $payload['failed'] ?? 0;
    $skipped = $payload['skipped'] ?? 0;
    $totalCost = $payload['total_cost'] ?? 0;
@endphp

<div class="flex justify-start">
    <div class="max-w-[80%] rounded-2xl bg-gray-50 px-5 py-4">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-900">
                <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-[15px] font-medium text-gray-900">{{ $payload['text'] ?? 'Workflow terminé.' }}</p>

                @if ($saved || $failed || $skipped)
                    <div class="mt-2.5 flex flex-wrap gap-2">
                        @if ($saved)
                            <span class="rounded-full bg-gray-900 px-2.5 py-0.5 text-xs font-medium text-white">
                                {{ $saved }} créé(s)
                            </span>
                        @endif
                        @if ($failed)
                            <span class="rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                {{ $failed }} échoué(s)
                            </span>
                        @endif
                        @if ($skipped)
                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                {{ $skipped }} ignoré(s)
                            </span>
                        @endif
                    </div>
                @endif

                <div class="mt-2.5 flex items-center gap-3">
                    @if ($totalCost > 0)
                        <span class="text-xs text-gray-400">${{ number_format($totalCost, 4) }}</span>
                    @endif

                    <a href="{{ route('admin.autofill.show', $message->workflow_id) }}"
                       class="text-xs font-medium text-gray-500 transition-colors hover:text-gray-900">
                        Voir le détail du workflow →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
