# Etapa 1: compilar el frontend React
FROM node:22-alpine AS frontend

WORKDIR /app
COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

# Etapa 2: backend Laravel (API) - PHP 8.4
FROM php:8.4-cli

# Dependencias del sistema y extensiones de PHP
# (pdo_mysql para FreeDB/MySQL, gd para dompdf, zip para composer, intl para formatos de Laravel)
RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip bcmath intl \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instalar dependencias primero (aprovecha la caché de Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copiar el resto del proyecto
COPY . .

# Frontend compilado: index.html y assets quedan en public/ y los sirve Laravel
COPY --from=frontend /app/dist/ ./public/

# Carpetas que Laravel necesita escribir (se excluyen en .dockerignore)
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
        storage/logs bootstrap/cache \
    && composer dump-autoload --optimize \
    && chmod -R 775 storage bootstrap/cache

# Puerto (Render y otros servicios definen la variable PORT)
ENV PORT=8000
ENV PHP_CLI_SERVER_WORKERS=4
EXPOSE 8000

# Al arrancar: limpia caché, ejecuta migraciones y seeders (idempotentes),
# regenera la documentación Swagger con el APP_URL real y levanta el servidor
CMD php artisan config:clear \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan l5-swagger:generate \
    && php artisan serve --host=0.0.0.0 --port=${PORT}
