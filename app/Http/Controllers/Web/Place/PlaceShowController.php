<?php

namespace App\Http\Controllers\Web\Place;

use App\Domain\Seo\Actions\SeoBuilderAction;
use App\Exceptions\Web\Place\Show\PlaceNotFoundException;
use App\Exceptions\Web\Place\Show\PlaceTranslationNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Web\Place\Show\PlaceDetailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlaceShowController extends Controller
{
    public function __construct(
        private readonly PlaceDetailService $placeDetailService,
        private readonly SeoBuilderAction $seoBuilderAction
    ) {}

    /**
     * Afficher la page de détail d'un lieu (/fr/lieux/{slug})
     *
     * @param  Request  $request  La requête HTTP
     * @param  string  $slug  Le slug du lieu dans la locale actuelle
     * @return View|RedirectResponse La vue avec les données du lieu ou redirection en cas d'erreur
     */
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        try {
            // Récupérer la locale actuelle
            $locale = app()->getLocale();

            // Récupérer les détails du lieu via le service
            $place = $this->placeDetailService->getPlaceDetailBySlug($slug, $locale);

            // Génération des données SEO
            $seo = $this->seoBuilderAction->execute('place_show', [
                'place' => $place,
                'locale' => $locale,
            ]);

            // Retourner la vue avec les données
            return view('web.place.show.show', [
                'place' => $place,
                'locale' => $locale,
                'seo' => $seo,
            ]);
        } catch (PlaceNotFoundException $e) {
            // Message traduit pour l'utilisateur
            $userMessage = __('web/pages/place-show.errors.not_found');

            // En mode développement, ajouter les détails techniques
            if (config('app.debug')) {
                $userMessage .= "\n\n[DEBUG] {$e->getMessage()}";
            }

            // Log de l'erreur
            logger()->warning('Place not found', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
                'exception' => $e->getMessage(),
            ]);

            // Redirection vers explore avec message d'erreur
            return redirect()
                ->route('explore.'.app()->getLocale())
                ->with('error', $userMessage);
        } catch (PlaceTranslationNotFoundException $e) {
            // Message traduit pour l'utilisateur
            $userMessage = __('web/pages/place-show.errors.translation_missing');

            // En mode développement, ajouter les détails techniques
            if (config('app.debug')) {
                $userMessage .= "\n\n[DEBUG] {$e->getMessage()}";
            }

            // Log de l'erreur
            logger()->warning('Place translation not found', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
                'exception' => $e->getMessage(),
            ]);

            // Redirection vers explore avec message d'erreur
            return redirect()
                ->route('explore.'.app()->getLocale())
                ->with('error', $userMessage);
        } catch (\Exception $e) {
            // Erreur générique inattendue
            $userMessage = __('errors/general.error_occurred');

            // En mode développement, ajouter les détails techniques
            if (config('app.debug')) {
                $userMessage .= "\n\n[DEBUG] {$e->getMessage()}\n{$e->getTraceAsString()}";
            }

            // Log de l'erreur critique
            logger()->error('Unexpected error in PlaceShowController', [
                'slug' => $slug,
                'locale' => app()->getLocale(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Redirection vers home avec message d'erreur
            return redirect()
                ->back()
                ->with('error', $userMessage);
        }
    }
}
