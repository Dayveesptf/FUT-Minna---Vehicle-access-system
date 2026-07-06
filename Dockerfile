# -----------------------------
# Stage 1: Build Frontend Assets
# -----------------------------
FROM node:20 AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# -----------------------------
# Stage 2: PHP Runtime
# -----------------------------
FROM dunglas/frankenphp:php8.2

# Install required PHP extensions
RUN install-php-extensions \
    gd \
    pdo_mysql \
    mbstring \
    xml \
    curl \
    zip \
    dom \
    fileinfo \
    bcmath \
    exif \
    intl \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --ignore-platform-req=ext-gd

# Copy built frontend assets
COPY --from=frontend /app/public/build ./public/build

# Optimize Laravel
RUN php artisan config:clear || true
RUN php artisan route:clear || true
RUN php artisan view:clear || true

EXPOSE 8000

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
