# ========================================================
# PHASE 40 - Dockerfile pour héberger Mosnoky gratuitement
# sur Render.com avec Supabase (PostgreSQL) comme base de données
# (corrige l'erreur "Can't resolve vendor/livewire/flux/dist/flux.css")
# ========================================================

# --- Etape 1 : installer les dépendances PHP avec Composer (crée vendor/) ---
FROM composer:2 AS composer_build
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# --- Etape 2 : compiler les assets (CSS/JS avec Tailwind/Flux) ---
# IMPORTANT : cette étape part du résultat de Composer, car le CSS de Flux UI
# (resources/css/app.css importe vendor/livewire/flux/dist/flux.css) a besoin
# que le dossier vendor/ existe déjà avant de lancer "npm run build".
FROM node:20-alpine AS node_build
WORKDIR /app
COPY --from=composer_build /app /app
RUN npm install
RUN npm run build

# --- Etape 3 : image finale qui fait tourner le site ---
# PHP 8.4 (et non 8.3) car tes dépendances Composer exigent PHP >= 8.4.1
FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev unzip git \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd exif mbstring \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copier le code + dépendances PHP installées à l'étape 1
COPY --from=composer_build /app /var/www/html
# Copier les assets compilés (CSS/JS) de l'étape 2 (écrase le dossier build
# non-compilé copié ci-dessus par la version finale compilée)
COPY --from=node_build /app/public/build /var/www/html/public/build

# Configuration Apache : pointer sur le dossier public/ de Laravel
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Script qui prépare Laravel au démarrage (migrations, cache, etc.)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
