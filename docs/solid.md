# Principes SOLID (appliqués à COSMAP)

- **S** — Single Responsibility : une classe = une responsabilité.
- **O** — Open/Closed : étendre sans modifier l’existant.
- **L** — Liskov : respecter les contrats d’interface.
- **I** — Interface Segregation : interfaces petites et ciblées.
- **D** — Dependency Inversion : Services dépendent d’**interfaces** de Repositories.

Rappels concrets :
- Pas de logique métier durable dans Livewire/Controllers/Blade : déléguer au **Service**.
- Les requêtes vers la base de données sont faites par des repositories (respect des interfaces) appellés par les services
- Transactions autour des écritures multi-tables (Place + Photos + Tags).
- DTO si nécessaire pour clarifier les entrées/sorties de Services.
