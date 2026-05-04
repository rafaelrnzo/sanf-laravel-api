FROM alpine:3.16

LABEL Description="PHP 7.4 + Nginx + Supervisor (Fixed for Laravel / API)"

ENV WORKDIR="/var/www"

# Install PHP 7.4 + extensions
RUN apk add --no-cache \
    php7 \
    php7-fpm \
    php7-opcache \
    php7-phar \
    php7-openssl \
    php7-gd \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-iconv \
    php7-intl \
    php7-json \
    php7-mbstring \
    php7-pdo \
    php7-pdo_mysql \
    php7-session \
    php7-simplexml \
    php7-tokenizer \
    php7-fileinfo \
    php7-xml \
    php7-xmlreader \
    php7-xmlwriter \
    php7-zip \
    php7-zlib \
    php7-sodium \
    php7-posix \
    php7-pcntl \
    nginx \
    supervisor \
    curl \
    bash

# Biar command "php" langsung jalan
RUN ln -s /usr/bin/php7 /usr/bin/php

# Configure nginx
COPY ./deployments/api/nginx.conf /etc/nginx/nginx.conf
RUN rm -f /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY ./deployments/api/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY ./deployments/api/php.ini /etc/php7/conf.d/custom.ini

# Configure supervisord
COPY ./deployments/api/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Setup working directory
WORKDIR $WORKDIR
RUN mkdir -p $WORKDIR

# Copy project
COPY . $WORKDIR

# Install dependencies (tanpa dev)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# Permission fix (penting buat Laravel / storage)
RUN chown -R nobody:nobody $WORKDIR \
    /run \
    /var/lib/nginx \
    /var/log/nginx

# Switch user
USER nobody

# Expose port nginx
EXPOSE 8080

# Run services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]