# Conversation - État Actuel du Projet COSMAP

## 📍 Où nous en sommes

### ✅ **Travaux Terminés**
1. **Homepage responsive complète** - Design validé par le client
   - 8 sections avec responsive parfait (mobile/tablette/desktop)
   - Hero, How it works, Features, Community contribution, Featured places, Community stats, Why COSMAP, CTA
   - Design system établi avec Tailwind CSS

2. **Navbar responsive corrigée** - Breakpoints cohérents
   - Mobile (320-767px) : Menu hamburger
   - Tablette (768-1023px) : Actions visibles + Menu hamburger pour navigation
   - Desktop (1024px+) : Navigation complète visible

3. **Architecture complète documentée** - `docs/site_structure.md`
   - 52 pages répertoriées avec fonctionnalités détaillées
   - Plan de développement en 4 phases
   - Spécifications UX complètes de la page `/explorer`

### 🎯 **Prochaine Étape - Phase 1 Front-End Statique**

**OBJECTIF** : Créer les pages statiques avec données mockées pour validation client, en préparant l'intégration backend future.

---

## 🗺️ Page Explorer - Spécifications UX Validées

### **Interface Unique avec Deux Modes**
```
┌─────────────────────────────────────────────────┐
│  [ • Autour de moi ]  [   Thématique mondial  ] │ ← Tabs
├─────────────────────────────────────────────────┤
│  [Contrôles spécifiques selon mode]             │ ← Zone filtres
├─────────────────────────────────────────────────┤
│              CARTE INTERACTIVE                  │ ← Leaflet + clustering
├─────────────────────────────────────────────────┤
│              LISTE RÉSULTATS                    │ ← Pagination
└─────────────────────────────────────────────────┘
```

### **Mode "Autour de moi" - Comportement**
- **État initial** : Paris, 200km de rayon par défaut
- **Interface** :
  ```
  🔍 [Paris, France          ] [📍 Me géolocaliser]
  🎚️  Rayon: [═══●═══] 200 km
  🏷️  Tags: [+ Ajouter un filtre] (optionnel)
  ```
- **Saisie adresse** → définit automatiquement lat/lng du center
- **Résultats** : Lieux dans le cercle de rayon

### **Mode "Thématique mondial" - Comportement**
- **État initial** : Carte vide + "Veuillez sélectionner une thématique"
- **Interface** :
  ```
  🏷️  [+ Sélectionner une thématique] (obligatoire, min 1 tag)
  ```
- **Après sélection** : 
  ```
  🏷️  [NASA] [x] [SpaceX] [x] [+ Ajouter une thématique]
  ```
- **Multi-sélection tags** : Possible avec opérateur ET/OU
- **Résultats** : Carte mondiale + clustering + liste paginée

### **Responsive Mobile**
```
Desktop/Tablette : Carte AU DESSUS + Liste EN DESSOUS
Mobile : [ 🗺️ Carte ] [ 📋 Liste ] ← Toggle (une vue à la fois)
```

### **Fonctionnalités Techniques**
1. **Bounding Box dynamique** : Chargement uniquement des lieux visibles
2. **Clustering intelligent** : Regroupement selon zoom
3. **Synchronisation Carte ↔ Liste** : Survol, clic, pagination

---

## 🚀 Plan de Développement - Phase 1

### **Pages Prioritaires (Front-end statique)**
1. **`/explorer`** - Page unique avec double mode + carte interactive
2. **`/lieux/{slug}`** - Fiche lieu publique détaillée
3. **`/proposer-lieu`** - Formulaire proposition nouveau lieu
4. **`/proposer-correction/{slug}`** - Formulaire correction lieu existant
5. **`/connexion` + `/inscription`** - Pages d'authentification

### **Approche Technique**
- **Front-end uniquement** : Blade + Tailwind + JavaScript vanilla
- **Données mockées** : Arrays PHP intégrés dans les vues
- **Design system** : Cohérent avec homepage validée
- **Responsive** : 3 breakpoints (320px/768px/1024px)
- **Préparation backend** : Structures de données futures

---

## ❓ Questions en Attente (À clarifier)

### **1. Données Mock pour Explorer**
- Combien de lieux fictifs ? (50-100 pour Paris, 200-500 mondial)
- Tags catégories : NASA, SpaceX, ESA, Observatoires, Musées, Bases ?

### **2. Niveau Fonctionnalité Carte**
- Leaflet complètement intégré avec markers cliquables ?
- Clustering visuel fonctionnel ?
- Toggle mobile avec animations ?
- Synchronisation carte ↔ liste active ?

### **3. Formulaires Interactivité**
- Validation côté client uniquement ?
- Géocodage/autocomplétion Nominatim en live ?
- Upload images mockée ou vraie preview ?
- reCAPTCHA v3 intégré ou placeholder ?

### **4. Design System**
- Cards standardisées (lieu, stat, formulaire) ?
- Boutons système (primary, secondary, danger) ?
- États de chargement (skeleton loaders) ?
- Messages d'erreur/succès standardisés ?

---

## 🛠️ Structure Technique Préparée

### **Routes à Créer**
```php
// routes/web.php
Route::get('/explorer', [ExplorerController::class, 'index'])->name('explorer');
Route::get('/lieux/{slug}', [PlaceController::class, 'show'])->name('place.show');
Route::get('/proposer-lieu', [PlaceRequestController::class, 'create'])->name('place.create');
Route::get('/proposer-correction/{slug}', [EditRequestController::class, 'create'])->name('place.correction');
Route::get('/connexion', [AuthController::class, 'login'])->name('login');
Route::get('/inscription', [AuthController::class, 'register'])->name('register');
```

### **Mock Data Structure**
```php
// Exemple Place
$places = [
    [
        'id' => 1,
        'title' => 'Centre spatial Kennedy',
        'slug' => 'centre-spatial-kennedy',
        'description' => 'Centre de lancement historique de la NASA...',
        'coordinates' => ['lat' => 28.5721, 'lng' => -80.6480],
        'address' => 'Kennedy Space Center, FL 32899, USA',
        'tags' => ['NASA', 'Lancement', 'Historique'],
        'photos' => [
            ['url' => '/images/places/kennedy-1.jpg', 'is_main' => true],
            ['url' => '/images/places/kennedy-2.jpg', 'is_main' => false],
        ],
        'practical_info' => 'Ouvert 7j/7, Visites guidées disponibles',
        'created_at' => '2024-01-15',
        'updated_at' => '2024-02-10'
    ]
    // ... autres lieux
];
```

### **Composants Blade à Développer**
```
resources/views/components/app/
├── place/
│   ├── card.blade.php (carte lieu pour liste)
│   ├── marker-popup.blade.php (popup carte)
│   └── detail-hero.blade.php (hero fiche lieu)
├── forms/
│   ├── search-input.blade.php (recherche adresse)
│   ├── tag-selector.blade.php (sélecteur tags)
│   └── location-picker.blade.php (carte sélection position)
└── ui/
    ├── button.blade.php (système boutons)
    ├── skeleton.blade.php (états chargement)
    └── alert.blade.php (messages utilisateur)
```

---

## 📋 Workflow par Page

### **Étapes de Développement**
1. **Structure HTML/Blade** - Layout et composants
2. **Données mockées** - Arrays PHP réalistes
3. **Styling responsive** - Tailwind cohérent avec homepage
4. **JavaScript interactions** - Fonctionnalités UX
5. **Validation client** - Tests et ajustements

### **Page Explorer - Actions Spécifiques**
1. Créer layout avec tabs "Autour de moi" / "Thématique mondial"
2. Implémenter zones de contrôles dynamiques selon mode
3. Intégrer Leaflet avec clustering et markers
4. Développer toggle mobile Carte/Liste
5. Créer synchronisation carte ↔ liste
6. Ajouter données mock (lieux Paris + worldwide)
7. Tester responsive sur tous breakpoints

---

## 🎯 Objectif Immédiat

**Une fois cette conversation reprise :**

1. **Clarifier les questions en attente** avec le client
2. **Commencer par `/explorer`** - Page la plus complexe et centrale
3. **Créer la structure de routes** pour Phase 1
4. **Développer les composants Blade** réutilisables
5. **Intégrer Leaflet** avec fonctionnalités interactives
6. **Valider responsive** sur tous devices

**Priorité absolue** : Page `/explorer` fonctionnelle avec les deux modes, carte interactive, et données mockées réalistes pour présentation client.

---

## 💡 Contexte Projet

- **COSMAP** : Annuaire mondial sites spatiaux
- **Stack** : Laravel 12 + Livewire 3 + Blade + Tailwind + MySQL
- **Philosophie** : Fonctionnel avant esthétique (style Google)
- **Deux modes recherche** : "Autour de moi" (géoloc + rayon) + "Thématique mondial" (tags)
- **Responsive** : Mobile-first, 3 breakpoints
- **Homepage** : ✅ Terminée et validée client
- **Phase actuelle** : Front-end statique pour validation maquettes