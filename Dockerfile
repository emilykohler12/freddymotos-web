# ---- Etapa 1: compilar CSS/JS (Tailwind + Vite) ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm run build

# ---- Etapa 2: la app en sí (PHP) ----
FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencias de PHP primero (aprovecha la cache de Docker si el código cambia pero composer.json no).
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x start.sh

EXPOSE 10000

# Corre las migraciones (Postgres en Render) y levanta el server de Laravel en el puerto que da Render.
# Si migrate falla, el contenedor corta acá y se ve el error real en los logs (antes quedaba tapado).
CMD ["./start.sh"]
