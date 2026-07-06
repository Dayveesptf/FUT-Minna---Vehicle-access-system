# Stage 1: Composer
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize


# Stage 2: Node
FROM node:20 AS node

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# Stage 3: Production
FROM dunglas/frankenphp:php8.2

RUN install-php-extensions \
    gd \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    curl \
    dom

WORKDIR /app

COPY --from=composer /app /app
COPY --from=node /app/public/build /app/public/build

EXPOSE 8000

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
