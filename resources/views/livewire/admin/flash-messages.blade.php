{{-- Container pour stack de toasts (fixé en haut à droite) --}}
<div class="fixed top-4 right-[10%] z-50 w-[90%] max-w-sm space-y-3">
    @foreach($messages as $message)
        @php
            // Normaliser les types spéciaux vers les types standards
            $normalizedType = match($message['type']) {
                'photo_success', 'translation_success' => 'success',
                default => $message['type']
            };

            // Configuration par type
            $config = match($normalizedType) {
                'success' => [
                    'color' => 'green',
                    'title' => 'Succès',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                ],
                'error' => [
                    'color' => 'red',
                    'title' => 'Erreur',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />'
                ],
                'warning' => [
                    'color' => 'yellow',
                    'title' => 'Attention',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />'
                ],
                'info' => [
                    'color' => 'blue',
                    'title' => 'Information',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />'
                ],
                default => [
                    'color' => 'blue',
                    'title' => 'Information',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />'
                ]
            };
        @endphp

        <div wire:key="toast-{{ $message['id'] }}"
             x-data="{
                    show: true,
                    progress: 100,
                    duration: 5000,
                    interval: null,
                    messageId: '{{ $message['id'] }}',
                    init() {
                        this.interval = setInterval(() => {
                            this.progress -= 100 / (this.duration / 50);
                            if (this.progress <= 0) {
                                this.close();
                            }
                        }, 50);
                    },
                    close() {
                        this.show = false;
                        if (this.interval) clearInterval(this.interval);
                        setTimeout(() => $wire.removeMessage(this.messageId), 200);
                    }
                 }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 scale-95"
             role="alert"
             x-cloak>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                {{-- Barre de progression --}}
                <div class="h-1 bg-gray-100">
                    <div class="h-full bg-{{ $config['color'] }}-500 transition-all duration-50 ease-linear"
                         :style="'width: ' + progress + '%'"></div>
                </div>

                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-{{ $config['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                {!! $config['icon'] !!}
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $config['title'] }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ $message['message'] }}</p>
                        </div>
                        <button @click="close()"
                                class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
