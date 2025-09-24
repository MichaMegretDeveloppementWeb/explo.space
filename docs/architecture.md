# Architecture (Laravel 12 + Livewire 3)

## Couches et responsabilités
- **Blade pages** : structure la page, inclut des composants.
- **Blade components** : UI statique/réutilisable (pas de logique métier durable).
- **Livewire components** : interactions (carte, carrousel photo, recherche). Appellent des **Services** (pas de logique métier durable ici).
- **Controllers** (`app/Http/Controllers/{Entity}`) : contient les methode appellées par les routes. Fait appel aux services si besoin, renvoi une vue blade.
- **Services** (`app/Services/{Entity}`) : règles métier, orchestration, transactions ; appellent des **Repositories**.
- **Repositories** (`app/Repositories/{Entity}`) : persistence (Eloquent/Query Builder), requêtes optimisées, retours typés.

Les Controllers/Services/Repositories segmenteront au maximum les classes. Par exemple on aura pas seulement app/Services/Place/PlaceService mais app/Services/Place/PlaceListService+PlaceDetailService+PlaceUpdateService+PlaceCreateService etc... pareil pour les repositories. Pour les controller on aura un controller pour la liste, un pour le detail et un autre pour le Crud. Cette logique est applicable à toutes les entités de l'application

## Dépendances
- Blade/Livewire → **Services**
- Services → **Repositories (interfaces)**
- Repositories → **Modèles Eloquent**

## Organisation Livewire & Blade
- Pages Blade : `resources/views/app/{feature}/.../index.blade.php`
- Blade components : `resources/views/components/app/{feature}/...`
- Livewire : `app/Livewire/app/{feature}/...` + vues `resources/views/livewire/app/{feature}/...`

## Données & flux
- `User` : modèle pour les administrateurs uniquement (admin/super-admin). Pas de comptes publics.
- `PlaceRequest` : submitted → pending → accepted/refused (+ reason + contact_email). Visibilité : tous les admins. Traçabilité : viewed_by_admin_id, processed_by_admin_id.
- `EditRequest` : submitted → pending → accepted/refused (+ reason + contact_email). Visibilité : tous les admins. Traçabilité : viewed_by_admin_id, processed_by_admin_id.
- `Place` : entité principale avec tags (visibles publiquement) et categories (usage interne admin).
- `Tag` : thématiques visibles publiquement, utilisées pour filtrage.
- `Category` : catégories internes admin uniquement, non visibles par les visiteurs.
- `Photo` : stockage local, miniature pour chaque photo, 1 principale par `Place` pour affichage listes.

## Navigation & URL
- Page fiche **dédiée**. Conserver filtres/zoom/position en **URL** pour un retour exact.
- Slugs : `Str::slug(title)` ; modifiables ; pensés pour l’i18n futur.

## Carte & géocodage
- **Leaflet + OSM** (gratuit). **Clustering** pour zones denses.
- **Géocodage/autocomplete** : Nominatim (OSM) d’abord ; extensible Google Places plus tard.
