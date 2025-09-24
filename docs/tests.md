# Stratégie de tests — PHPUnit

La couverture cible est de 100% de l'application

## Types
- **Unitaires** : Services, helpers, règles métier, validations complexes.
- **Intégration** : Repositories (requêtes spécifiques), relations Eloquent.
- **Fonctionnels** : Livewire/HTTP (200/4xx/5xx, interactions de base), flux PlaceRequest/EditRequest.

## Bonnes pratiques
- Factories pour la donnée ; éventuellement seeds de test.
- Cas limites : données vides/invalides, droits (Policies), taille images, quotas, etc.
- Un test rouge se **corrige** (ne pas l’ignorer).

## Commandes
- `composer test` → `vendor/bin/phpunit`
- Fichier `phpunit.xml` à la racine ; par défaut DB **sqlite en mémoire** (modifiable).
