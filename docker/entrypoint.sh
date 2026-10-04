#!/bin/sh
set -e

echo ">> Préparation de Mosnoky au démarrage..."

# Vide les anciens caches (au cas où une ancienne version était en cache)
php artisan config:clear || true

# Applique les migrations sur la base Supabase (sans poser de questions)
php artisan migrate --force

# Crée le lien storage -> public (pour que les photos uploadées s'affichent)
php artisan storage:link || true

# Mets en cache la config et les routes pour un site plus rapide
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ">> Mosnoky est prêt, démarrage du serveur web..."

exec "$@"
