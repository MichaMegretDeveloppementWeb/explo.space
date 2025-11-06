{{-- Modal d'erreur réutilisable --}}
<div
    x-data="{ show: false, title: '', message: '', canRetry: false }"
    x-show="show"
    x-on:show-error-modal.window="
        title = $event.detail.title;
        message = $event.detail.message;
        canRetry = $event.detail.canRetry || false;
        show = true;
    "
    x-on:keydown.escape.window="show = false"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="show = false"
        class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
    ></div>

    {{-- Modal --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white shadow-2xl"
    >
        {{-- Icône d'erreur --}}
        <div class="flex items-center justify-center pt-8 pb-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
                <svg class="h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="px-6 pb-6 pt-2 text-center">
            <h3 x-text="title" class="mb-3 text-xl font-semibold text-gray-900"></h3>
            <p x-html="message" class="mb-6 text-sm leading-relaxed text-gray-600"></p>

            {{-- Boutons --}}
            <div class="flex flex-col gap-2 sm:flex-row sm:justify-center">
                <button
                    @click="show = false"
                    type="button"
                    class="rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
                >
                    {{ __('web/common.close') }}
                </button>

                <button
                    x-show="canRetry"
                    @click="show = false; $dispatch('error-modal-retry')"
                    type="button"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    {{ __('web/common.retry') }}
                </button>
            </div>
        </div>
    </div>
</div>
