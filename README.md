# Explo.space

**Annuaire mondial collaboratif des lieux de la conquête spatiale et de l'exploration de l'univers.**

Découvrez, explorez et contribuez à la cartographie mondiale des sites liés à la conquête spatiale : centres de lancement, observatoires, musées, sites historiques, et bien plus encore.

---

## 🚀 Fonctionnalités

### Pour les visiteurs
- **Exploration interactive** : Carte mondiale avec clustering intelligent des lieux
- **Double mode de recherche** :
  - **Autour de moi** : Recherche par adresse ou géolocalisation avec rayon configurable (jusqu'à 1500 km)
  - **Monde entier** : Exploration globale avec filtrage par thématiques
- **Fiches détaillées** : Informations complètes, photos en carrousel, coordonnées GPS
- **Système multilingue** : Interface et contenus en français et anglais avec traduction automatique
- **Proposition de lieux** : Formulaire guidé pour suggérer de nouveaux sites
- **Signalement** : Système de correction et signalement d'erreurs

### Pour les administrateurs
- **Interface d'administration** complète (tableau de bord, gestion des lieux, modération)
- **Workflow de validation** : Modération des propositions et signalements avec notifications email
- **Gestion multilingue** : Traduction automatique via DeepL API + édition manuelle
- **Organisation interne** : Système de tags (publics) et catégories (usage interne)
- **Gestion des photos** : Upload, réorganisation, miniatures automatiques
- **Traçabilité** : Logs d'audit pour toutes les actions sensibles

---

## 🛠️ Stack Technique

### Backend
- **Laravel 12** : Framework PHP moderne
- **Livewire 3** : Interactions temps réel sans API AJAX
- **PHP 8.3+** : Typage strict, enums, readonly properties
- **MySQL** : Base de données avec index spatiaux pour les coordonnées GPS

### Frontend
- **Blade** : Templates Laravel
- **Alpine.js** : Interactions légères
- **Tailwind CSS 4** : Framework utility-first
- **Vite** : Build tool moderne
- **Leaflet** + **OpenStreetMap** : Cartographie interactive
- **Leaflet.markercluster** : Clustering des marqueurs

### Cartographie
- **Tiles** : CartoDB Positron (fallback OpenStreetMap)
- **Géocodage** : Nominatim (OSM) avec extensibilité vers Google Places
- **Clustering** : Automatique pour zones denses (50+ marqueurs)
- **Bounding box dynamique** : Chargement uniquement de la zone visible

### Architecture
- **Pattern Repository** : Interfaces + implémentations pour persistence
- **Services métier** : Logique applicative segmentée par action/page
- **DTOs** : Objets de transfert typés entre couches
- **Strategy Pattern** : Traduction (DeepL), géocodage (Nominatim/Google)
- **SOLID** : Principes appliqués rigoureusement

### Qualité & Tests
- **PHPUnit 11** : 947 tests (unitaires, intégration, fonctionnels)
- **Laravel Pint** : Formatage automatique (PSR-12)
- **PHPStan** / **Larastan** : Analyse statique niveau 6
- **Rector** : Refactoring automatisé (mode prudent)

### Sécurité & RGPD
- **Google reCAPTCHA v3** : Protection anti-bot sur tous les formulaires publics
- **Policies** : Autorisation granulaire pour actions sensibles
- **Validation stricte** : Form Requests pour toutes les entrées
- **Hashage sécurisé** : Argon2/BCrypt pour mots de passe
- **Journalisation** : Logs quotidiens + alertes email sur erreurs critiques

---

## 📋 Prérequis

- **PHP** >= 8.3
- **Composer** >= 2.0
- **Node.js** >= 20.x
- **npm** >= 10.x
- **MySQL** >= 8.0
- **Extension PHP** : PDO, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, GD

---

## ⚙️ Installation

### 1. Cloner le repository
```bash
git clone https://github.com/MichaMegretDeveloppementWeb/explo.space.git
cd explo.space
```

### 2. Installer les dépendances PHP
```bash
composer install
```

### 3. Installer les dépendances JavaScript
```bash
npm install
```

### 4. Créer le fichier d'environnement
```bash
cp .env.example .env
```

### 5. Générer la clé d'application
```bash
php artisan key:generate
```

### 6. Configurer la base de données
Modifier le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=explo_space
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Exécuter les migrations
```bash
php artisan migrate
```

### 8. Générer les données de test (optionnel)
```bash
php artisan db:seed
```

### 9. Compiler les assets
```bash
# Développement
npm run dev

# Production
npm run build
```

### 10. Lancer le serveur de développement
```bash
php artisan serve
```

L'application est accessible à l'adresse : `http://localhost:8000`

---

## 🧪 Tests

### Exécuter tous les tests
```bash
composer test
# ou
vendor/bin/phpunit
```

**947 tests** couvrant :
- **Tests unitaires** : Services, DTOs, helpers, règles métier
- **Tests d'intégration** : Repositories, relations Eloquent
- **Tests fonctionnels** : Controllers, Livewire components, flux utilisateur

### Analyse statique
```bash
composer stan
# ou
vendor/bin/phpstan analyse --memory-limit=256M
```

### Formatage du code
```bash
composer fix
# ou
vendor/bin/pint -v
```

### Vérification complète
```bash
composer qa
# Exécute : Pint + PHPStan + PHPUnit
```

---

## 📁 Structure du Projet

```
app/
├── Contracts/                    # Interfaces (Repositories, Services)
│   ├── Repositories/
│   ├── Services/
│   └── Translation/
├── DTO/                          # Data Transfer Objects
├── Domain/                       # Logique métier transversale
│   └── Seo/                      # Stratégies SEO (hreflang, Open Graph)
├── Enums/                        # Énumérations typées
├── Exceptions/                   # Exceptions métier personnalisées
├── Helpers/                      # Fonctions utilitaires
├── Http/
│   ├── Controllers/              # Controllers segmentés par entité/action
│   ├── Middleware/               # SetLocale, Admin, etc.
│   └── Requests/                 # Form Requests de validation
├── Livewire/                     # Components Livewire
│   ├── Admin/                    # Interface administration
│   └── Web/                      # Interface publique
├── Models/                       # Modèles Eloquent
├── Repositories/                 # Implémentations repositories
│   ├── Admin/
│   └── Web/
├── Services/                     # Services métier
│   ├── Admin/
│   └── Web/
├── Strategies/                   # Pattern Strategy (traduction, géocodage)
│   ├── Geocoding/
│   └── Translation/
└── Support/                      # Classes support (config, helpers)

resources/
├── css/
│   ├── admin/                    # Styles administration
│   └── web/                      # Styles publics
├── js/
│   ├── admin/                    # JavaScript administration
│   └── web/                      # JavaScript publics (carte Leaflet)
├── views/
│   ├── admin/                    # Vues administration
│   ├── components/               # Composants Blade réutilisables
│   ├── layouts/                  # Layouts principaux
│   ├── livewire/                 # Vues Livewire
│   └── web/                      # Vues publiques

database/
├── migrations/                   # Migrations de base de données
├── seeders/                      # Seeders pour données de test
└── factories/                    # Factories pour tests

tests/
├── Feature/                      # Tests fonctionnels
├── Livewire/                     # Tests Livewire components
└── Unit/                         # Tests unitaires

lang/
├── en/                           # Traductions anglais
└── fr/                           # Traductions français
```

---

## 🌍 Système Multilingue

### Fonctionnement
- **URLs distinctes** : `/fr/lieux/{slug-fr}` vs `/en/places/{slug-en}`
- **Segments traduits** : Chemins d'URL localisés (ex: `/fr/explorer` vs `/en/explore`)
- **Slugs traduits** : Chaque entité a un slug spécifique par langue
- **Détection automatique** : Langue du navigateur détectée à la première visite
- **Cookie de persistance** : Préférence de langue sauvegardée (1 an)

### Tables de traduction
- `place_translations` : titre, description, slug, infos pratiques
- `tag_translations` : nom, description, slug
- `category_translations` : nom, description, slug

### Traduction automatique
- **DeepL API** : Traduction de haute qualité
- **Interface admin** : Boutons "Traduire automatiquement" pour chaque champ
- **Détection de langue** : Identification automatique de la langue source sur les propositions visiteurs

### SEO multilingue
- **Hreflang** : Balises `<link rel="alternate" hreflang="...">`
- **Canonical** : URL canonique par langue
- **Open Graph** : `og:locale` + `og:locale:alternate`
- **JSON-LD** : Données structurées multilingues

---

## 🗺️ Configuration Cartographie

### Tiles
- **Principal** : CartoDB Positron (style épuré)
- **Fallback** : OpenStreetMap standard
- **Zoom** : Min 2, Max 19

### Clustering
- **Activation** : Si > 50 marqueurs
- **Rayon** : 80 pixels
- **Désactivation** : Au zoom 18
- **Icônes** : Cercles colorés avec nombre de lieux

### Performance
- **Bounding box dynamique** : Chargement uniquement de la zone visible
- **Debounce** : 300ms avant requête sur changement de vue
- **Eager loading** : Relations chargées de manière optimisée
- **Index spatiaux** : Sur coordonnées GPS

---

## 🔒 Sécurité & RGPD

### Protection
- **CSRF** : Tokens automatiques sur tous les formulaires
- **XSS** : Échappement automatique Blade
- **SQL Injection** : Requêtes paramétrées via Eloquent
- **Validation** : Form Requests strictes sur toutes les entrées
- **reCAPTCHA v3** : Sur tous les formulaires publics

### RGPD
- **Minimisation** : Collecte uniquement des données nécessaires
- **Consentement** : Formulaires avec email de contact uniquement
- **Transparence** : Pages légales (mentions légales, politique de confidentialité, CGU)
- **Droits** : Contact RGPD via email configuré

### Journalisation
- **Logs quotidiens** : Rotation automatique
- **Alertes email** : Sur erreurs critiques (500)
- **Audit logs** : Traçabilité des actions administrateur

---

## 🤝 Contribution

Ce projet suit les principes suivants :
- **Conventional Commits** : `feat:`, `fix:`, `refactor:`, `test:`, `docs:`, etc.
- **SOLID** : Architecture rigoureuse
- **Tests** : Couverture maximale requise
- **Qualité** : `composer qa` doit être au vert avant commit

---

## 📄 Licence

Ce projet est la propriété de **Jeremie Roussel** et développé par **Micha Megret - Développement Web**.

Tous droits réservés.

---

## 👤 Auteur

**Micha Megret**
Développeur Web Full Stack
[https://github.com/MichaMegretDeveloppementWeb](https://github.com/MichaMegretDeveloppementWeb)

---

## 📞 Contact

Pour toute question ou suggestion :
- **Email** : (à configurer via `.env`)
- **GitHub Issues** : [https://github.com/MichaMegretDeveloppementWeb/explo.space/issues](https://github.com/MichaMegretDeveloppementWeb/explo.space/issues)
