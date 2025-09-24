# Système de gestion multilingue - COSMAP

## Table des matières

1. [Vue d'ensemble et objectifs](#1-vue-densemble-et-objectifs)
2. [Architecture des URLs](#2-architecture-des-urls)
3. [Configuration et locale](#3-configuration-et-locale)
4. [Modèle de données multilingue](#4-modèle-de-données-multilingue)
5. [Routes localisées](#5-routes-localisées)
6. [Middleware et gestion de la locale](#6-middleware-et-gestion-de-la-locale)
7. [Helpers d'URLs localisées](#7-helpers-durls-localisées)
8. [Contrôleurs multilingues](#8-contrôleurs-multilingues)
9. [Workflow visiteur : détection de langue](#9-workflow-visiteur--détection-de-langue)
10. [Interface admin : onglets de traduction](#10-interface-admin--onglets-de-traduction)
11. [Traduction automatique](#11-traduction-automatique)
12. [Sélecteur de langue](#12-sélecteur-de-langue)
13. [SEO multilingue](#13-seo-multilingue)
14. [Sitemap multilingue](#14-sitemap-multilingue)
15. [Recherche et performance](#15-recherche-et-performance)
16. [Tests et validation](#16-tests-et-validation)
17. [Plan d'implémentation](#17-plan-dimplémentation)

---

## 1. Vue d'ensemble et objectifs

### 1.1 Principe fondamental

COSMAP implémente un système multilingue complet avec :
- **URLs distinctes par langue** : `/fr/lieux/{slug-fr}` vs `/en/places/{slug-en}`
- **Segments traduits** : non seulement le préfixe `/fr/en`, mais aussi les segments de chemin
- **Slugs traduits** : chaque entité a un slug spécifique par langue
- **Données normalisées** : séparation contenu invariant (GPS, IDs) et traductions
- **Workflow visiteur-admin** : détection automatique de langue avec interface d'administration multilingue

### 1.2 Langues supportées

- **Phase 1** : Français (par défaut) + Anglais
- **Architecture extensible** : préparation pour langues futures (ES, DE, etc.)
- **Fallback** : Français comme langue de secours

### 1.3 Entités traduites

**Places** :
- Champs traduits : `title`, `description`, `practical_info`, `slug`
- Champs invariants : `latitude`, `longitude`, `address`, `admin_id`, `is_featured`

**Categories** (admin interne) :
- Champs traduits : `name`, `description`, `slug`
- Champs invariants : `color`, `is_active`

**Tags** :
- Champs traduits : `name`, `description`, `slug`
- Champs invariants : `color`, `is_active`

---

## 2. Architecture des URLs

### 2.1 Structure générale

```
FR : https://cosmap.com/fr/{segment-fr}/{slug-fr}
EN : https://cosmap.com/en/{segment-en}/{slug-en}
```

### 2.2 Exemples concrets

**Homepage** :
- `/fr/` (français)
- `/en/` (anglais)

**Liste des lieux** :
- `/fr/lieux` (français)
- `/en/places` (anglais)

**Fiche lieu** :
- `/fr/lieux/centre-spatial-kennedy` (français)
- `/en/places/kennedy-space-center` (anglais)

**Page explorer** :
- `/fr/explorer` (français)
- `/en/explore` (anglais)

**Proposer un lieu** :
- `/fr/proposer-lieu` (français)
- `/en/propose-place` (anglais)

### 2.3 Redirection automatique

- **Détection langue navigateur** : suggestion de redirection vers langue appropriée
- **URL sans préfixe** : `https://cosmap.com/` → redirection vers `/fr/` (langue par défaut)
- **Conservation du contexte** : redirection vers page équivalente si traduite

---

## 3. Configuration et locale

### 3.1 Configuration Laravel

**config/app.php** :
```php
'locale' => 'fr',
'fallback_locale' => 'fr',
```

**config/locales.php** (nouveau fichier) :
```php
return [
    'supported' => ['fr', 'en'],
    'default' => 'fr',
    'fallback' => 'fr',

    // Segments traduits par route
    'segments' => [
        'fr' => [
            'places' => 'lieux',
            'explore' => 'explorer',
            'propose_place' => 'proposer-lieu',
            'about' => 'a-propos',
            'contact' => 'contact',
            'legal' => 'mentions-legales',
            'privacy' => 'politique-confidentialite',
            'terms' => 'cgu',
            'features' => 'fonctionnalites',
        ],
        'en' => [
            'places' => 'places',
            'explore' => 'explore',
            'propose_place' => 'propose-place',
            'about' => 'about',
            'contact' => 'contact',
            'legal' => 'legal-notice',
            'privacy' => 'privacy-policy',
            'terms' => 'terms-of-use',
            'features' => 'features',
        ],
    ],

    // Cookie de persistance
    'cookie_name' => 'cosmap_locale',
    'cookie_lifetime' => 60 * 24 * 365, // 1 an
];
```

---

## 4. Modèle de données multilingue

### 4.1 Structure des tables

**Tables principales** (données invariantes) :
- `places` : `id`, `latitude`, `longitude`, `address`, `admin_id`, `is_featured`, `created_at`, `updated_at`
- `categories` : `id`, `color`, `is_active`, `created_at`, `updated_at`
- `tags` : `id`, `color`, `is_active`, `created_at`, `updated_at`

**Tables de traduction** :
- `place_translations` : traductions pour Place
- `category_translations` : traductions pour Category
- `tag_translations` : traductions pour Tag

### 4.2 Migration place_translations

```php
Schema::create('place_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('place_id')->constrained()->cascadeOnDelete();
    $table->string('locale', 5); // 'fr' | 'en'
    $table->string('title');
    $table->string('slug');
    $table->text('description');
    $table->text('practical_info')->nullable();
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->string('source_hash', 40)->nullable(); // Pour traduction auto
    $table->timestamps();

    // Index et contraintes
    $table->unique(['place_id', 'locale']);
    $table->unique(['locale', 'slug']);
    $table->index(['locale', 'status']);

    // Configuration base
    $table->engine('InnoDB');
    $table->charset('utf8mb4');
    $table->collation('utf8mb4_general_ci');
});
```

### 4.3 Migration category_translations

```php
Schema::create('category_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->string('locale', 5);
    $table->string('name');
    $table->string('slug');
    $table->text('description')->nullable();
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->string('source_hash', 40)->nullable();
    $table->timestamps();

    $table->unique(['category_id', 'locale']);
    $table->unique(['locale', 'slug']);
    $table->index(['locale', 'status']);

    $table->engine('InnoDB');
    $table->charset('utf8mb4');
    $table->collation('utf8mb4_general_ci');
});
```

### 4.4 Migration tag_translations

```php
Schema::create('tag_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    $table->string('locale', 5);
    $table->string('name');
    $table->string('slug');
    $table->text('description')->nullable();
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->string('source_hash', 40)->nullable();
    $table->timestamps();

    $table->unique(['tag_id', 'locale']);
    $table->unique(['locale', 'slug']);
    $table->index(['locale', 'status']);

    $table->engine('InnoDB');
    $table->charset('utf8mb4');
    $table->collation('utf8mb4_general_ci');
});
```

### 4.5 Modèles Eloquent

**app/Models/Place.php** :
```php
class Place extends Model
{
    protected $fillable = [
        'latitude', 'longitude', 'address', 'admin_id', 'is_featured'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_featured' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(PlaceTranslation::class);
    }

    public function translate(string $locale)
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function getTranslationAttribute()
    {
        return $this->translate(app()->getLocale());
    }

    // Relations invariantes
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'place_category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'place_tag');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
```

**app/Models/PlaceTranslation.php** :
```php
class PlaceTranslation extends Model
{
    protected $fillable = [
        'place_id', 'locale', 'title', 'slug', 'description',
        'practical_info', 'status', 'source_hash'
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($translation) {
            if (empty($translation->slug)) {
                $translation->slug = Str::slug($translation->title);
            }
        });
    }
}
```

### 4.6 Modification tables demandes

**Migration place_requests** (ajout champ détection langue) :
```php
Schema::table('place_requests', function (Blueprint $table) {
    $table->string('detected_language', 5)->default('unknown')->after('contact_email');
});
```

**Migration edit_requests** (ajout champ détection langue) :
```php
Schema::table('edit_requests', function (Blueprint $table) {
    $table->string('detected_language', 5)->default('unknown')->after('contact_email');
});
```

---

## 5. Routes localisées

### 5.1 Helper LocaleUrl

**app/Support/LocaleUrl.php** :
```php
class LocaleUrl
{
    public static function segment(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return config("locales.segments.$locale.$key", $key);
    }

    public static function routeName(string $base, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return "$base.$locale";
    }

    public static function route(string $base, array $params = [], ?string $locale = null): string
    {
        return route(self::routeName($base, $locale), $params);
    }

    public static function switchRoute(string $currentRoute, array $params, string $targetLocale): string
    {
        // Extraire le nom de base de la route (sans la locale)
        $baseName = preg_replace('/\.(fr|en)$/', '', $currentRoute);
        return self::route($baseName, $params, $targetLocale);
    }
}
```

### 5.2 Déclaration des routes

**routes/web.php** :
```php
$locales = config('locales.supported');

foreach ($locales as $locale) {
    Route::prefix($locale)
        ->middleware("setLocale:$locale")
        ->group(function () use ($locale) {

            // Homepage
            Route::get('/', function () {
                return view('web.home.homepage');
            })->name("home.$locale");

            // Pages statiques
            Route::get(LocaleUrl::segment('about', $locale), function () {
                return view('web.pages.about');
            })->name("about.$locale");

            Route::get(LocaleUrl::segment('features', $locale), function () {
                return view('web.pages.features');
            })->name("features.$locale");

            Route::get(LocaleUrl::segment('contact', $locale), function () {
                return view('web.pages.contact');
            })->name("contact.$locale");

            // Explorer
            Route::get(LocaleUrl::segment('explore', $locale), [ExploreController::class, 'index'])
                ->name("explore.$locale");

            // Lieux
            Route::get(LocaleUrl::segment('places', $locale), [PlaceController::class, 'index'])
                ->name("places.index.$locale");

            Route::get(LocaleUrl::segment('places', $locale) . '/{slug}', [PlaceController::class, 'show'])
                ->name("places.show.$locale");

            // Proposer un lieu
            Route::get(LocaleUrl::segment('propose_place', $locale), [PlaceRequestController::class, 'create'])
                ->name("place_requests.create.$locale");

            Route::post(LocaleUrl::segment('propose_place', $locale), [PlaceRequestController::class, 'store'])
                ->name("place_requests.store.$locale");

            // Tags
            Route::get('tags/{slug}', [TagController::class, 'show'])
                ->name("tags.show.$locale");
        });
}

// Routes communes (changement de langue, admin, etc.)
Route::post('/lang/switch', [LangController::class, 'switch'])->name('lang.switch');

// Redirection racine vers langue par défaut
Route::get('/', function () {
    return redirect()->to('/' . config('locales.default') . '/');
});
```

---

## 6. Middleware et gestion de la locale

### 6.1 Middleware SetLocale

**app/Http/Middleware/SetLocale.php** :
```php
class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $forcedLocale = null): Response
    {
        $supported = config('locales.supported');
        $cookieName = config('locales.cookie_name');
        $default = config('locales.default');

        // Déterminer la locale
        $locale = $forcedLocale
            ?? $request->route('locale')
            ?? $request->cookie($cookieName)
            ?? $this->detectBrowserLocale($request)
            ?? $default;

        // Vérifier que la locale est supportée
        if (!in_array($locale, $supported, true)) {
            $locale = $default;
        }

        // Appliquer la locale
        app()->setLocale($locale);
        $request->attributes->set('current_locale', $locale);

        // Sauvegarder dans cookie si différent
        $response = $next($request);

        if ($request->cookie($cookieName) !== $locale) {
            $response->withCookie(cookie(
                $cookieName,
                $locale,
                config('locales.cookie_lifetime')
            ));
        }

        return $response;
    }

    private function detectBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        $supported = config('locales.supported');
        $preferred = [];

        // Parser Accept-Language header
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', $lang);
            $locale = trim($parts[0]);
            $quality = 1.0;

            if (isset($parts[1]) && strpos($parts[1], 'q=') === 0) {
                $quality = floatval(substr($parts[1], 2));
            }

            // Extraire code langue principal (fr-FR -> fr)
            $mainLocale = substr($locale, 0, 2);
            if (in_array($mainLocale, $supported)) {
                $preferred[$mainLocale] = $quality;
            }
        }

        if (empty($preferred)) {
            return null;
        }

        // Retourner la langue avec la plus haute priorité
        arsort($preferred);
        return array_key_first($preferred);
    }
}
```

### 6.2 Enregistrement du middleware

**bootstrap/app.php** :
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'setLocale' => \App\Http\Middleware\SetLocale::class,
    ]);
})
```

---

## 7. Helpers d'URLs localisées

### 7.1 Helper global

**app/helpers.php** :
```php
if (!function_exists('localized_route')) {
    function localized_route(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return route("$name.$locale", $parameters);
    }
}

if (!function_exists('current_route_localized')) {
    function current_route_localized(string $targetLocale): string
    {
        $currentRoute = request()->route()->getName();
        $params = request()->route()->parameters();

        return LocaleUrl::switchRoute($currentRoute, $params, $targetLocale);
    }
}
```

### 7.2 Usage dans les vues

```blade
{{-- Lien vers liste des lieux dans la langue courante --}}
<a href="{{ localized_route('places.index') }}">{{ __('common.places') }}</a>

{{-- Lien vers fiche lieu avec slug traduit --}}
<a href="{{ localized_route('places.show', ['slug' => $place->translation->slug]) }}">
    {{ $place->translation->title }}
</a>

{{-- Lien vers même page en anglais --}}
<a href="{{ current_route_localized('en') }}">English</a>
```

---

## 8. Contrôleurs multilingues

### 8.1 PlaceController

**app/Http/Controllers/PlaceController.php** :
```php
class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $locale = $request->attributes->get('current_locale');

        $places = PlaceTranslation::with(['place.photos', 'place.categories.translations', 'place.tags.translations'])
            ->where('locale', $locale)
            ->where('status', 'published')
            ->orderBy('title')
            ->paginate(20);

        return view('places.index', [
            'places' => $places,
            'locale' => $locale,
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $locale = $request->attributes->get('current_locale');

        $translation = PlaceTranslation::with([
            'place.photos',
            'place.categories.translations' => fn($q) => $q->where('locale', $locale),
            'place.tags.translations' => fn($q) => $q->where('locale', $locale),
            'place.admin'
        ])
        ->where('locale', $locale)
        ->where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();

        // Générer alternates pour hreflang
        $alternates = $this->getAlternates($translation->place, $locale);

        return view('places.show', [
            'translation' => $translation,
            'place' => $translation->place,
            'locale' => $locale,
            'alternates' => $alternates,
        ]);
    }

    private function getAlternates(Place $place, string $currentLocale): array
    {
        $alternates = [];
        $supportedLocales = config('locales.supported');

        foreach ($supportedLocales as $locale) {
            $translation = $place->translations()
                ->where('locale', $locale)
                ->where('status', 'published')
                ->first();

            if ($translation) {
                $alternates[$locale] = localized_route(
                    'places.show',
                    ['slug' => $translation->slug],
                    $locale
                );
            }
        }

        return $alternates;
    }
}
```

---

## 9. Workflow visiteur : détection de langue

### 9.1 Service de détection

**app/Services/LanguageDetectionService.php** :
```php
class LanguageDetectionService
{
    private $supportedLocales;

    public function __construct()
    {
        $this->supportedLocales = config('locales.supported');
    }

    public function detectFromTexts(array $texts): string
    {
        // Combiner tous les textes avec espaces
        $combinedText = implode(' ', array_filter($texts));

        // Minimum 20 caractères pour détecter
        if (strlen(trim($combinedText)) < 20) {
            return 'unknown';
        }

        try {
            // Option A : Google Translate Detection API
            $detectedLanguage = $this->detectWithGoogle($combinedText);

            // Vérifier si la langue détectée est supportée
            if (in_array($detectedLanguage, $this->supportedLocales)) {
                return $detectedLanguage;
            }

        } catch (Exception $e) {
            Log::warning('Language detection failed', [
                'error' => $e->getMessage(),
                'text_length' => strlen($combinedText)
            ]);
        }

        return 'unknown';
    }

    private function detectWithGoogle(string $text): string
    {
        // Implementation avec Google Translate Detection API
        $translate = new Google\Cloud\Translate\V2\TranslateClient([
            'key' => config('services.google.translate_key')
        ]);

        $result = $translate->detectLanguage($text);
        return $result['languageCode'];
    }

    // Alternative : détection offline
    private function detectWithLibrary(string $text): string
    {
        $detector = new LanguageDetector\LanguageDetector();
        $language = $detector->evaluate($text)->getLanguage();

        // Mapper vers nos codes
        $mapping = [
            'french' => 'fr',
            'english' => 'en',
        ];

        return $mapping[$language] ?? 'unknown';
    }
}
```

### 9.2 Service PlaceRequest avec détection

**app/Services/PlaceRequestService.php** :
```php
class PlaceRequestService
{
    public function __construct(
        private LanguageDetectionService $languageDetection
    ) {}

    public function create(array $data): PlaceRequest
    {
        // Extraire textes pour détection
        $textsToAnalyze = [
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['practical_info'] ?? '',
        ];

        // Détecter la langue
        $detectedLanguage = $this->languageDetection->detectFromTexts($textsToAnalyze);

        // Créer la demande
        $placeRequest = PlaceRequest::create([
            'contact_email' => $data['contact_email'],
            'title' => $data['title'],
            'description' => $data['description'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'address' => $data['address'],
            'practical_info' => $data['practical_info'] ?? null,
            'detected_language' => $detectedLanguage,
            'status' => 'submitted',
        ]);

        // Log pour monitoring
        Log::info('PlaceRequest created with language detection', [
            'id' => $placeRequest->id,
            'detected_language' => $detectedLanguage,
            'contact_email' => $data['contact_email'],
        ]);

        return $placeRequest;
    }
}
```

---

## 10. Interface admin : onglets de traduction

### 10.1 Structure du formulaire admin

**Exemple formulaire Place (resources/views/admin/places/form.blade.php)** :
```blade
<form method="POST" action="{{ $place->exists ? route('admin.places.update', $place) : route('admin.places.store') }}">
    @csrf
    @if($place->exists) @method('PUT') @endif

    {{-- Champs invariants (hors onglets) --}}
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
            <label for="latitude">Latitude</label>
            <input type="number" step="0.0000001" id="latitude" name="latitude"
                   value="{{ old('latitude', $place->latitude) }}" required>
        </div>

        <div>
            <label for="longitude">Longitude</label>
            <input type="number" step="0.0000001" id="longitude" name="longitude"
                   value="{{ old('longitude', $place->longitude) }}" required>
        </div>

        <div>
            <label for="address">Adresse</label>
            <input type="text" id="address" name="address"
                   value="{{ old('address', $place->address) }}">
        </div>

        <div>
            <label for="is_featured">À l'affiche</label>
            <input type="checkbox" id="is_featured" name="is_featured"
                   {{ old('is_featured', $place->is_featured) ? 'checked' : '' }}>
        </div>
    </div>

    {{-- Onglets de traduction --}}
    <div class="border border-gray-200 rounded-lg">
        {{-- Barre d'onglets --}}
        <div class="border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <nav class="flex space-x-8 px-6">
                @foreach(config('locales.supported') as $locale)
                    <button type="button"
                            class="tab-button py-4 px-1 border-b-2 font-medium text-sm"
                            data-tab="{{ $locale }}"
                            @if($loop->first) data-active="true" @endif>
                        {{ strtoupper($locale) }}
                        @if($errors->has("translations.$locale.*"))
                            <span class="text-red-500">*</span>
                        @endif
                    </button>
                @endforeach

                {{-- Bouton traduction automatique --}}
                <div class="ml-auto py-4">
                    <button type="button" id="auto-translate"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                        Traduire automatiquement
                    </button>
                </div>
            </nav>
        </div>

        {{-- Contenu des onglets --}}
        @foreach(config('locales.supported') as $locale)
            @php
                $translation = $place->translations->firstWhere('locale', $locale);
                $isPreFilledFromRequest = isset($placeRequest) && $placeRequest->detected_language === $locale;
            @endphp

            <div class="tab-content p-6 space-y-6" data-tab="{{ $locale }}"
                 @if(!$loop->first) style="display: none;" @endif>

                @if($isPreFilledFromRequest)
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                        <p class="text-blue-800 text-sm">
                            <strong>Langue détectée :</strong> {{ strtoupper($locale) }}
                            - Ces champs ont été pré-remplis depuis la demande du visiteur.
                        </p>
                    </div>
                @endif

                <div>
                    <label for="translations_{{ $locale }}_title">Titre</label>
                    <input type="text"
                           id="translations_{{ $locale }}_title"
                           name="translations[{{ $locale }}][title]"
                           value="{{ old("translations.$locale.title",
                               $isPreFilledFromRequest ? $placeRequest->title : $translation?->title
                           ) }}"
                           @if($locale === 'fr') required @endif>
                    @error("translations.$locale.title")
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="translations_{{ $locale }}_slug">Slug</label>
                    <input type="text"
                           id="translations_{{ $locale }}_slug"
                           name="translations[{{ $locale }}][slug]"
                           value="{{ old("translations.$locale.slug", $translation?->slug) }}">
                    <p class="text-gray-500 text-sm">Laissez vide pour générer automatiquement</p>
                </div>

                <div>
                    <label for="translations_{{ $locale }}_description">Description</label>
                    <textarea id="translations_{{ $locale }}_description"
                              name="translations[{{ $locale }}][description]"
                              rows="6"
                              @if($locale === 'fr') required @endif>{{ old("translations.$locale.description",
                                  $isPreFilledFromRequest ? $placeRequest->description : $translation?->description
                              ) }}</textarea>
                </div>

                <div>
                    <label for="translations_{{ $locale }}_practical_info">Informations pratiques</label>
                    <textarea id="translations_{{ $locale }}_practical_info"
                              name="translations[{{ $locale }}][practical_info]"
                              rows="4">{{ old("translations.$locale.practical_info",
                                  $isPreFilledFromRequest ? $placeRequest->practical_info : $translation?->practical_info
                              ) }}</textarea>
                </div>

                <div>
                    <label for="translations_{{ $locale }}_status">Statut</label>
                    <select id="translations_{{ $locale }}_status"
                            name="translations[{{ $locale }}][status]">
                        <option value="draft"
                            {{ old("translations.$locale.status", $translation?->status) === 'draft' ? 'selected' : '' }}>
                            Brouillon
                        </option>
                        <option value="published"
                            {{ old("translations.$locale.status", $translation?->status) === 'published' ? 'selected' : '' }}>
                            Publié
                        </option>
                    </select>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex justify-end space-x-4 mt-8">
        <a href="{{ route('admin.places.index') }}"
           class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md">
            Annuler
        </a>
        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
            {{ $place->exists ? 'Mettre à jour' : 'Créer' }}
        </button>
    </div>
</form>
```

### 10.2 JavaScript pour onglets

**resources/js/admin/translation-tabs.js** :
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Désactiver tous les onglets
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
                btn.removeAttribute('data-active');
            });

            tabContents.forEach(content => {
                content.style.display = 'none';
            });

            // Activer l'onglet sélectionné
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            this.setAttribute('data-active', 'true');

            document.querySelector(`[data-tab="${targetTab}"].tab-content`).style.display = 'block';
        });
    });

    // Auto-génération des slugs
    document.querySelectorAll('[id$="_title"]').forEach(input => {
        const locale = input.id.split('_')[1];
        const slugInput = document.getElementById(`translations_${locale}_slug`);

        input.addEventListener('input', function() {
            if (!slugInput.value || slugInput.value === slugify(slugInput.getAttribute('data-original'))) {
                slugInput.value = slugify(this.value);
                slugInput.setAttribute('data-original', this.value);
            }
        });
    });

    // Traduction automatique
    document.getElementById('auto-translate')?.addEventListener('click', async function() {
        if (!confirm('Voulez-vous traduire automatiquement tous les champs vides depuis le français ?')) {
            return;
        }

        this.disabled = true;
        this.textContent = 'Traduction en cours...';

        try {
            await autoTranslateFields();
            alert('Traduction automatique terminée. Vérifiez et corrigez si nécessaire.');
        } catch (error) {
            alert('Erreur lors de la traduction automatique : ' + error.message);
        } finally {
            this.disabled = false;
            this.textContent = 'Traduire automatiquement';
        }
    });
});

function slugify(text) {
    return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

async function autoTranslateFields() {
    const frTitle = document.getElementById('translations_fr_title').value;
    const frDescription = document.getElementById('translations_fr_description').value;
    const frPracticalInfo = document.getElementById('translations_fr_practical_info').value;

    if (!frTitle && !frDescription) {
        throw new Error('Aucun contenu français à traduire');
    }

    const response = await fetch('/admin/translate/auto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            texts: {
                title: frTitle,
                description: frDescription,
                practical_info: frPracticalInfo
            },
            target_locale: 'en'
        })
    });

    if (!response.ok) {
        throw new Error('Erreur serveur');
    }

    const translations = await response.json();

    // Remplir les champs anglais
    if (translations.title) {
        document.getElementById('translations_en_title').value = translations.title;
        document.getElementById('translations_en_slug').value = slugify(translations.title);
    }
    if (translations.description) {
        document.getElementById('translations_en_description').value = translations.description;
    }
    if (translations.practical_info) {
        document.getElementById('translations_en_practical_info').value = translations.practical_info;
    }
}
```

---

## 11. Traduction automatique

### 11.1 Service de traduction

**app/Services/AutoTranslationService.php** :
```php
class AutoTranslationService
{
    public function __construct(
        private $translator = null
    ) {
        // Initialiser le service selon configuration
        $this->translator = $this->initializeTranslator();
    }

    public function translateTexts(array $texts, string $sourceLocale, string $targetLocale): array
    {
        $translations = [];

        foreach ($texts as $key => $text) {
            if (empty($text)) {
                $translations[$key] = '';
                continue;
            }

            try {
                $translations[$key] = $this->translateText($text, $sourceLocale, $targetLocale);
            } catch (Exception $e) {
                Log::error('Translation failed', [
                    'key' => $key,
                    'source' => $sourceLocale,
                    'target' => $targetLocale,
                    'error' => $e->getMessage()
                ]);
                $translations[$key] = $text; // Fallback vers texte original
            }
        }

        return $translations;
    }

    private function translateText(string $text, string $sourceLocale, string $targetLocale): string
    {
        // Google Translate
        if (config('services.google.translate_key')) {
            return $this->translateWithGoogle($text, $sourceLocale, $targetLocale);
        }

        // DeepL
        if (config('services.deepl.api_key')) {
            return $this->translateWithDeepL($text, $sourceLocale, $targetLocale);
        }

        throw new Exception('Aucun service de traduction configuré');
    }

    private function translateWithGoogle(string $text, string $source, string $target): string
    {
        $translate = new Google\Cloud\Translate\V2\TranslateClient([
            'key' => config('services.google.translate_key')
        ]);

        $result = $translate->translate($text, [
            'source' => $source,
            'target' => $target,
        ]);

        return $result['text'];
    }

    private function translateWithDeepL(string $text, string $source, string $target): string
    {
        $deepL = new DeepL\Translator(config('services.deepl.api_key'));

        $result = $deepL->translateText(
            $text,
            $this->mapToDeepLCode($source),
            $this->mapToDeepLCode($target)
        );

        return $result->text;
    }

    private function mapToDeepLCode(string $locale): string
    {
        return match($locale) {
            'fr' => 'FR',
            'en' => 'EN-US',
            default => strtoupper($locale)
        };
    }

    private function initializeTranslator()
    {
        // Configuration basée sur les services disponibles
        if (config('services.google.translate_key')) {
            return 'google';
        }
        if (config('services.deepl.api_key')) {
            return 'deepl';
        }
        return null;
    }

    public function calculateSourceHash(array $texts, string $sourceLocale): string
    {
        $combined = implode('|', $texts) . '|' . $sourceLocale;
        return sha1($combined);
    }
}
```

### 11.2 Contrôleur API traduction

**app/Http/Controllers/Admin/TranslationController.php** :
```php
class TranslationController extends Controller
{
    public function __construct(
        private AutoTranslationService $translationService
    ) {
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    public function autoTranslate(Request $request): JsonResponse
    {
        $request->validate([
            'texts' => 'required|array',
            'texts.title' => 'nullable|string',
            'texts.description' => 'nullable|string',
            'texts.practical_info' => 'nullable|string',
            'target_locale' => 'required|string|in:' . implode(',', config('locales.supported')),
            'source_locale' => 'string|in:' . implode(',', config('locales.supported')),
        ]);

        $sourceLocale = $request->input('source_locale', 'fr');
        $targetLocale = $request->input('target_locale');
        $texts = array_filter($request->input('texts')); // Supprimer les valeurs vides

        if (empty($texts)) {
            return response()->json(['error' => 'Aucun texte à traduire'], 400);
        }

        try {
            $translations = $this->translationService->translateTexts(
                $texts,
                $sourceLocale,
                $targetLocale
            );

            Log::info('Auto-translation completed', [
                'user_id' => auth()->id(),
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
                'text_count' => count($texts),
            ]);

            return response()->json($translations);

        } catch (Exception $e) {
            Log::error('Auto-translation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'source_locale' => $sourceLocale,
                'target_locale' => $targetLocale,
            ]);

            return response()->json([
                'error' => 'Erreur lors de la traduction automatique'
            ], 500);
        }
    }
}
```

### 11.3 Configuration services

**config/services.php** :
```php
'google' => [
    'translate_key' => env('GOOGLE_TRANSLATE_API_KEY'),
],

'deepl' => [
    'api_key' => env('DEEPL_API_KEY'),
],
```

**\.env** :
```env
# Services de traduction (optionnels)
GOOGLE_TRANSLATE_API_KEY=
DEEPL_API_KEY=
```

---

## 12. Sélecteur de langue

### 12.1 Contrôleur changement de langue

**app/Http/Controllers/LangController.php** :
```php
class LangController extends Controller
{
    public function switch(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => 'required|string|in:' . implode(',', config('locales.supported')),
            'route_name' => 'nullable|string',
            'route_params' => 'nullable|array',
        ]);

        $targetLocale = $request->input('locale');
        $routeName = $request->input('route_name');
        $routeParams = $request->input('route_params', []);

        // Sauvegarder la préférence
        $response = redirect()->back();
        $response->withCookie(cookie(
            config('locales.cookie_name'),
            $targetLocale,
            config('locales.cookie_lifetime')
        ));

        // Tentative de redirection vers page équivalente
        if ($routeName) {
            try {
                $url = $this->findEquivalentRoute($routeName, $routeParams, $targetLocale);
                if ($url) {
                    return redirect()->to($url)->withCookie(
                        cookie(
                            config('locales.cookie_name'),
                            $targetLocale,
                            config('locales.cookie_lifetime')
                        )
                    );
                }
            } catch (Exception $e) {
                Log::warning('Failed to find equivalent route', [
                    'route_name' => $routeName,
                    'target_locale' => $targetLocale,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback vers homepage
        return redirect()->to(localized_route('home', [], $targetLocale))
            ->withCookie(cookie(
                config('locales.cookie_name'),
                $targetLocale,
                config('locales.cookie_lifetime')
            ));
    }

    private function findEquivalentRoute(string $routeName, array $params, string $targetLocale): ?string
    {
        // Extraire le nom de base (sans locale)
        $baseName = preg_replace('/\.(fr|en)$/', '', $routeName);
        $targetRouteName = "$baseName.$targetLocale";

        // Vérifier que la route existe
        if (!Route::has($targetRouteName)) {
            return null;
        }

        // Cas spécial : routes avec slug traduit
        if (str_contains($baseName, 'show') && isset($params['slug'])) {
            return $this->findTranslatedSlugRoute($baseName, $params, $targetLocale);
        }

        // Route standard
        try {
            return route($targetRouteName, $params);
        } catch (Exception $e) {
            return null;
        }
    }

    private function findTranslatedSlugRoute(string $baseName, array $params, string $targetLocale): ?string
    {
        $slug = $params['slug'] ?? null;
        if (!$slug) {
            return null;
        }

        // Places
        if (str_contains($baseName, 'places.show')) {
            $currentTranslation = PlaceTranslation::where('slug', $slug)->first();
            if (!$currentTranslation) {
                return null;
            }

            $targetTranslation = $currentTranslation->place
                ->translations()
                ->where('locale', $targetLocale)
                ->where('status', 'published')
                ->first();

            if ($targetTranslation) {
                return localized_route('places.show', ['slug' => $targetTranslation->slug], $targetLocale);
            }
        }

        // Tags
        if (str_contains($baseName, 'tags.show')) {
            $currentTranslation = TagTranslation::where('slug', $slug)->first();
            if (!$currentTranslation) {
                return null;
            }

            $targetTranslation = $currentTranslation->tag
                ->translations()
                ->where('locale', $targetLocale)
                ->where('status', 'published')
                ->first();

            if ($targetTranslation) {
                return localized_route('tags.show', ['slug' => $targetTranslation->slug], $targetLocale);
            }
        }

        return null;
    }
}
```

### 12.2 Composant sélecteur

**resources/views/components/web/language-switcher.blade.php** :
```blade
@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('locales.supported');
    $currentRoute = request()->route()?->getName();
    $routeParams = request()->route()?->parameters() ?? [];
@endphp

<div class="relative inline-block text-left" x-data="{ open: false }">
    {{-- Bouton actuel --}}
    <button @click="open = !open"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        {{ strtoupper($currentLocale) }}
        <x-heroicon-s-chevron-down class="ml-2 h-4 w-4" />
    </button>

    {{-- Menu déroulant --}}
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="origin-top-right absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">

        <div class="py-1">
            @foreach($supportedLocales as $locale)
                @if($locale !== $currentLocale)
                    <form method="POST" action="{{ route('lang.switch') }}" class="block">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $locale }}">
                        <input type="hidden" name="route_name" value="{{ $currentRoute }}">
                        @foreach($routeParams as $key => $value)
                            <input type="hidden" name="route_params[{{ $key }}]" value="{{ $value }}">
                        @endforeach

                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center">
                            {{ strtoupper($locale) }}
                            <span class="ml-2 text-gray-500">
                                @if($locale === 'fr')
                                    Français
                                @elseif($locale === 'en')
                                    English
                                @endif
                            </span>
                        </button>
                    </form>
                @endif
            @endforeach
        </div>
    </div>
</div>
```

---

## 13. SEO multilingue

### 13.1 Layout avec hreflang

**resources/views/layouts/web.blade.php** :
```blade
@php
    $currentLocale = app()->getLocale();
    $alternates = $alternates ?? [];
@endphp

<!DOCTYPE html>
<html lang="{{ $currentLocale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'COSMAP - ' . __('common.discover_space_places'))</title>
    <meta name="description" content="@yield('meta_description', __('common.site_description'))">

    {{-- hreflang pour toutes les versions disponibles --}}
    @if(!empty($alternates))
        @foreach($alternates as $locale => $url)
            <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
        @endforeach

        {{-- x-default vers la langue par défaut --}}
        @if(isset($alternates[config('locales.default')]))
            <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('locales.default')] }}">
        @endif
    @endif

    {{-- Canonical vers soi-même --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph localisé --}}
    <meta property="og:locale" content="{{ $currentLocale === 'fr' ? 'fr_FR' : 'en_US' }}">
    @if(!empty($alternates))
        @foreach($alternates as $locale => $url)
            @if($locale !== $currentLocale)
                <meta property="og:locale:alternate" content="{{ $locale === 'fr' ? 'fr_FR' : 'en_US' }}">
            @endif
        @endforeach
    @endif

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="antialiased bg-white text-gray-900 font-inter">
    <!-- Navbar -->
    <x-web.navbar />

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <x-web.footer />

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
```

### 13.2 JSON-LD multilingue

**resources/views/places/show.blade.php** (extrait) :
```blade
@extends('layouts.web')

@section('title', $translation->title . ' - COSMAP')
@section('meta_description', Str::limit(strip_tags($translation->description), 160))

@push('head')
{{-- JSON-LD avec langue --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Place",
    "name": @json($translation->title),
    "description": @json(Str::limit(strip_tags($translation->description), 200)),
    "inLanguage": @json($locale),
    "url": @json(url()->current()),
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": @json($place->latitude),
        "longitude": @json($place->longitude)
    },
    "address": {
        "@type": "PostalAddress",
        "streetAddress": @json($place->address)
    }
    @if($place->photos->isNotEmpty())
    ,
    "image": @json($place->photos->first()->url)
    @endif
}
</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Fil d'ariane multilingue --}}
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="{{ localized_route('home') }}" class="text-blue-600 hover:text-blue-800">{{ __('common.home') }}</a></li>
            <li class="text-gray-500">/</li>
            <li><a href="{{ localized_route('places.index') }}" class="text-blue-600 hover:text-blue-800">{{ __('common.places') }}</a></li>
            <li class="text-gray-500">/</li>
            <li class="text-gray-900 font-medium">{{ $translation->title }}</li>
        </ol>
    </nav>

    {{-- Contenu du lieu --}}
    <article>
        <header class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $translation->title }}
            </h1>

            @if($translation->description)
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! nl2br(e($translation->description)) !!}
                </div>
            @endif
        </header>

        {{-- Reste du contenu... --}}
    </article>
</div>
@endsection
```

---

## 14. Sitemap multilingue

### 14.1 Contrôleur sitemap

**app/Http/Controllers/SitemapController.php** :
```php
class SitemapController extends Controller
{
    public function index(): Response
    {
        $locales = config('locales.supported');

        // Récupérer tous les contenus publiés
        $places = Place::with(['translations' => fn($q) => $q->where('status', 'published')])
            ->whereHas('translations', fn($q) => $q->where('status', 'published'))
            ->get();

        $tags = Tag::with(['translations' => fn($q) => $q->where('status', 'published')])
            ->whereHas('translations', fn($q) => $q->where('status', 'published'))
            ->get();

        $xml = view('sitemap.index', [
            'locales' => $locales,
            'places' => $places,
            'tags' => $tags,
        ])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
```

### 14.2 Vue sitemap

**resources/views/sitemap/index.blade.php** :
```xml
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- Pages statiques --}}
    @foreach($locales as $locale)
        {{-- Homepage --}}
        <url>
            <loc>{{ localized_route('home', [], $locale) }}</loc>
            <lastmod>{{ now()->toISOString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.0</priority>
            @foreach($locales as $altLocale)
                <xhtml:link rel="alternate" hreflang="{{ $altLocale }}"
                           href="{{ localized_route('home', [], $altLocale) }}" />
            @endforeach
        </url>

        {{-- Page explorer --}}
        <url>
            <loc>{{ localized_route('explore', [], $locale) }}</loc>
            <lastmod>{{ now()->toISOString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
            @foreach($locales as $altLocale)
                <xhtml:link rel="alternate" hreflang="{{ $altLocale }}"
                           href="{{ localized_route('explore', [], $altLocale) }}" />
            @endforeach
        </url>

        {{-- Liste des lieux --}}
        <url>
            <loc>{{ localized_route('places.index', [], $locale) }}</loc>
            <lastmod>{{ now()->toISOString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
            @foreach($locales as $altLocale)
                <xhtml:link rel="alternate" hreflang="{{ $altLocale }}"
                           href="{{ localized_route('places.index', [], $altLocale) }}" />
            @endforeach
        </url>

        {{-- Autres pages statiques --}}
        @foreach(['about', 'features', 'contact'] as $page)
            <url>
                <loc>{{ localized_route($page, [], $locale) }}</loc>
                <lastmod>{{ now()->toISOString() }}</lastmod>
                <changefreq>monthly</changefreq>
                <priority>0.5</priority>
                @foreach($locales as $altLocale)
                    <xhtml:link rel="alternate" hreflang="{{ $altLocale }}"
                               href="{{ localized_route($page, [], $altLocale) }}" />
                @endforeach
            </url>
        @endforeach
    @endforeach

    {{-- Lieux --}}
    @foreach($places as $place)
        @php $translationsByLocale = $place->translations->keyBy('locale'); @endphp
        @foreach($locales as $locale)
            @if($translationsByLocale->has($locale))
                @php
                    $translation = $translationsByLocale[$locale];
                    $url = localized_route('places.show', ['slug' => $translation->slug], $locale);
                @endphp
                <url>
                    <loc>{{ $url }}</loc>
                    <lastmod>{{ $translation->updated_at->toISOString() }}</lastmod>
                    <changefreq>weekly</changefreq>
                    <priority>0.7</priority>

                    {{-- Alternates pour toutes les traductions disponibles --}}
                    @foreach($locales as $altLocale)
                        @if($translationsByLocale->has($altLocale))
                            @php $altTranslation = $translationsByLocale[$altLocale]; @endphp
                            <xhtml:link rel="alternate" hreflang="{{ $altLocale }}"
                                       href="{{ localized_route('places.show', ['slug' => $altTranslation->slug], $altLocale) }}" />
                        @endif
                    @endforeach
                </url>
            @endif
        @endforeach
    @endforeach

    {{-- Tags --}}
    @foreach($tags as $tag)
        @php $translationsByLocale = $tag->translations->keyBy('locale'); @endphp
        @foreach($locales as $locale)
            @if($translationsByLocale->has($locale))
                @php
                    $translation = $translationsByLocale[$locale];
                    $url = localized_route('tags.show', ['slug' => $translation->slug], $locale);
                @endphp
                <url>
                    <loc>{{ $url }}</loc>
                    <lastmod>{{ $translation->updated_at->toISOString() }}</lastmod>
                    <changefreq>weekly</changefreq>
                    <priority>0.6</priority>

                    @foreach($locales as $altLocale)
                        @if($translationsByLocale->has($altLocale))
                            @php $altTranslation = $translationsByLocale[$altLocale]; @endphp
                            <xhtml:link rel="alternate" hreflang="{{ $altLocale }}"
                                       href="{{ localized_route('tags.show', ['slug' => $altTranslation->slug], $altLocale) }}" />
                        @endif
                    @endforeach
                </url>
            @endif
        @endforeach
    @endforeach
</urlset>
```

### 14.3 Route sitemap

**routes/web.php** :
```php
// Sitemap (non localisé)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
```

---

## 15. Recherche et performance

### 15.1 Optimisations base de données

**Index supplémentaires** :
```php
// Dans les migrations de translation
$table->index(['locale', 'status', 'created_at']);
$table->index(['status', 'locale']);
```

**Scope pour traductions publiées** :
```php
// PlaceTranslation.php
public function scopePublished($query)
{
    return $query->where('status', 'published');
}

public function scopeForLocale($query, string $locale)
{
    return $query->where('locale', $locale);
}
```

### 15.2 Cache multilingue

**app/Services/CacheService.php** :
```php
class CacheService
{
    public function placeKey(string $slug, string $locale): string
    {
        return "place:{$locale}:{$slug}";
    }

    public function placesListKey(string $locale, array $filters = []): string
    {
        $filterHash = md5(serialize($filters));
        return "places_list:{$locale}:{$filterHash}";
    }

    public function remember(string $key, int $minutes, callable $callback)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }

    public function forgetPlace(Place $place): void
    {
        $locales = config('locales.supported');

        foreach ($locales as $locale) {
            $translation = $place->translate($locale);
            if ($translation) {
                Cache::forget($this->placeKey($translation->slug, $locale));
            }
        }

        // Oublier les listes
        foreach ($locales as $locale) {
            Cache::tags(["places_list_{$locale}"])->flush();
        }
    }
}
```

---

## 16. Tests et validation

### 16.1 Tests unitaires

**tests/Unit/LocaleUrlTest.php** :
```php
class LocaleUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_segment_returns_correct_translation()
    {
        $this->assertEquals('lieux', LocaleUrl::segment('places', 'fr'));
        $this->assertEquals('places', LocaleUrl::segment('places', 'en'));
    }

    public function test_route_generates_correct_name()
    {
        $this->assertEquals('places.show.fr', LocaleUrl::routeName('places.show', 'fr'));
        $this->assertEquals('places.show.en', LocaleUrl::routeName('places.show', 'en'));
    }
}
```

**tests/Unit/PlaceTranslationTest.php** :
```php
class PlaceTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_generates_automatically()
    {
        $translation = PlaceTranslation::factory()->create([
            'title' => 'Centre Spatial Kennedy',
            'slug' => null,
        ]);

        $this->assertEquals('centre-spatial-kennedy', $translation->slug);
    }

    public function test_unique_slug_per_locale()
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
        ]);

        $this->expectException(QueryException::class);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
        ]);
    }
}
```

### 16.2 Tests fonctionnels

**tests/Feature/MultilingualRoutesTest.php** :
```php
class MultilingualRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_accessible_in_all_locales()
    {
        foreach (config('locales.supported') as $locale) {
            $response = $this->get("/$locale/");
            $response->assertStatus(200);
            $response->assertSee('COSMAP');
        }
    }

    public function test_place_show_with_correct_slug()
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'title' => 'Centre Spatial Kennedy',
            'slug' => 'centre-spatial-kennedy',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'title' => 'Kennedy Space Center',
            'slug' => 'kennedy-space-center',
            'status' => 'published',
        ]);

        // Test FR
        $response = $this->get('/fr/lieux/centre-spatial-kennedy');
        $response->assertStatus(200);
        $response->assertSee('Centre Spatial Kennedy');

        // Test EN
        $response = $this->get('/en/places/kennedy-space-center');
        $response->assertStatus(200);
        $response->assertSee('Kennedy Space Center');
    }

    public function test_language_switch_preserves_context()
    {
        $place = Place::factory()->create();

        $frTranslation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
            'status' => 'published',
        ]);

        $enTranslation = PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'slug' => 'kennedy-space-center',
            'status' => 'published',
        ]);

        $response = $this->post('/lang/switch', [
            'locale' => 'en',
            'route_name' => 'places.show.fr',
            'route_params' => ['slug' => 'centre-spatial-kennedy'],
        ]);

        $response->assertRedirect('/en/places/kennedy-space-center');
    }
}
```

### 16.3 Tests SEO

**tests/Feature/SeoTest.php** :
```php
class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_hreflang_tags_present()
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'slug' => 'kennedy-space-center',
            'status' => 'published',
        ]);

        $response = $this->get('/fr/lieux/centre-spatial-kennedy');

        $response->assertSee('hreflang="fr"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_canonical_points_to_self()
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
            'status' => 'published',
        ]);

        $response = $this->get('/fr/lieux/centre-spatial-kennedy');

        $response->assertSee('<link rel="canonical" href="http://localhost/fr/lieux/centre-spatial-kennedy">', false);
    }

    public function test_sitemap_contains_all_locales()
    {
        $place = Place::factory()->create();

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'fr',
            'slug' => 'centre-spatial-kennedy',
            'status' => 'published',
        ]);

        PlaceTranslation::factory()->create([
            'place_id' => $place->id,
            'locale' => 'en',
            'slug' => 'kennedy-space-center',
            'status' => 'published',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('/fr/lieux/centre-spatial-kennedy');
        $response->assertSee('/en/places/kennedy-space-center');
    }
}
```

---

## 17. Plan d'implémentation

### 17.1 Phase 1 : Infrastructure de base

**Semaine 1-2** :
1. ✅ Configuration locale et middleware
2. ✅ Migrations tables de traduction
3. ✅ Modèles Place/PlaceTranslation
4. ✅ Routes localisées de base
5. ✅ Helpers LocaleUrl

**Livrables** :
- URLs `/fr/lieux` et `/en/places` fonctionnelles
- Middleware SetLocale actif
- Modèles avec relations

### 17.2 Phase 2 : Interface admin

**Semaine 3-4** :
1. ✅ Formulaires admin avec onglets traduction
2. ✅ Détection langue automatique
3. ✅ Service de traduction automatique
4. ✅ Interface de gestion Places

**Livrables** :
- Formulaire admin Place multilingue
- Bouton "Traduire automatiquement"
- Pré-remplissage selon langue détectée

### 17.3 Phase 3 : SEO et frontend

**Semaine 5-6** :
1. ✅ Sélecteur de langue frontend
2. ✅ Pages publiques multilingues
3. ✅ hreflang et canonical
4. ✅ Sitemap multilingue

**Livrables** :
- Pages publiques FR/EN complètes
- SEO conforme standards
- Sitemap avec alternates

### 17.4 Phase 4 : Optimisation et tests

**Semaine 7-8** :
1. ✅ Cache multilingue
2. ✅ Tests automatisés
3. ✅ Performance optimisée
4. ✅ Extension Categories/Tags

**Livrables** :
- Tests complets (95%+ couverture)
- Performance < 200ms
- Categories et Tags multilingues

### 17.5 Checklist de livraison

#### ✅ URLs et routing
- [ ] URLs distinctes par langue
- [ ] Segments traduits
- [ ] Slugs traduits
- [ ] Redirections contextuelles

#### ✅ Données et modèles
- [ ] Séparation invariant/traductions
- [ ] Contraintes unicité
- [ ] Relations Eloquent
- [ ] Détection langue automatique

#### ✅ Interface admin
- [ ] Onglets par langue
- [ ] Traduction automatique
- [ ] Pré-remplissage intelligent
- [ ] Validation formulaires

#### ✅ SEO
- [ ] hreflang complet
- [ ] Canonical correct
- [ ] JSON-LD multilingue
- [ ] Sitemap avec alternates

#### ✅ UX
- [ ] Sélecteur langue
- [ ] Conservation contexte
- [ ] Performance optimisée
- [ ] Tests fonctionnels

---

## Conclusion

Ce système multilingue pour COSMAP offre :

- **Architecture solide** : extensible, performante, respectueuse des standards
- **Workflow optimisé** : détection automatique, interface admin intuitive
- **SEO irréprochable** : hreflang, canonical, sitemap, JSON-LD
- **Expérience utilisateur** : navigation fluide, conservation du contexte
- **Maintenance facilitée** : tests automatisés, cache intelligent, monitoring

Le système est conçu pour évoluer facilement vers d'autres langues tout en maintenant la robustesse et les performances de l'application.