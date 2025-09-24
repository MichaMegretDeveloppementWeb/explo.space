# CLAUDE.md — COSMAP (À LIRE AVANT TOUTE MODIFICATION)

## 0) Contexte du projet
- **Nom** : COSMAP
- **Mission** : Annuaire mondial pour découvrir des lieux liés à la conquête spatiale et à l'exploration de l'univers.
- **Stack** : Laravel 12, Livewire 3, Blade, TypeScript/Vite, MySQL (snake_case), stockage local (S3 plus tard).
- **Langues** : V1 en français (textes via fichiers de langue pour préparer l’i18n). i18n & fuseaux horaires prévus plus tard.
- **Hébergement** : Hostinger mutualisé au départ (éviter dépendances système lourdes).
- **Sécurité & RGPD** : irréprochable (Policies, validation, XSS/CSRF, mots de passe), Google reCAPTCHA v3, journaux quotidiens, alertes mail sur erreurs critiques.

## 1) Cahier des charges
Lire le cahier des charges si ce n'est pas déjà fait pour bien comprendre le projet : 
- @docs/cahier_des_charges.md

## 2) Domaines & rôles
- **Entités V1** : `User`, `Place`, `Tag`, `Category`, `Photo`, `PlaceRequest` (proposition d'ajout), `EditRequest` (proposition de modification/signalement), `AuditLog`.
- **Rôles** :
    - *Visiteur* : proposer des lieux (`PlaceRequest`) et modifications/signalements (`EditRequest`) via formulaires avec email + reCAPTCHA v3.
    - *Admin* (`User` connecté) : créer/éditer lieux/tags/catégories/photos, valider/refuser `PlaceRequest`/`EditRequest`, assigner tags et catégories.
    - *Super-admin* (`User` connecté) : droits d'admin + gestion des admins et réglages.
- **Workflow demandes** :
    - `PlaceRequest` : **submitted → pending → accepted/refused** (+ `reason`). Acceptée ⇒ devient `Place`. Email de contact pour suivi/notification.
    - `EditRequest` : **submitted → pending → accepted/refused** (+ `reason`). Acceptée ⇒ applique la modification. Email de contact pour suivi/notification.
    - Visibilité : les demandes sont visibles par tous les admins. Traçabilité de qui l'a vue en premier et qui l'a traitée.
    - Traçabilité : `created_at`, `viewed_at` (première consultation admin), `viewed_by_admin_id`, `accepted_at`/`refused_at`, `processed_by_admin_id`, `contact_email`.

## 3) Recherche & carte
- Carte : **Leaflet + OpenStreetMap** (gratuit, RGPD-friendly). Style proche Google Maps via tuiles/thèmes.
- Géocodage/autocomplete : **Nominatim (OSM)** au départ (respect quotas), extensible vers Google Places plus tard.
- Modes :
    - **Autour de moi** : rayon max **1500 km** ; charger uniquement ce qui intersecte la **zone affichée** (bounding box).
    - **Monde entier** : toutes les références (filtrage par tags optionnel) avec **clustering** et **pagination** côté liste.
- Navigation : fiche **dédiée** (page “show”). Conserver filtres/zoom/position en **URL** pour un retour exact.

## 4) Place & médias
- **Champs min. Place** : **title**, **slug**, **description**, **coordinates (lat,lng)** (obligatoire), **address**, **practical_info**, **tags** (assignés par admin), **categories** (gestion interne admin), **photos**.
- **Tags** : visibles publiquement, utilisés pour filtrage et recherche thématique.
- **Catégories** : champ interne admin uniquement, non visible par les visiteurs, pour organisation/gestion interne.
- **Photos** : JPEG/PNG/WebP (pas SVG), **≤ 5 Mo/photo**, **≤ 10 photos/lieu**, 1 **photo principale**. Générer **miniatures** (thumb/medium/large). Visionneuse plein écran (lazy-load).
- **Workflow propositions** : les visiteurs ne peuvent pas choisir de tags lors de la proposition ; l'admin assigne tags et catégories lors de la validation.

## 5) Architecture & conventions (Laravel + Livewire)
- Respect **SOLID**. **Service + Repository (interfaces)** pour métier & persistence (Eloquent côté repository).
- **Blade pages** sous `resources/views/app/...` incluent :
    - **Blade components** (statiques) sous `resources/views/components/app/...`
    - **Livewire components** (interactifs) sous `app/Livewire/app/...` et leurs vues `resources/views/livewire/app/...`
- Code en **anglais**, DB en **snake_case**. Slugs : générés depuis le titre (modifiables), prêts pour l’i18n.

## 6) Sécurité, RGPD, erreurs & journaux
- **Policies** pour actions sensibles (création/validation/modération). CSRF/XSS protégés, validation stricte (Form Requests).
- **reCAPTCHA v3** sur **tous** les formulaires publics (proposition, modification, contact, etc.).
- **Erreurs** : messages clairs côté utilisateur (aucune info sensible). **Logs quotidiens**. **Alertes email** sur erreurs **critiques (500)** via `.env`.

## 7) Qualité de code (outils et usage)
- **Outils** : Laravel Pint (formatage), Larastan (PHPStan, niveau 6), PHPUnit (tests), Rector (profil **prudent**), GrumPHP (bloque les commits si QA échoue).
- **Scripts Composer** :
    - `composer qa` → Pint + PHPStan + **PHPUnit**
    - `composer fix` → Pint
    - `composer stan` → PHPStan
    - `composer test` → PHPUnit
- **Fréquence** : **à la demande**. Avant tout commit, `composer qa` doit être **au vert**.

## 8) Manière de travailler avec Claude Code
1. **Mode Plan par défaut** : propose un **plan détaillé** (fichiers + chemins complets + tests) **sans coder**.
2. **Bypass** autorisé pour tâches triviales (`/bypass-plan`).
3. **Implémentation par petites étapes** : après chaque étape, **suggère** de lancer `composer qa`, corrige jusqu’à **tout vert**.
4. **Sorties attendues** : diffs/patchs **avec chemins complets**, commandes proposées, explications brèves et précises.
5. **Interdits** : pas d’accès non nécessaire à des secrets ; ne pas désactiver des garde-fous ; ne pas ignorer un test au rouge.

## 9) Documents à lire avant de coder
- @docs/architecture.md
- @docs/solid.md
- @docs/request_optimisation.md
- @docs/tests.md
- @docs/naming_conventions.md
- @docs/errors_exceptions.md
- @docs/security.md
- @docs/git_conventions.md
- @docs/bdd.md
- @docs/design_system.md
- @docs/site_structure.md
- @docs/translate_system.md

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.0
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- larastan/larastan (LARASTAN) - v3
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- rector/rector (RECTOR) - v2
- tailwindcss (TAILWINDCSS) - v4


## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()`) for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit <name>` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff"
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
