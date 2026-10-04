# ========================================================
# PHASE 39 - Dockerfile pour héberger Mosnoky gratuitement
# sur Render.com avec Supabase (PostgreSQL) comme base de données
# ========================================================

# --- Etape 1 : compiler les assets (CSS/JS avec Tailwind/Flux) ---
FROM node:20-alpine AS node_build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- Etape 2 : installer les dépendances PHP avec Composer ---
FROM composer:2 AS composer_build
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# --- Etape 3 : image finale qui fait tourner le site ---
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev unzip git \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd exif mbstring \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copier le code + dépendances PHP installées à l'étape 2
COPY --from=composer_build /app /var/www/html
# Copier les assets compilés (CSS/JS) de l'étape 1
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
