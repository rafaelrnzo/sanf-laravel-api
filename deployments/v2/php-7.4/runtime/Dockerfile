# Build runtime php base
FROM php:7.4-fpm

ENV PATH="./vendor/bin:${PATH}" \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS="1" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="6000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="128"

ENV BUILD_PACKAGES="libicu-dev libzip-dev libpng-dev libwebp-dev libgmp-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libpq-dev"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        unzip \
        procps \
        supervisor \
        nginx \
        $BUILD_PACKAGES

RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring opcache gd zip intl \
    && pecl install apcu \
    && docker-php-ext-enable apcu

ENV COMPOSER_HOME /composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH /composer/vendor/bin:$PATH
ENV PHP_CONF_DIR=/usr/local/etc/php/conf.d

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app
