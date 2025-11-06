@php
    // Collecter tous les messages flash dans un array unifié
    $toasts = [];

    if (session()->has('success')) {
        $messages = is_array(session('success')) ? session('success') : [session('success')];
        foreach ($messages as $message) {
            $toasts[] = ['type' => 'success', 'message' => $message, 'id' => uniqid()];
        }
    }

    if (session()->has('error')) {
        $messages = is_array(session('error')) ? session('error') : [session('error')];
        foreach ($messages as $message) {
            $toasts[] = ['type' => 'error', 'message' => $message, 'id' => uniqid()];
        }
    }

    if (session()->has('warning')) {
        $messages = is_array(session('warning')) ? session('warning') : [session('warning')];
        foreach ($messages as $message) {
            $toasts[] = ['type' => 'warning', 'message' => $message, 'id' => uniqid()];
        }
    }

    if (session()->has('info')) {
        $messages = is_array(session('info')) ? session('info') : [session('info')];
        foreach ($messages as $message) {
            $toasts[] = ['type' => 'info', 'message' => $message, 'id' => uniqid()];
        }
    }
@endphp

@if(count($toasts) > 0)
    {{-- Container pour stack de toasts (fixé en haut à droite) --}}
    <div class="fixed top-4 right-4 z-50 w-full max-w-sm space-y-3">
        @foreach($toasts as $toast)
            <div wire:key="toast-{{ $toast['id'] }}"
                 x-data="{
                        show: true,
                        progress: 100,
                        duration: 5000,
                        interval: null
                     }"
                 x-init="
                        interval = setInterval(() => {
                            progress -= 100 / (duration / 50);
                            if (progress <= 0) {
                                show = false;
                                clearInterval(interval);
                            }
                        }, 50);
                     "
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 role="alert"
                 style="display: none;">

                @if($toast['type'] === 'success')
                    {{-- Success Toast --}}
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        {{-- Barre de progression --}}
                        <div class="h-1 bg-gray-100">
                            <div class="h-full bg-green-500 transition-all duration-50 ease-linear"
                                 :style="'width: ' + progress + '%'"></div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Succès</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $toast['message'] }}</p>
                                </div>
                                <button @click="show = false; clearInterval(interval)"
                                        class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($toast['type'] === 'error')
                    {{-- Error Toast --}}
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        {{-- Barre de progression --}}
                        <div class="h-1 bg-gray-100">
                            <div class="h-full bg-red-500 transition-all duration-50 ease-linear"
                                 :style="'width: ' + progress + '%'"></div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Erreur</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $toast['message'] }}</p>
                                </div>
                                <button @click="show = false; clearInterval(interval)"
                                        class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($toast['type'] === 'warning')
                    {{-- Warning Toast --}}
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        {{-- Barre de progression --}}
                        <div class="h-1 bg-gray-100">
                            <div class="h-full bg-yellow-500 transition-all duration-50 ease-linear"
                                 :style="'width: ' + progress + '%'"></div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Attention</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $toast['message'] }}</p>
                                </div>
                                <button @click="show = false; clearInterval(interval)"
                                        class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($toast['type'] === 'info')
                    {{-- Info Toast --}}
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        {{-- Barre de progression --}}
                        <div class="h-1 bg-gray-100">
                            <div class="h-full bg-blue-500 transition-all duration-50 ease-linear"
                                 :style="'width: ' + progress + '%'"></div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Information</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $toast['message'] }}</p>
                                </div>
                                <button @click="show = false; clearInterval(interval)"
                                        class="ml-4 flex-shrink-0 inline-flex text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
