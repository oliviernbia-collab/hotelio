#!/bin/bash
# Déploiement du site web sur LWS, lancé par cPanel (Git Version Control → .cpanel.yml) depuis
# la copie du dépôt (~/repositories/hotelio). Copie webPHP/ dans ~/hotelio (servi par
# hotelio-ci.site), installe les dépendances, migre la base et régénère les caches Laravel.
set -euo pipefail

REPO_DIR="$(pwd)"
APP_DIR="$HOME/hotelio"
PHP=/usr/local/bin/php
COMPOSER="$(command -v composer || echo /opt/cpanel/composer/bin/composer)"

echo "==> Mise en maintenance"
"$PHP" "$APP_DIR/artisan" down --retry=30 || true
# Toujours remettre le site en ligne, même si une étape échoue
trap '"$PHP" "$APP_DIR/artisan" up || true' EXIT

echo "==> Copie du code"
# Mêmes exclusions que webPHP/deploy-lws.ps1. Sans --delete : .env, fichiers envoyés
# (storage/) et lien public/storage du serveur restent intacts.
rsync -a --no-perms --omit-dir-times \
  --exclude='.env' --exclude='.env.*' --exclude='.git' --exclude='node_modules' --exclude='/vendor/' \
  --exclude='/storage/' --exclude='/public/storage' --exclude='/public/hot' \
  --exclude='/bootstrap/cache/*.php' --exclude='/tests/' --exclude='/docker/' --exclude='/deploy/' \
  --exclude='Dockerfile' --exclude='docker-compose.yml' --exclude='phpunit.xml' \
  --exclude='database.sqlite' --exclude='deploy-lws.ps1' --exclude='*.zip' --exclude='*.log' \
  "$REPO_DIR/webPHP/" "$APP_DIR/"

cd "$APP_DIR"

echo "==> Dépendances Composer"
"$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --no-progress

echo "==> Migrations"
"$PHP" artisan migrate --force

echo "==> Caches"
"$PHP" artisan optimize:clear
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache

echo "==> Déploiement web terminé"
