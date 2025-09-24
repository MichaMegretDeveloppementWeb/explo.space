# Structure du Site COSMAP

## Vue d'ensemble

COSMAP est une plateforme collaborative de cartographie mondiale des sites spatiaux avec deux niveaux d'accès : public (visiteurs non connectés) et administration.

---

## 🗂️ Architecture des Pages

### 🌐 PAGES PUBLIQUES (Accessibles à tous)

#### 1. Pages de Présentation
- **`/` - Homepage** ✅ *(Développé)*
  - Hero, fonctionnalités, lieux emblématiques, stats communauté, CTA
  - **Navigation** : vers Explorer, Créer un compte

- **`/fonctionnalites` - Page Fonctionnalités**
  - Détail des 2 modes de recherche, workflow de modération, API
  - **Navigation** : vers Explorer, Créer un compte

- **`/a-propos` - À propos**
  - Mission, équipe, vision, contact
  - **Navigation** : vers toutes les pages publiques

#### 2. Pages Légales & Support
- **`/mentions-legales` - Mentions légales**
- **`/politique-confidentialite` - RGPD**
- **`/cgu` - Conditions d'utilisation**
- **`/contact` - Contact**
  - Formulaire avec reCAPTCHA v3

#### 3. Pages de Contenu Principal

##### `/explorer` - Carte interactive publique ⭐
**Page unique avec deux modes d'exploration :**

**Structure interface :**
```
┌─────────────────────────────────────────────────┐
│  [ • Autour de moi ]  [   Monde entier  ] │ ← Tabs
├─────────────────────────────────────────────────┤
│  [Contrôles spécifiques selon mode]             │ ← Zone filtres
├─────────────────────────────────────────────────┤
│              CARTE INTERACTIVE                  │ ← Leaflet + clustering
├─────────────────────────────────────────────────┤
│              LISTE RÉSULTATS                    │ ← Pagination
└─────────────────────────────────────────────────┘
```

**Mode "Autour de moi" - État initial :**
- **URL** : `/explorer?mode=proximity&lat=48.8566&lng=2.3522&radius=200&address=Paris%2C%20France`
- **Interface** :
  ```
  🔍 [Saisir une adresse...    ] [📍 Me géolocaliser]
  🎚️  Rayon: [═══●═══] 200 km (par défaut)
  🏷️  Tags: [+ Ajouter un filtre] (optionnel)
  ```
- **Comportement** :
  - Champ adresse avec autocomplétion (Nominatim)
  - Bouton géolocalisation pour récupérer position actuelle
  - Saisie adresse OU géolocalisation définit automatiquement lat/lng du center
  - Carte centrée avec cercle rayon + liste lieux

**Mode "Monde entier" - État initial :**
- **URL** : `/explorer?mode=worldwide&tags=nasa,spacex`
- **Interface** :
  ```
  🏷️  [+ Filtrer par thématique] (optionnel)
  ```
- **Comportement** :
  - Pas de champ adresse (mode mondial)
  - Carte mondiale avec tous les lieux + clustering + liste paginée
  - Filtrage optionnel par tags sélectionnés

**Responsive Mobile :**
```
Desktop/Tablette : Carte AU DESSUS + Liste EN DESSOUS
Mobile : [ 🗺️ Carte ] [ 📋 Liste ] ← Boutons toggle (une vue à la fois)
```

##### Autres pages publiques
- **`/lieux/{slug}` - Fiche lieu publique**
  - Toutes infos, carrousel photos
  - Actions : Signaler erreur (formulaire avec email + reCAPTCHA), Proposer modification (formulaire avec email + reCAPTCHA)
  - **Navigation** : retour Explorer, lieux similaires

- **`/tags/{slug}` - Page thématique**
  - Tous lieux d'un tag mondial, carte + liste paginée
  - **Navigation** : vers fiches lieux, autres tags

- **`/proposer-lieu` - Nouveau lieu**
  - Formulaire guidé + carte interactive + email contact + reCAPTCHA v3
  - **Champs** : titre, description, coordonnées, adresse, infos pratiques, photos (PAS de tags)
  - **Navigation** : confirmation soumission

- **`/proposer-correction/{lieu-slug}` - Formulaire correction**
  - Formulaire de signalement/correction pour un lieu existant + email contact + reCAPTCHA v3

### 🔐 CONNEXION ADMINISTRATEUR

- **`/admin/connexion` - Connexion admin**
  - Formulaire email/mot de passe pour les administrateurs
  - Pas d'inscription (comptes créés par le super-admin)
  - Redirection vers `/admin/dashboard` après connexion

### 🛡️ PAGES ADMINISTRATION

#### 6. Espace Admin (`/admin/*`)
- **`/admin/dashboard` - Vue d'ensemble admin**
  - Stats, demandes en attente, activité récente
  - **Rôles** : Admin + Super-admin

**Modération des demandes :**
- **`/admin/demandes/lieux` - Liste PlaceRequests**
  - Demandes en attente de validation
- **`/admin/demandes/lieux/{id}` - Détail/Validation PlaceRequest**
  - Actions : accepter/refuser + raison, éditer avant validation

- **`/admin/demandes/modifications` - Liste EditRequests**
  - Signalements/corrections en attente
- **`/admin/demandes/modifications/{id}` - Détail/Validation EditRequest**
  - Actions : accepter/refuser + raison, appliquer modifications

**Gestion des lieux :**
- **`/admin/lieux` - Liste des lieux**
  - CRUD complet, recherche, filtres, mise "à l'affiche"
- **`/admin/lieux/{id}` - Détail/Édition lieu**
  - Actions : Éditer toutes informations, supprimer, featured
- **`/admin/lieux/nouveau` - Création lieu**
  - Création directe par admin (bypass workflow utilisateur)

**Gestion des tags :**
- **`/admin/tags` - Liste des tags**
  - Vue d'ensemble de toutes les thématiques
- **`/admin/tags/{id}` - Détail/Édition tag**
  - Actions : Modifier, fusionner, réorganiser
- **`/admin/tags/nouveau` - Création tag**
  - Création de nouvelles thématiques

**Gestion des catégories :**
- **`/admin/categories` - Liste des catégories**
  - Vue d'ensemble de toutes les catégories internes
- **`/admin/categories/{id}` - Détail/Édition catégorie**
  - Actions : Modifier, fusionner, réorganiser
- **`/admin/categories/nouveau` - Création catégorie**
  - Création de nouvelles catégories internes

- **`/admin/parametres` - Réglages système**
  - Config reCAPTCHA, emails, maintenance
  - **Rôles** : Super-admin uniquement

---

## 🔗 Maillage & Navigation

### Navigation Principale (Navbar)
```
Logo | Fonctionnalités | Explorer | Communauté | À propos | Proposer un lieu | Connexion Admin
```

### Flux Utilisateur Types

#### 🌟 Visiteur Découverte
`Homepage` → `Explorer` → `Fiche lieu` → `Proposer lieu/correction`

#### 🎯 Visiteur Contributeur
`Homepage` → `Proposer lieu` → `Email confirmation` → `Modération admin` → `Notification email acceptation/refus`

#### 🔧 Admin Modération
`Admin dashboard` → `Demandes lieux` → `Édition/Validation` → `Publication` → `Stats`

### Breadcrumbs Exemples
- `Accueil > Explorer > Centre spatial Kennedy`
- `Admin > Demandes > Lieux en attente > Validation #1247`

---

## 🎯 Fonctionnalités Techniques Clés

### Page Explorer - Spécifications
**1. Bounding Box dynamique :**
- Chargement uniquement des lieux **visibles dans la zone carte**
- Zoom IN → retire des lieux de la liste
- Zoom OUT → ajoute des lieux à la liste
- **Performance** : Requêtes optimisées selon viewport

**2. Clustering intelligent :**
- Regroupe les lieux proches selon le niveau de zoom
- Nombre dans les clusters, clic cluster → zoom sur la zone

**3. Synchronisation Carte ↔ Liste :**
- Survol liste → highlight sur carte
- Clic marker → scroll vers item liste
- Pagination liste → markers correspondants

**4. États d'interface :**
```javascript
// Mode "Autour de moi"
{
  mode: 'proximity',
  center: { lat: 48.8566, lng: 2.3522 }, // Défini par adresse ou géoloc
  radius: 200000, // mètres (200km par défaut)
  tags: [], // optionnel
  address: 'Paris, France'
}

// Mode "Monde entier"
{
  mode: 'worldwide',
  tags: ['nasa', 'spacex'], // optionnel, multi-sélection possible
  bounds: 'world'
}
```

### Autres Fonctionnalités par Page

#### Fiche Lieu (Conversion)
- Carrousel photos optimisé
- Infos complètes + pratiques
- **Actions** : Signaler → `EditRequest`
- Lieux similaires → retention
- **SEO** : URL slug, metas optimisées


---

## 🚀 Plan de Développement

### Phase 1 - Core Fonctionnel (MVP)
1. **`/explorer`** - Page unique avec double mode
2. **`/lieux/{slug}`** - Fiche lieu publique
3. **`/proposer-lieu`** - Formulaire proposition
4. **`/proposer-correction/{lieu-slug}`** - Formulaire correction
5. **`/admin/connexion`** - Connexion admin

### Phase 2 - Administration
6. **`/admin/dashboard`** - Vue d'ensemble admin
7. **`/admin/demandes/lieux`** + **`/admin/demandes/lieux/{id}`**
8. **`/admin/demandes/modifications`** + **`/admin/demandes/modifications/{id}`**
9. **`/admin/lieux`** + **`/admin/lieux/{id}`** + **`/admin/lieux/nouveau`**
10. **`/admin/tags`** + **`/admin/tags/{id}`** + **`/admin/tags/nouveau`**
11. **`/admin/categories`** + **`/admin/categories/{id}`** + **`/admin/categories/nouveau`**

### Phase 3 - Finitions
12. **Pages présentation** - `/fonctionnalites`, `/a-propos`
13. **Pages légales** - `/mentions-legales`, `/cgu`, etc.

---

## 🔒 Contrôles d'Accès

### Pages Publiques
- Aucune authentification requise
- Actions de proposition/signalement via formulaires avec email + reCAPTCHA v3

### Pages Admin
- **Admin** : Consultation + modération
- **Super-admin** : Gestion complète + paramètres système
- Logs d'audit pour toutes les actions sensibles

---

## 📱 Considérations Responsive

### Breakpoints Standard
- **Mobile** : 320px-767px
- **Tablette** : 768px-1023px  
- **Desktop** : 1024px+

### Adaptations Clés
- **Explorer** : Toggle Carte/Liste sur mobile
- **Navigation** : Menu hamburger < 1024px
- **Formulaires** : Layout adaptatif
- **Admin** : Tables responsives avec scroll horizontal

---

Cette structure couvre l'intégralité des besoins fonctionnels de COSMAP tout en maintenant une architecture claire et évolutive.