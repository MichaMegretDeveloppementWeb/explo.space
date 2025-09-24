# Base de données - Conventions & Directives

## Moteur et encodage
**OBLIGATOIRE** sur toutes les tables :
- **Moteur** : `InnoDB` (pour les contraintes de clés étrangères avec CASCADE)
- **Charset** : `utf8mb4` (support complet Unicode)
- **Collation** : `utf8mb4_general_ci` (insensible à la casse, performances optimales)

## Implémentation dans les migrations
```php
Schema::create('table_name', function (Blueprint $table) {
    $table->engine('InnoDB');
    $table->charset('utf8mb4');
    $table->collation('utf8mb4_general_ci');
    
    // Colonnes...
    
});
```

## Avantages CASCADE
- **onDelete cascade** : suppression automatique des enregistrements enfants
- **onUpdate cascade** : mise à jour automatique des références
- **Intégrité** : cohérence garantie par la base de données
- **Performance** : évite les requêtes manuelles de nettoyage

## Index spatiaux
Pour les coordonnées GPS (`Place`), prévoir un index spatial :
```php
$table->point('coordinates')->spatialIndex();
```

## Conventions de nommage
- Tables : `snake_case` au pluriel (`places`, `place_requests`)
- Colonnes : `snake_case` (`created_at`, `admin_id`)
- Clés étrangères : `{table_singular}_id` (`user_id`, `place_id`)
- Index : automatiques via conventions Laravel
