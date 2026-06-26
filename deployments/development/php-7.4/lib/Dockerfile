FROM php:8.3-cli-alpine

ENV COMPOSER_HOME /composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH /composer/vendor/bin:$PATH
ENV PHP_CONF_DIR=/usr/local/etc/php/conf.d

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "memory_limit=-1" > $PHP_CONF_DIR/99_memory-limit.ini \
    && apk add git \
    && rm -rf /var/cache/apk/* /var/tmp/* /tmp/*

COPY database /var/www/app/database
COPY packages/sanf/core/database /var/www/app/packages/sanf/core/database
COPY composer.json /var/www/app/composer.json
COPY composer.lock /var/www/app/composer.lock

WORKDIR /var/www/app

RUN composer install \
    --no-dev \
    --no-cache \
    --optimize-autoloader \
    --no-progress \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-req=ext-intl \
    --ignore-platform-req=ext-zip

RUN composer clear-cache
