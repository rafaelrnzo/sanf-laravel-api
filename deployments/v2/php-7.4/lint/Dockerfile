FROM php:7.4-cli-alpine

RUN php --version

ENV COMPOSER_HOME /composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV PATH /composer/vendor/bin:$PATH
ENV PHP_CONF_DIR=/usr/local/etc/php/conf.d

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "memory_limit=-1" > $PHP_CONF_DIR/99_memory-limit.ini \
    && apk add git \
    && rm -rf /var/cache/apk/* /var/tmp/* /tmp/*

ARG PHPSTAN_VERSION="*"
ARG PHP_CS_FIXER_VERSION="*"
ARG PHP_MESS_DETECTOR="*"

COPY --chown=www-data:www-data . /var/www/app
COPY --from=cr.nbs.dev/sanf/core/vendor:latest /var/www/app/vendor /var/www/app/vendor

WORKDIR /var/www/app

RUN composer require phpstan/phpstan:"$PHPSTAN_VERSION" --prefer-dist --dev \
    && composer require friendsofphp/php-cs-fixer:"$PHP_CS_FIXER_VERSION" --prefer-dist --dev \
    && composer require phpmd/phpmd:"$PHP_MESS_DETECTOR" --prefer-dist --dev \
	&& composer clear-cache
