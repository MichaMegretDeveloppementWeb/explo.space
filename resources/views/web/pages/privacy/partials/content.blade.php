{{-- Privacy Policy Content --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Sections --}}
        <div class="space-y-12">

            {{-- 1. Introduction --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    1. {{ __('web/pages/privacy.sections.intro.title') }}
                </h2>
                <div class="space-y-3 text-gray-700 leading-relaxed">
                    <p>{{ __('web/pages/privacy.sections.intro.content.text1') }}</p>
                    <p>{{ __('web/pages/privacy.sections.intro.content.text2') }}</p>
                </div>
            </div>

            {{-- 2. Responsable du traitement --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    2. {{ __('web/pages/privacy.sections.responsible.title') }}
                </h2>
                <div class="space-y-2 text-gray-700">
                    <p>{{ __('web/pages/privacy.sections.responsible.content.text') }}</p>
                    <ul class="space-y-1 ml-4">
                        <li>{!! __('web/pages/privacy.sections.responsible.content.name') !!}</li>
                        <li>{!! __('web/pages/privacy.sections.responsible.content.email') !!}</li>
                    </ul>
                </div>
            </div>

            {{-- 3. Données collectées --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    3. {{ __('web/pages/privacy.sections.data_collected.title') }}
                </h2>
                <p class="mb-4 text-gray-700">{{ __('web/pages/privacy.sections.data_collected.content.intro') }}</p>
                @foreach(__('web/pages/privacy.sections.data_collected.content.categories') as $category)
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $category['title'] }}</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-700">
                            @foreach($category['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>

            {{-- 4. Utilisation des données --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    4. {{ __('web/pages/privacy.sections.data_usage.title') }}
                </h2>
                <p class="mb-3 text-gray-700">{{ __('web/pages/privacy.sections.data_usage.content.intro') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    @foreach(__('web/pages/privacy.sections.data_usage.content.items') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- 5. Base légale du traitement --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    5. {{ __('web/pages/privacy.sections.legal_basis.title') }}
                </h2>
                <p class="mb-3 text-gray-700">{{ __('web/pages/privacy.sections.legal_basis.content.intro') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    @foreach(__('web/pages/privacy.sections.legal_basis.content.items') as $item)
                        <li>{!! $item !!}</li>
                    @endforeach
                </ul>
            </div>

            {{-- 6. Durée de conservation --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    6. {{ __('web/pages/privacy.sections.data_retention.title') }}
                </h2>
                <p class="mb-3 text-gray-700">{{ __('web/pages/privacy.sections.data_retention.content.intro') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    @foreach(__('web/pages/privacy.sections.data_retention.content.items') as $item)
                        <li>{!! $item !!}</li>
                    @endforeach
                </ul>
            </div>

            {{-- 7. Partage des données --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    7. {{ __('web/pages/privacy.sections.data_sharing.title') }}
                </h2>
                <p class="mb-3 text-gray-700">{{ __('web/pages/privacy.sections.data_sharing.content.intro') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700 mb-3">
                    @foreach(__('web/pages/privacy.sections.data_sharing.content.items') as $item)
                        <li>{!! $item !!}</li>
                    @endforeach
                </ul>
                <p class="text-gray-700">{{ __('web/pages/privacy.sections.data_sharing.content.text') }}</p>
            </div>

            {{-- 8. Vos droits --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    8. {{ __('web/pages/privacy.sections.your_rights.title') }}
                </h2>
                <p class="mb-3 text-gray-700">{{ __('web/pages/privacy.sections.your_rights.content.intro') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700 mb-4">
                    @foreach(__('web/pages/privacy.sections.your_rights.content.items') as $item)
                        <li>{!! $item !!}</li>
                    @endforeach
                </ul>
                <p class="text-gray-700 mb-2">{!! __('web/pages/privacy.sections.your_rights.content.how_to') !!}</p>
                <p class="text-sm text-gray-600">{{ __('web/pages/privacy.sections.your_rights.content.deadline') }}</p>
            </div>

            {{-- 9. Sécurité des données --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    9. {{ __('web/pages/privacy.sections.security.title') }}
                </h2>
                <p class="mb-3 text-gray-700">{{ __('web/pages/privacy.sections.security.content.intro') }}</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    @foreach(__('web/pages/privacy.sections.security.content.items') as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- 10. Cookies et traceurs --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    10. {{ __('web/pages/privacy.sections.cookies.title') }}
                </h2>
                <p class="mb-4 text-gray-700">{{ __('web/pages/privacy.sections.cookies.content.intro') }}</p>

                <div class="mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('web/pages/privacy.sections.cookies.content.essential.title') }}</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        @foreach(__('web/pages/privacy.sections.cookies.content.essential.items') as $item)
                            <li>{!! $item !!}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="mb-4">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ __('web/pages/privacy.sections.cookies.content.functional.title') }}</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        @foreach(__('web/pages/privacy.sections.cookies.content.functional.items') as $item)
                            <li>{!! $item !!}</li>
                        @endforeach
                    </ul>
                </div>

                <p class="text-sm text-gray-600">{{ __('web/pages/privacy.sections.cookies.content.management') }}</p>
            </div>

            {{-- 11. Transferts internationaux --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    11. {{ __('web/pages/privacy.sections.international_transfer.title') }}
                </h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ __('web/pages/privacy.sections.international_transfer.content') }}
                </p>
            </div>

            {{-- 12. Données des mineurs --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    12. {{ __('web/pages/privacy.sections.minors.title') }}
                </h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ __('web/pages/privacy.sections.minors.content') }}
                </p>
            </div>

            {{-- 13. Modifications de la politique --}}
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    13. {{ __('web/pages/privacy.sections.updates.title') }}
                </h2>
                <p class="text-gray-700 leading-relaxed">
                    {{ __('web/pages/privacy.sections.updates.content') }}
                </p>
            </div>

            {{-- 14. Réclamations --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    14. {{ __('web/pages/privacy.sections.complaints.title') }}
                </h2>
                <div class="space-y-2 text-gray-700">
                    <p>{{ __('web/pages/privacy.sections.complaints.content.text') }}</p>
                    <ul class="space-y-1 ml-4">
                        <li>{!! __('web/pages/privacy.sections.complaints.content.cnil') !!}</li>
                        <li>{{ __('web/pages/privacy.sections.complaints.content.address') }}</li>
                        <li>{!! __('web/pages/privacy.sections.complaints.content.website') !!}</li>
                    </ul>
                </div>
            </div>

            {{-- 15. Contact --}}
            <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    15. {{ __('web/pages/privacy.sections.contact.title') }}
                </h2>
                <div class="space-y-2 text-gray-700">
                    <p>{{ __('web/pages/privacy.sections.contact.content.intro') }}</p>
                    <ul class="space-y-1">
                        <li>{!! __('web/pages/privacy.sections.contact.content.email') !!}</li>
                        <li>
                            <a href="{{ localRoute(__('web/pages/privacy.sections.contact.content.form_url')) }}" class="text-blue-600 hover:underline">
                                {{ __('web/pages/privacy.sections.contact.content.form') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Dernière mise à jour --}}
        <div class="mt-12 pt-8 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-500">
                {{ __('web/pages/privacy.last_updated') }} : {{ now()->translatedFormat('F Y') }}
            </p>
        </div>

    </div>
</section>
