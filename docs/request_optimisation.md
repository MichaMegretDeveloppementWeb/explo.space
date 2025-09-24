# Optimisation des requêtes & performance

## Anti-N+1
- Eager loading (`with()`), relations minimales utiles.
- SELECT ciblés (colonnes nécessaires).
- Eviter les attributs personnalisés sur les models, ils genèrent parfois des conflits dans les requetes. 
Nous les utiliseront si nécessaire uniquement et pas de manière systématique. Ne pas les ajouter sans accord préalable.
Quand tu remarque qu'un attribut personnalisé serait vraiment utile, il est important de me le faire savoir et de me le proposer. De manière générale on preferera ajouter des addSelect dans des requettes optimisées directement plutot que des attribut personnalisés dans le model pour éviter les conflits 

## Recherche & carte
- Modes : "Autour de moi" (rayon ≤ 1500 km) et "Thématique" (tag global).
- **Clustering** côté carte pour zones denses.
- Pagination côté liste (30–50 par page).
- Charger **uniquement** ce qui est dans la **zone visible** (bounding box).

## Index & DB
- Indexer slug, pivots de tags, colonnes de tri/filtre, coordonnées (spatial index si possible).
- Sur lenteurs, analyser avec `EXPLAIN`.

## Cache & images
- HTTP cache public raisonnable pour pages publiques (sans PII).
- Lazy-loading d’images, miniatures obligatoires.
