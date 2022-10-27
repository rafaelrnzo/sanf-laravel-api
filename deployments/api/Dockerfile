FROM alpine:3.13

LABEL Description="Forked from https://github.com/TrafeX/docker-php-nginx/tree/1.10.0"

ENV WORKDIR="/var/www"

# Install packages
RUN apk --no-cache add \
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
#    php7-pdo_mysql \
    php7-pdo_pgsql \
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
    curl

# Install packages
RUN apk --no-cache add php php-fpm php-opcache php-openssl php-curl \
    nginx supervisor curl

# Configure nginx
COPY ./deployments/api/nginx.conf /etc/nginx/nginx.conf

# Remove default server definition
RUN rm /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY ./deployments/api/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY ./deployments/api/php.ini /etc/php7/conf.d/custom.ini

# Configure supervisord
COPY ./deployments/api/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Setup document root
RUN mkdir -p $WORKDIR

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody $WORKDIR && \
  chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

# Switch to use a non-root user from here on
USER nobody

# Add application
WORKDIR $WORKDIR
COPY --chown=nobody . $WORKDIR

# Install Dependency
RUN composer install --no-cache --no-dev --optimize-autoloader --prefer-dist --no-interaction --no-progress

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
