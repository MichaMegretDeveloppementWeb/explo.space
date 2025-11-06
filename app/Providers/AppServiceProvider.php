<?php

namespace App\Providers;

use App\Contracts\Repositories\Admin\Category\CategorySelectionRepositoryInterface;
use App\Contracts\Repositories\Admin\Dashboard\DashboardStatsRepositoryInterface;
use App\Contracts\Repositories\Admin\Place\Create\PlaceCreateRepositoryInterface;
use App\Contracts\Repositories\Admin\Place\Detail\PlaceDetailRepositoryInterface as AdminPlaceDetailRepositoryInterface;
use App\Contracts\Repositories\Admin\Place\Edit\PlaceUpdateRepositoryInterface;
use App\Contracts\Repositories\Admin\Place\Management\PlaceDeleteRepositoryInterface;
use App\Contracts\Repositories\Admin\Place\PlaceListRepositoryInterface;
use App\Contracts\Repositories\Admin\PlaceRequest\PlaceRequestListRepositoryInterface;
use App\Contracts\Repositories\Admin\Tag\TagSelectionRepositoryInterface as AdminTagSelectionRepositoryInterface;
use App\Contracts\Repositories\Web\Place\EditRequest\EditRequestCreateRepositoryInterface;
use App\Contracts\Repositories\Web\Place\Index\PlaceExplorationRepositoryInterface;
use App\Contracts\Repositories\Web\Place\PhotoSuggestion\PhotoSuggestionCreateRepositoryInterface;
use App\Contracts\Repositories\Web\Place\PlaceRequest\PlaceRequestCreateRepositoryInterface;
use App\Contracts\Repositories\Web\Place\PreviewModal\PlacePreviewRepositoryInterface;
use App\Contracts\Repositories\Web\Place\Show\PlaceDetailRepositoryInterface as WebPlaceDetailRepositoryInterface;
use App\Contracts\Repositories\Web\Tag\TagSelectionRepositoryInterface as WebTagSelectionRepositoryInterface;
use App\Contracts\Services\Admin\Auth\AdminAuthenticationServiceInterface;
use App\Contracts\Services\GeocodingServiceInterface;
use App\Contracts\Translation\TranslationStrategyInterface;
use App\Repositories\Admin\Category\CategorySelectionRepository;
use App\Repositories\Admin\Dashboard\DashboardStatsRepository;
use App\Repositories\Admin\Place\Create\PlaceCreateRepository;
use App\Repositories\Admin\Place\Detail\PlaceDetailRepository as AdminPlaceDetailRepository;
use App\Repositories\Admin\Place\Edit\PlaceUpdateRepository;
use App\Repositories\Admin\Place\Management\PlaceDeleteRepository;
use App\Repositories\Admin\Place\PlaceListRepository;
use App\Repositories\Admin\PlaceRequest\PlaceRequestListRepository;
use App\Repositories\Admin\Tag\TagSelectionRepository as AdminTagSelectionRepository;
use App\Repositories\Web\Place\EditRequest\EditRequestCreateRepository;
use App\Repositories\Web\Place\Index\PlaceExplorationRepository;
use App\Repositories\Web\Place\PhotoSuggestion\PhotoSuggestionCreateRepository;
use App\Repositories\Web\Place\PlaceRequest\PlaceRequestCreateRepository;
use App\Repositories\Web\Place\PreviewModal\PlacePreviewRepository;
use App\Repositories\Web\Place\Show\PlaceDetailRepository as WebPlaceDetailRepository;
use App\Repositories\Web\Tag\TagSelectionRepository as WebTagSelectionRepository;
use App\Services\Admin\Auth\AdminAuthenticationService;
use App\Strategies\Geocoding\GeocodingResolver;
use App\Strategies\Translation\TranslationResolver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(
            PlaceExplorationRepositoryInterface::class,
            PlaceExplorationRepository::class
        );

        $this->app->bind(
            WebTagSelectionRepositoryInterface::class,
            WebTagSelectionRepository::class
        );

        $this->app->bind(
            PlaceRequestCreateRepositoryInterface::class,
            PlaceRequestCreateRepository::class
        );

        $this->app->bind(
            PlacePreviewRepositoryInterface::class,
            PlacePreviewRepository::class
        );

        $this->app->bind(
            WebPlaceDetailRepositoryInterface::class,
            WebPlaceDetailRepository::class
        );

        $this->app->bind(
            EditRequestCreateRepositoryInterface::class,
            EditRequestCreateRepository::class
        );

        $this->app->bind(
            PhotoSuggestionCreateRepositoryInterface::class,
            PhotoSuggestionCreateRepository::class
        );

        $this->app->bind(
            AdminTagSelectionRepositoryInterface::class,
            AdminTagSelectionRepository::class
        );

        $this->app->bind(
            CategorySelectionRepositoryInterface::class,
            CategorySelectionRepository::class
        );

        $this->app->bind(
            DashboardStatsRepositoryInterface::class,
            DashboardStatsRepository::class
        );

        $this->app->bind(
            PlaceListRepositoryInterface::class,
            PlaceListRepository::class
        );

        $this->app->bind(
            AdminPlaceDetailRepositoryInterface::class,
            AdminPlaceDetailRepository::class
        );

        $this->app->bind(
            PlaceDeleteRepositoryInterface::class,
            PlaceDeleteRepository::class
        );

        $this->app->bind(
            PlaceCreateRepositoryInterface::class,
            PlaceCreateRepository::class
        );

        $this->app->bind(
            PlaceUpdateRepositoryInterface::class,
            PlaceUpdateRepository::class
        );

        $this->app->bind(
            PlaceRequestListRepositoryInterface::class,
            PlaceRequestListRepository::class
        );

        // Services
        $this->app->bind(
            AdminAuthenticationServiceInterface::class,
            AdminAuthenticationService::class
        );

        // Geocoding Strategy Pattern
        $this->app->singleton(GeocodingResolver::class);

        // Binding du contrat vers le driver par défaut (configurable)
        $this->app->bind(GeocodingServiceInterface::class, function ($app) {
            $driver = config('geocoding.default_provider');

            return $app->make(GeocodingResolver::class)->via($driver);
        });

        // Translation Strategy Pattern
        $this->app->singleton(TranslationResolver::class);

        // Binding du contrat vers le driver par défaut (configurable)
        $this->app->bind(TranslationStrategyInterface::class, function ($app) {
            $driver = config('translation.default_provider');

            return $app->make(TranslationResolver::class)->via($driver);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Ajouter un placeholder custom :maxMo (en plus de :max standard)
        Validator::replacer('max', function ($message, $attribute, $rule, $parameters, $validator) {
            // Vérifier si le message contient :maxMo (placeholder custom)
            if (str_contains($message, ':maxMo')) {
                // Convertir Ko en Mo (arrondi à 1 décimale)
                $maxKB = (int) ($parameters[0] ?? 0);
                $maxMB = round($maxKB / 1024, 1);

                // Remplacer :maxMo par la valeur en Mo
                $message = str_replace(':maxMo', (string) $maxMB, $message);
            }

            // Toujours remplacer :max par la valeur (comportement par défaut de Laravel)
            $message = str_replace(':max', $parameters[0] ?? '', $message);

            return $message;
        });
    }
}
