FROM php:7.4-fpm

ENV WORKDIR=/var/www

RUN apt-get update && apt-get install -y \
    curl zip unzip git \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install \
        pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR $WORKDIR

COPY . $WORKDIR

RUN cp .env.example .env 2>/dev/null || true

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
        storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

RUN php artisan key:generate --force

EXPOSE 8032

USER www-data

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8032"]