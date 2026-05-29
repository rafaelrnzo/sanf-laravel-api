FROM alpine:3.20

LABEL Description="Forked from https://github.com/TrafeX/docker-php-nginx/tree/1.10.0"

ENV WORKDIR="/var/www"

# Install packages
RUN apk --no-cache add \
    php83 \
    php83-fpm \
    php83-opcache \
    php83-phar \
    php83-openssl \
    php83-gd \
    php83-ctype \
    php83-curl \
    php83-dom \
    php83-iconv \
    php83-intl \
    php83-mbstring \
#    php83-pdo_mysql \
    php83-pdo_pgsql \
    php83-pgsql \
    php83-session \
    php83-simplexml \
    php83-tokenizer \
    php83-fileinfo \
    php83-xml \
    php83-xmlreader \
    php83-xmlwriter \
    php83-zip \
    php83-zlib \
    php83-sodium \
    php83-posix \
    php83-pcntl \
    php83-bcmath \
    nginx \
    supervisor \
    curl

# Symlink php83 binaries to the generic names the rest of the image expects
RUN ln -sf /usr/bin/php83 /usr/bin/php \
    && ln -sf /usr/sbin/php-fpm83 /usr/sbin/php-fpm

# Configure nginx
COPY ./deployments/api/nginx.conf /etc/nginx/nginx.conf

# Remove default server definition
RUN rm -f /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY ./deployments/api/fpm-pool.conf /etc/php83/php-fpm.d/www.conf
COPY ./deployments/api/php.ini /etc/php83/conf.d/custom.ini

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
