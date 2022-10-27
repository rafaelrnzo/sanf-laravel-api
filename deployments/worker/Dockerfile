FROM php:7.4-alpine

ENV WORKDIR="/var/www"

RUN apk --update add wget \
  curl \
  build-base \
  libmcrypt-dev \
  libxml2-dev \
  zlib-dev \
  autoconf \
  cyrus-sasl-dev \
  libgsasl-dev \
  openssl \
  openssl-dev \
  supervisor \
  postgresql-dev \
  libzip-dev \
  unzip


# Install extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_pgsql opcache bcmath exif zip pdo intl

# Install mbstring extension
RUN apk add --no-cache \
    oniguruma-dev \
    && docker-php-ext-install mbstring \
    && docker-php-ext-enable mbstring \
    && rm -rf /tmp/*

# Install redis
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

# Add a non-root user:
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN addgroup -g ${PGID} deployer && \
  adduser -D -G deployer -u ${PUID} deployer

COPY ./deployments/worker/supervisord.conf /etc/supervisord.conf
COPY ./deployments/worker/supervisord.conf /etc/supervisor/
COPY ./deployments/worker/supervisord.d/* /etc/supervisor/conf.d/

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

RUN php -v | head -n 1 | grep -q "PHP 7.4"

# Clean up
RUN rm /var/cache/apk/* \
    && mkdir -p /var/www

WORKDIR $WORKDIR

COPY --chown=deployer:www-data . $WORKDIR

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer install --no-cache --no-dev --optimize-autoloader --prefer-dist --no-interaction --no-progress
