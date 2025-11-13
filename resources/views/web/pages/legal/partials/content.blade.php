{{-- Legal Content --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Introduction --}}
        <div class="prose prose-lg max-w-none mb-12">
            <p class="text-gray-600 leading-relaxed">
                {{ __('web/pages/legal.hero.subtitle') }}
            </p>
        </div>

        {{-- Sections --}}
        <div class="space-y-12">

            {{-- 1. Éditeur du site --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    1. {{ __('web/pages/legal.sections.editor.title') }}
                </h2>
                <div class="prose prose-gray max-w-none">
                    <p class="mb-3">{{ __('web/pages/legal.sections.editor.content.intro') }}</p>
                    <ul class="space-y-2 text-gray-700">
                        <li>{!! __('web/pages/legal.sections.editor.content.name') !!}</li>
                        <li>{{ __('web/pages/legal.sections.editor.content.status') }}</li>
                        <li>{!! __('web/pages/legal.sections.editor.content.email') !!}</li>
                    </ul>
                </div>
            </div>

            {{-- 2. Directeur de la publication --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    2. {{ __('web/pages/legal.sections.publication.title') }}
                </h2>
                <p class="text-gray-700 leading-relaxed">
                    {!! __('web/pages/legal.sections.publication.content') !!}
                </p>
            </div>

            {{-- 3. Développement --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    3. {{ __('web/pages/legal.sections.developer.title') }}
                </h2>
                <div class="prose prose-gray max-w-none">
                    <p class="mb-3">{{ __('web/pages/legal.sections.developer.content.intro') }}</p>
                    <ul class="space-y-2 text-gray-700">
                        <li>{!! __('web/pages/legal.sections.developer.content.name') !!}</li>
                        <li>{{ __('web/pages/legal.sections.developer.content.address') }}</li>
                        <li>{!! __('web/pages/legal.sections.developer.content.website') !!}</li>
                    </ul>
                </div>
            </div>

            {{-- 4. Hébergement --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    4. {{ __('web/pages/legal.sections.hosting.title') }}
                </h2>
                <div class="prose prose-gray max-w-none">
                    <p class="mb-3">{{ __('web/pages/legal.sections.hosting.content.intro') }}</p>
                    <ul class="space-y-2 text-gray-700">
                        <li>{!! __('web/pages/legal.sections.hosting.content.name') !!}</li>
                        <li>{{ __('web/pages/legal.sections.hosting.content.address') }}</li>
                        <li>{!! __('web/pages/legal.sections.hosting.content.website') !!}</li>
                    </ul>
                </div>
            </div>

            {{-- 5. Propriété intellectuelle --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    5. {{ __('web/pages/legal.sections.intellectual_property.title') }}
                </h2>
                <div class="space-y-3 text-gray-700 leading-relaxed">
                    <p>{{ __('web/pages/legal.sections.intellectual_property.content.intro') }}</p>
                    <p>{{ __('web/pages/legal.sections.intellectual_property.content.rights') }}</p>
                    <p class="text-sm text-gray-600 italic">{{ __('web/pages/legal.sections.intellectual_property.content.exception') }}</p>
                </div>
            </div>

            {{-- 6. Données personnelles et RGPD --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    6. {{ __('web/pages/legal.sections.personal_data.title') }}
                </h2>
                <div class="space-y-4">
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('web/pages/legal.sections.personal_data.content.responsible') }}
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('web/pages/legal.sections.personal_data.content.principles') }}
                    </p>

                    <div>
                        <p class="font-semibold text-gray-900 mb-2">
                            {{ __('web/pages/legal.sections.personal_data.content.collected_data.title') }}
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                            @foreach(__('web/pages/legal.sections.personal_data.content.collected_data.items') as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <p class="font-semibold text-gray-900 mb-2">
                            {{ __('web/pages/legal.sections.personal_data.content.usage') }}
                        </p>
                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                            @foreach(__('web/pages/legal.sections.personal_data.content.usage_items') as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="text-gray-700 leading-relaxed">
                        {{ __('web/pages/legal.sections.personal_data.content.retention') }}
                    </p>

                    <p class="text-gray-700 leading-relaxed">
                        {!! __('web/pages/legal.sections.personal_data.content.rights') !!}
                    </p>

                    <p class="text-sm text-gray-600">
                        {!! __('web/pages/legal.sections.personal_data.content.cnil') !!}
                    </p>
                </div>
            </div>

            {{-- 7. Cookies --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    7. {{ __('web/pages/legal.sections.cookies.title') }}
                </h2>
                <div class="space-y-3">
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('web/pages/legal.sections.cookies.content.intro') }}
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        @foreach(__('web/pages/legal.sections.cookies.content.items') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('web/pages/legal.sections.cookies.content.acceptance') }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ __('web/pages/legal.sections.cookies.content.management') }}
                    </p>
                </div>
            </div>

            {{-- 8. Responsabilité --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    8. {{ __('web/pages/legal.sections.liability.title') }}
                </h2>
                <div class="space-y-3 text-gray-700 leading-relaxed">
                    <p>{{ __('web/pages/legal.sections.liability.content.accuracy') }}</p>
                    <p>{{ __('web/pages/legal.sections.liability.content.disclaimer') }}</p>
                    <p>{{ __('web/pages/legal.sections.liability.content.external_links') }}</p>
                </div>
            </div>

            {{-- 9. Droit applicable --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    9. {{ __('web/pages/legal.sections.applicable_law.title') }}
                </h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ __('web/pages/legal.sections.applicable_law.content') }}
                </p>
            </div>

            {{-- 10. Contact --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    10. {{ __('web/pages/legal.sections.contact.title') }}
                </h2>
                <div class="space-y-3">
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('web/pages/legal.sections.contact.content.intro') }}
                    </p>
                    <ul class="space-y-2 text-gray-700">
                        <li>{!! __('web/pages/legal.sections.contact.content.email') !!}</li>
                        <li>
                            <a href="{{ localRoute(__('web/pages/legal.sections.contact.content.form_url')) }}" class="text-blue-600 hover:underline">
                                {{ __('web/pages/legal.sections.contact.content.form') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Dernière mise à jour --}}
        <div class="mt-12 pt-8 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-500">
                {{ __('web/pages/legal.last_updated') }} : {{ now()->translatedFormat('F Y') }}
            </p>
        </div>

    </div>
</section>
