# Conventions de nommage & structure

## Langue & base
- Code (classes, méthodes, variables) : **anglais**.
- Base de données : **snake_case**.

## Emplacements
- Services : `app/Domain/{Entity}/Services/{Entity}Service.php`
- Repositories : `app/Domain/{Entity}/Repositories/{Entity}Repository.php` (+ interface)
- Livewire : `app/Livewire/app/{feature}/...` ; vues : `resources/views/livewire/app/{feature}/...`
- Blade pages : `resources/views/app/{feature}/.../index.blade.php` (+ partials)
- Blade components : `resources/views/components/app/{feature}/...`

## Méthodes types
- Repository : `findById(int $id): ?Model`, `paginate(array $filters, int $perPage)`
- Service : `getDetail(int $id): PlaceDTO`, `getList(FiltersDTO $filters)`
- Slugs : générés via `Str::slug(title)` ; modifiables ; prêts pour i18n.
