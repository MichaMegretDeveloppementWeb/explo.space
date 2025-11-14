@extends('layouts.admin')

@section('title', 'Diagnostic serveur - Administration')

@section('content')
<div class="max-w-7xl mx-auto px-0 py-8 text-[0.9em] md:text-md">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Diagnostic serveur</h1>
        <p class="mt-2 text-gray-600">Vérification de la configuration pour l'upload d'images</p>
    </div>

    <!-- Configuration PHP -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Configuration PHP</h2>
        </div>
        <div class="px-6 py-4">
            <dl class="space-y-3">
                @foreach($phpConfig as $key => $value)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="font-medium text-gray-700">{{ $key }}</dt>
                        <dd class="text-gray-900 font-mono text-sm">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>

    <!-- Configuration Laravel/Livewire -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Configuration Laravel/Livewire</h2>
        </div>
        <div class="px-6 py-4">
            <dl class="space-y-3">
                @foreach($laravelConfig as $key => $value)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="font-medium text-gray-700">{{ $key }}</dt>
                        <dd class="text-gray-900 font-mono text-sm">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>

    <!-- Vérifications critiques -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Vérifications critiques</h2>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-4">
                @foreach($checks as $check)
                    <div class="flex items-start space-x-3 p-4 rounded-lg {{ $check['status'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                        <div class="flex-shrink-0 pt-0.5">
                            @if($check['status'])
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold {{ $check['status'] ? 'text-green-900' : 'text-red-900' }}">
                                {{ $check['name'] }}
                            </h3>
                            <p class="mt-1 text-sm {{ $check['status'] ? 'text-green-700' : 'text-red-700' }}">
                                {{ $check['message'] }}
                            </p>
                            @if(!$check['status'] && $check['fix'])
                                <div class="mt-2 p-3 bg-white rounded border {{ $check['status'] ? 'border-green-200' : 'border-red-200' }}">
                                    <p class="text-xs font-semibold text-gray-700 mb-1">Solution :</p>
                                    <code class="text-xs text-gray-900 break-all">{{ $check['fix'] }}</code>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Résumé global -->
    <div class="mt-6 p-4 rounded-lg {{ collect($checks)->every('status') ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if(collect($checks)->every('status'))
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold {{ collect($checks)->every('status') ? 'text-green-900' : 'text-yellow-900' }}">
                    @if(collect($checks)->every('status'))
                        ✓ Toutes les vérifications sont passées avec succès
                    @else
                        ⚠ {{ collect($checks)->where('status', false)->count() }} vérification(s) en échec
                    @endif
                </h3>
                <p class="mt-1 text-sm {{ collect($checks)->every('status') ? 'text-green-700' : 'text-yellow-700' }}">
                    @if(collect($checks)->every('status'))
                        La configuration du serveur est optimale pour l'upload d'images.
                    @else
                        Certains paramètres doivent être ajustés pour garantir le bon fonctionnement de l'upload d'images.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
