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

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan serve --host=0.0.0.0 --port=$PORT
