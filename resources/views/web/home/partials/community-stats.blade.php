<!-- Section Statistiques communaute -->
<section class="py-12 sm:py-16 md:py-20 lg:pb-32 bg-gradient-to-br from-slate-50 to-blue-50 px-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-3 sm:mb-4 px-2 sm:px-0">
                {{ __('web/pages/home.community_stats.title') }}
            </h2>
            <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl sm:max-w-3xl mx-auto px-2 sm:px-0">
                {{ __('web/pages/home.community_stats.subtitle') }}
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-6 md:gap-8 max-w-5xl mx-auto">
            <!-- Stat 1: Lieux référencés -->
            <div class="flex-1 min-w-[280px] max-w-[360px] text-center">
                <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 md:p-10 shadow-sm hover:shadow-md transition-shadow h-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gray-50 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5">
                        <x-heroicon-o-map-pin class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 text-blue-500" />
                    </div>
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-2 sm:mb-3">{{ $stats['places_count'] }}</div>
                    <div class="text-sm sm:text-base text-gray-600">{{ __('web/pages/home.community_stats.stats.places.label') }}</div>
                </div>
            </div>

            <!-- Stat 2: Membres actifs -->
            <div class="flex-1 min-w-[280px] max-w-[360px] text-center">
                <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 md:p-10 shadow-sm hover:shadow-md transition-shadow h-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gray-50 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5">
                        <x-heroicon-o-users class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 text-purple-500" />
                    </div>
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-2 sm:mb-3">{{ $stats['active_members'] }}</div>
                    <div class="text-sm sm:text-base text-gray-600">{{ __('web/pages/home.community_stats.stats.members.label') }}</div>
                </div>
            </div>

            <!-- Stat 3: Propositions de lieux -->
            <div class="flex-1 min-w-[280px] max-w-[360px] text-center">
                <div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 md:p-10 shadow-sm hover:shadow-md transition-shadow h-full">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-gray-50 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-5">
                        <x-heroicon-o-document-plus class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 text-orange-500" />
                    </div>
                    <div class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-2 sm:mb-3">{{ $stats['total_submissions'] }}</div>
                    <div class="text-sm sm:text-base text-gray-600">{{ __('web/pages/home.community_stats.stats.submissions.label') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>
