#!/bin/sh
# Script de déploiement production — Hôtelio

set -e

echo "==> Mise en maintenance..."
php artisan down --render="errors.503"

echo "==> Mise à jour des dépendances..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Compilation des assets..."
# npm ci && npm run build   # décommenter si vous avez Vite

echo "==> Migrations..."
php artisan migrate --force

echo "==> Nettoyage des caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Lien symbolique Storage..."
php artisan storage:link

echo "==> Permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> Remise en ligne..."
php artisan up

echo "✅ Déploiement terminé."
