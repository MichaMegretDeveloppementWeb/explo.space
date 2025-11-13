<!-- Footer - Style Google moderne -->
<footer class="bg-gray-50 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-24">
        <div class="flex flex-wrap items-start justify-around gap-y-16 gap-x-20">
            <!-- À propos -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">{{ __('web/components/footer.sections.about.title') }}</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ localRoute('about') }}#mission" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.about.links.mission') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ localRoute('about') }}#how-it-works" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.about.links.how_it_works') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ localRoute('about') }}#contribute" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.about.links.contribute') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ localRoute('about') }}#philosophy" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.about.links.philosophy') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Explorer -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">{{ __('web/components/footer.sections.explore.title') }}</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ localRoute('explore', ['mode' => 'proximity']) }}" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.explore.links.around_me') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ localRoute('explore', ['mode' => 'worldwide']) }}" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.explore.links.worldwide') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ localRoute('explore', ['mode' => 'worldwide', 'featured' => '1']) }}" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.explore.links.featured') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Communauté -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">{{ __('web/components/footer.sections.community.title') }}</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ localRoute('place_requests.create') }}" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.community.links.suggest_place') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">{{ __('web/components/footer.sections.support.title') }}</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ localRoute('contact') }}" class="text-gray-600 hover:text-blue-600 text-sm transition-colors">
                            {{ __('web/components/footer.sections.support.links.contact_us') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Séparateur -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <!-- Logo et description -->
                <div class="flex items-center space-x-4">
                    <img src="{{ Vite::asset('resources/images/logo_explo_space.webp') }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ __('web/components/footer.brand.name') }}</div>
                        <div class="text-xs text-gray-600">{{ __('web/components/footer.brand.tagline') }}</div>
                    </div>
                </div>

                <!-- Liens légaux et réseaux -->
                <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                    <div class="flex space-x-4 text-xs text-gray-600">
                        <a href="{{ localRoute('legal') }}" class="hover:text-blue-600 transition-colors">
                            {{ __('web/components/footer.legal.legal_notice') }}
                        </a>
                        <a href="{{ localRoute('privacy') }}" class="hover:text-blue-600 transition-colors">
                            {{ __('web/components/footer.legal.privacy') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    © {{ date('Y') }} {{ __('web/components/footer.brand.name') }}. {{ __('web/components/footer.copyright.text') }}
                    {{ __('web/components/footer.copyright.tagline') }}
                </p>
                <!-- Lien admin discret -->
                <p class="mt-2">
                    <a href="{{ route('admin.login') }}" class="text-[10px] text-gray-400 hover:text-gray-600 transition-colors opacity-50 hover:opacity-100 p-4">
                        •
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>
