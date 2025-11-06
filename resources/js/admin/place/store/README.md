# Admin Place Form - JavaScript Modules

## Vue d'ensemble

Modules JavaScript pour le formulaire admin de création/édition de lieux.

## Modules créés

### 1. `slug-generator.js`
Génération automatique des slugs à partir des titres.

**Fonctionnalités** :
- Détecte les changements dans les champs titre pour chaque locale
- Génère automatiquement le slug si non édité manuellement
- Normalise les accents et caractères spéciaux
- Respecte les règles de slugification (lowercase, hyphens, no special chars)

**Exemple** :
```
"Centre Spatial Kennedy" → "centre-spatial-kennedy"
"Observatoire de l'Univers" → "observatoire-de-lunivers"
```

### 2. `location-map.js`
Carte interactive Leaflet pour sélectionner les coordonnées.

**Fonctionnalités** :
- Affichage carte mondiale avec tiles CartoDB (fallback OSM)
- Clic sur la carte pour définir les coordonnées
- Marqueur draggable pour ajuster la position
- Synchronisation bidirectionnelle avec les inputs lat/lng
- Écoute des événements d'autocomplete d'adresse
- Dispatch événement Livewire `map:coordinates-changed`

**Événements écoutés** :
- `address:selected` (window)
- `address-validated` (Livewire)
- Changements manuels sur inputs latitude/longitude

### 3. `photo-sortable.js`
Réorganisation des photos par drag & drop.

**Fonctionnalités** :
- SortableJS sur la grille de photos existantes
- Drag handle sur chaque photo (icône)
- Animation visuelle pendant le drag
- Dispatch événement `photos:reordered` avec orderMap
- Appel méthode Livewire `updatePhotoOrder()`

**Structure orderMap** :
```javascript
{
  photoId1: 0, // nouvelle position
  photoId2: 1,
  photoId3: 2,
  // ...
}
```

### 4. `init.js`
Point d'entrée principal qui initialise tous les modules.

**Fonctionnalités** :
- Initialisation automatique au chargement de la page
- Gestion des erreurs par module
- Re-initialisation après updates Livewire
- Exposition globale pour debug : `window.AdminPlaceFormManager`

## Installation des dépendances

Ajouter à `package.json` :

```json
{
  "dependencies": {
    "leaflet": "^1.9.4",
    "sortablejs": "^1.15.0"
  }
}
```

Installer :
```bash
npm install
```

## Intégration Vite

### Option 1 : Créer un point d'entrée dédié

Créer `resources/js/admin/place/store.js` :

```javascript
// Point d'entrée Vite pour le formulaire admin place
import './store/init.js';
```

Ajouter à `vite.config.js` :

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin/place/store.js', // Nouveau point d'entrée
            ],
            refresh: true,
        }),
    ],
});
```

### Option 2 : Import conditionnel dans app.js

Dans `resources/js/app.js`, ajouter :

```javascript
// Import conditionnel pour formulaire admin place
if (document.querySelector('[data-admin-place-form]')) {
    import('./admin/place/store/init.js');
}
```

Ajouter l'attribut dans la vue Blade :

```blade
<div data-admin-place-form>
    <livewire:admin.place.store.place-store-form ... />
</div>
```

## Intégration dans les vues Blade

### Dans `resources/views/layouts/admin.blade.php`

Ajouter dans le `<head>` :

```blade
{{-- Leaflet CSS (requis pour les cartes) --}}
@stack('styles')
```

Ajouter avant `</body>` :

```blade
{{-- Scripts Vite --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Scripts additionnels par page --}}
@stack('scripts')
```

### Dans `create.blade.php` et `edit.blade.php`

Si utilisation de l'Option 1 (point d'entrée dédié) :

```blade
@push('scripts')
    @vite('resources/js/admin/place/store.js')
@endpush
```

Si utilisation de l'Option 2 (import conditionnel), wrapper le Livewire :

```blade
<div data-admin-place-form>
    <livewire:admin.place.store.place-store-form ... />
</div>
```

## Configuration globale (optionnel)

Pour passer les locales supportées aux modules JavaScript, ajouter dans le layout admin :

```blade
<script>
    window.appConfig = {
        locales: {
            supported: @json(config('locales.supported'))
        }
    };
</script>
```

## Debugging

Les modules exposent des instances globales pour le debugging :

```javascript
// Dans la console navigateur
window.AdminPlaceFormManager // Instance principale
window.AdminPlaceFormManager.slugGenerator // Module slug
window.AdminPlaceFormManager.locationMap // Module carte
window.AdminPlaceFormManager.photoSortable // Module photos
```

## Événements Livewire

### Écoutés par les modules

| Événement | Source | Module | Action |
|-----------|--------|--------|--------|
| `address-validated` | Livewire | LocationMap | Centre la carte sur l'adresse |
| `livewire:init` | Livewire | Tous | Initialisation post-Livewire |
| `livewire:morph.updated` | Livewire | PhotoSortable | Re-initialise après update DOM |

### Dispatched par les modules

| Événement | Module | Paramètres | Destination |
|-----------|--------|------------|-------------|
| `map:coordinates-changed` | LocationMap | `{latitude, longitude}` | Livewire |
| `photos:reordered` | PhotoSortable | `{orderMap}` | Livewire |

## Structure des fichiers

```
resources/js/admin/place/store/
├── init.js                 # Point d'entrée principal
├── slug-generator.js       # Auto-génération slugs
├── location-map.js         # Carte Leaflet interactive
├── photo-sortable.js       # Drag & drop photos
└── README.md              # Cette documentation
```

## Tests manuels

### Slug Generator
1. Saisir un titre en FR → slug auto-généré
2. Éditer manuellement le slug → auto-génération désactivée
3. Vider le slug → réactive auto-génération

### Location Map
1. Cliquer sur la carte → coordonnées mises à jour
2. Modifier latitude/longitude manuellement → marqueur se déplace
3. Drag le marqueur → coordonnées mises à jour

### Photo Sortable (mode édition uniquement)
1. Survoler une photo → icône drag handle apparaît
2. Drag & drop une photo → ordre mis à jour
3. Vérifier dispatch événement dans console réseau Livewire

## Compatibilité

- **Navigateurs** : Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Leaflet** : v1.9.4
- **SortableJS** : v1.15.0
- **Livewire** : v3.x
- **Alpine.js** : v3.x (déjà inclus avec Livewire)
