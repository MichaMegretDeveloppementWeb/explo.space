#!/bin/bash

# Se placer dans le répertoire du dépôt
echo "Déploiement en cours..."

# 1. Récupérer les dernières modifications depuis le dépôt Git
echo "Mise à jour du dépôt Git..."
git fetch origin
git reset --hard origin/main

# 2. Installer les dépendances PHP avec composer.phar
echo "Installation des dépendances PHP..."
php composer.phar install --no-dev --optimize-autoloader

# 3. Installer les dépendances JavaScript
echo "Installation des dépendances npm..."
npm install

# 4. Compiler les assets (par exemple via Vite)
echo "Compilation des assets..."
npm run build

# 5. (Optionnel) Exécuter les migrations si nécessaire
# echo "Exécution des migrations..."
# php artisan migrate --force

# 6. Vider le cache de Laravel (facultatif, mais recommandé)
echo "Nettoyage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Déploiement terminé."
