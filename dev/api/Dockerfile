FROM php:8.3-fpm

LABEL Description="Laravel PHP 8.3"

ENV WORKDIR=/var/www

RUN apt-get update && apt-get install -y \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR $WORKDIR

COPY . $WORKDIR

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
        storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8032

CMD ["php", "-S", "0.0.0.0:8032", "-t", "public"]
