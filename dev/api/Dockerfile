FROM alpine:3.18

LABEL Description="PHP-FPM + Nginx optimized"

ENV WORKDIR="/var/www"

# Install packages (PHP 8 lebih aman sekarang)
RUN apk add --no-cache \
    php82 \
    php82-fpm \
    php82-opcache \
    php82-phar \
    php82-openssl \
    php82-gd \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-iconv \
    php82-intl \
    php82-mbstring \
    php82-pdo \
    php82-pdo_mysql \
    php82-session \
    php82-simplexml \
    php82-tokenizer \
    php82-fileinfo \
    php82-xml \
    php82-xmlreader \
    php82-xmlwriter \
    php82-zip \
    php82-zlib \
    php82-sodium \
    php82-posix \
    php82-pcntl \
    nginx \
    supervisor \
    curl \
    bash

# Symlink biar php gampang dipanggil
RUN ln -s /usr/bin/php82 /usr/bin/php

# Configure nginx
COPY ./deployments/api/nginx.conf /etc/nginx/nginx.conf
RUN rm -f /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY ./deployments/api/fpm-pool.conf /etc/php82/php-fpm.d/www.conf
COPY ./deployments/api/php.ini /etc/php82/conf.d/custom.ini

# Configure supervisord
COPY ./deployments/api/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup workdir
WORKDIR $WORKDIR
RUN mkdir -p $WORKDIR

# Copy app
COPY . $WORKDIR

# Install dependency (RUN as root dulu biar aman)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permission
RUN chown -R nobody:nobody $WORKDIR \
    /run \
    /var/lib/nginx \
    /var/log/nginx

USER nobody

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]