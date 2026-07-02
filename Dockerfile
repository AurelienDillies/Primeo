# =========================
# Backend Symfony + PHP
# =========================
FROM php:8.4-fpm AS symfony

# Dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    npm \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql \
    opcache \
    zip

# Installation Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail Symfony
WORKDIR /var/www/backend

# Copie des fichiers composer
COPY backend/composer.json backend/composer.lock ./

# Installation dépendances PHP
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copie du backend
COPY backend/ .

# Permissions Symfony
RUN mkdir -p var/cache var/log

# =========================
# Frontend Angular build
# =========================
FROM node:20 AS angular-build

WORKDIR /app

# Copie package Angular
COPY frontend/package*.json ./

RUN npm install

# Copie du frontend
COPY frontend/ .

# Build Angular
RUN npm run build -- --configuration production

# =========================
# Nginx final
# =========================
FROM nginx:stable-alpine

# Config nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Symfony backend
COPY --from=symfony /var/www/backend /var/www/backend

# Copie frontend Angular compilé
COPY --from=angular-build /app/dist/frontend /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]