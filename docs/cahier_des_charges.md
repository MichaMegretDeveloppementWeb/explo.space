# Cahier des charges - COSMAP (explo.space)

## Vue d'ensemble du projet

**Mission** : Annuaire mondial visant à favoriser la découverte des divers objets issus ou liés à la conquête spatiale, ou à la découverte de l'univers.

**Philosophie design** : Site épuré où l'aspect fonctionnel prime sur l'esthétique (inspiré des interfaces Google), avec simplicité pour le responsive et la compatibilité mobile.

## Fonctionnalités principales

### 1. Deux modes de recherche fondamentaux

#### Mode "Autour de moi"
- **Principe** : L'utilisateur saisit une adresse ou utilise sa géolocalisation et souhaite découvrir les références dans un rayon défini
- **Interface** :
  - Champ de saisie d'adresse avec autocomplétion (Nominatim)
  - Bouton de géolocalisation pour récupérer la position actuelle
  - Curseur de sélection du rayon (limité à 1500 km) - référence : https://www.handiguide.sports.gouv.fr/?distance=1
  - Interactivité carte avec pastilles/pointeurs + listing des résultats
  - Filtrage par tags/thématiques (par défaut "toute thématique")

#### Mode "Monde entier"
- **Principe** : Affichage mondial de toutes les références avec possibilité de filtrage par tags
- **Interface** :
  - Pas de champ d'adresse (vue mondiale)
  - Filtrage optionnel par thématiques
- **Fonctionnement** : Affichage initial de tous les lieux avec clustering, puis filtrage optionnel par thématiques
- **Affichage** : Carte mondiale indépendamment de la proximité géographique

### 2. Structure d'une référence (Place)

#### Champs obligatoires
- **Titre** : repris dans l'URL sans caractères spéciaux (slug pour SEO)
- **Description** : longue description textuelle
- **Coordonnées GPS** : sélection directe sur carte OU saisie d'adresse (synchronisation bidirectionnelle)
- **Adresse physique**
- **Tags/thématiques** : assignés par l'administrateur lors de la validation (non sélectionnables par le visiteur)
- **Catégories** : champ interne pour organisation admin (non visible publiquement)
- **Photos** : 1 à plusieurs photographies
- **Informations pratiques**

#### Fonctionnalités photos
- **Miniatures** suffisamment visibles en premier
- **Carrousel** : clic sur une photo → défilement en format harmonisé, taille réelle, qualité maximale
- **Navigation** : sans quitter la page de référence

#### Sécurité
- **reCAPTCHA v3** (invisible) sur tous les formulaires :
  - Ajout de référence
  - Moteur de recherche
  - Signalement d'erreur
  - Contact email
- **Email de contact** : champ obligatoire pour suivi et notifications (pas de compte utilisateur)

### 3. Workflow de modération

#### Proposition par visiteur
1. **Soumission** : Visiteur propose une référence via formulaire avec email + reCAPTCHA v3 (sans sélection de tags)
2. **Réception admin** : Référence arrive dans l'espace administration avec email de contact
3. **Modération** : Admin peut compléter/modifier, assigner tags et catégories avant validation
4. **Publication** : Validation définitive pour affichage public + notification email au proposant

#### Gestion des tags et catégories
- **Côté visiteur** : Aucune sélection de tags lors de la proposition
- **Côté admin** : Assignment des tags (visibles publiquement) et catégories (usage interne) lors de la validation

### 4. Affichage des références

#### Page de détail
- **Contenu** : Tous les champs (titre, description, coordonnées, adresse, infos pratiques, tags, photos)
- **Métadonnées** : 
  - "Fiche ajoutée le [date]"
  - "Dernière modification le [date]"
- **Actions** :
  - Signaler erreur/apporter précision (formulaire avec email + reCAPTCHA v3 + n° référence)
  - Proposer modification (formulaire avec email + reCAPTCHA v3)
  - Système de puce sur la carte

#### Gestion carte - références multiples
- **Problème** : Plusieurs références pour même adresse (ex: Cap Canaveral)
- **Solution** : Icône différente ou avec nombre de résultats pour regroupement

### 5. Page d'accueil

#### Contenu principal
- **Références à la une** : présélectionnées dans l'admin ("mettre à l'affiche")
- **Dernières références** : ajoutées récemment
- **Organisation** : 2 lignes distinctes
- **Moteur de recherche** : selon fonctionnalités demandées
- **Accès ajout** : possibilité d'aller au formulaire d'ajout

#### Espaces personnalisables
- 2-3 blocs libres pour adaptations secondaires :
  - Flux d'actualité
  - Espaces publicitaires
  - Autres contenus

**Référence design** : https://www.rankingcoach.com/fr-fr/fonctionnalites

### 6. Navigation et UX

#### Moteur de recherche
- **Présence** : Bandeau sur toutes les pages
- **Gestion résultats vides** : "Aucune référence ne correspond à vos critères"
- **Retour recherche** : Après clic sur référence, possibilité de revenir aux résultats OU affichage référence sous résultats (5 par ligne + pagination)

### 7. Optimisation SEO

#### Images
- **Google Images** : Titre défini dans code pour référencement
- **Balises descriptives** : Auto-incrémentation du titre de référence dans balises image

#### Pages
- **URL rewriting** activé
- **Référencement statique** : Toutes références en pages individuelles sur Google

### 8. Système multilangue

#### Enregistrement des traductions
1. **Double formulaire** : Double saisie FR/EN (meilleur choix pour fidélité traduction)
2. **Google Translate ou DeepL** : Intégration bouton traduction automatique

#### Interface
- **Sélecteur langue** : Petit drapeau FR/EN dans menu

#### Description du système
- **Le système de gestion multilangue** doit permettre un gestion multilangue complet et optimisé SEO avec generation d'url distinctes, sitemap sur-mesure, traduction des slug et des parametres d'url (exemple /lieux/observatoire donnera /places/observatory), enregitrement des traduction pour les données des lieux, tags, categories (mais uniquement ce qui necessite une traduction), etc...
- **Pour des explications plus detaillées** voir @docs/translate_system.md où tout le système est expliqué point par point.

## Points techniques importants

### Base de données
- Optimisation pour éviter la "casse" avec milliers de références
- Gestion efficace du clustering géographique
- Index sur coordonnées GPS et tags

### Performance
- Chargement optimisé des images (miniatures)
- Pagination intelligente des résultats
- Cache pour requêtes fréquentes

### Sécurité
- Protection anti-robot sur tous formulaires publics
- Validation côté serveur de tous les inputs
- Gestion sécurisée des uploads d'images

## Workflow utilisateur type

1. **Visiteur arrive** → Page d'accueil avec références mise en avant
2. **Recherche** → Mode "autour de moi" ou "monde entier"
3. **Résultats** → Carte interactive + liste paginée
4. **Détail** → Page dédiée avec toutes infos + carrousel photos
5. **Actions** → Signalement (avec email), ajout nouvelle référence (avec email)
6. **Modération** → Admin valide/complète, assigne tags/catégories avant publication + notification email

Cette structure respecte la philosophie "fonctionnel avant esthétique" tout en garantissant une expérience utilisateur fluide et un référencement optimal.
