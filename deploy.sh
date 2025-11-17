#!/bin/bash

# Script de déploiement pour Explo.space
# Usage: ./deploy.sh [--skip-maintenance]

set -e  # Arrêter le script en cas d'erreur

echo "============================================"
echo "  Déploiement Explo.space"
echo "============================================"

# Option pour skip la maintenance (utile pour debug)
SKIP_MAINTENANCE=false
if [ "$1" == "--skip-maintenance" ]; then
    SKIP_MAINTENANCE=true
fi

# 1. Activer le mode maintenance (sauf si --skip-maintenance)
if [ "$SKIP_MAINTENANCE" = false ]; then
    echo "→ Activation du mode maintenance..."
    php artisan down --retry=60 || true
fi

# 2. Récupérer les dernières modifications depuis le dépôt Git
echo "→ Mise à jour du dépôt Git..."
git fetch origin
git reset --hard origin/main
git clean -fd  # Supprimer fichiers et dossiers non-trackés

# 3. Installer les dépendances PHP avec composer.phar
echo "→ Installation des dépendances PHP..."
php composer.phar install --no-dev --optimize-autoloader --no-interaction

# 4. Vider tous les caches Laravel
echo "→ Nettoyage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 5. Optimiser les caches pour la production
echo "→ Optimisation des caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Exécuter les migrations (si nécessaire)
# Décommentez cette ligne si vous voulez exécuter les migrations automatiquement
# echo "→ Exécution des migrations..."
# php artisan migrate --force

# 7. Vérifier et corriger les permissions
echo "→ Correction des permissions..."
chmod -R 755 storage bootstrap/cache
find storage -type d -exec chmod 755 {} \;
find storage -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;

# 8. Désactiver le mode maintenance
if [ "$SKIP_MAINTENANCE" = false ]; then
    echo "→ Désactivation du mode maintenance..."
    php artisan up
fi

echo ""
echo "============================================"
echo "  ✓ Déploiement terminé avec succès !"
echo "============================================"
echo ""
echo "Version déployée : $(git rev-parse --short HEAD)"
echo "Branche : $(git branch --show-current)"
echo "Date : $(date '+%Y-%m-%d %H:%M:%S')"
