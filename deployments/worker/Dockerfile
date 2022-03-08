FROM php:7.4-alpine

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
  libpng-dev \
  jpeg-dev \
  freetype-dev \
  libjpeg-turbo-dev \
  libjpeg-turbo \
  libwebp-dev \
  unzip


# Install extensions
RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_pgsql opcache bcmath iconv exif zip gd pdo intl

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

COPY supervisord.conf /etc/supervisord.conf
COPY ./supervisord.conf /etc/supervisor/
COPY ./supervisord.d/* /etc/supervisor/conf.d/

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

RUN php -v | head -n 1 | grep -q "PHP 7.4"

# Clean up
RUN rm /var/cache/apk/* \
    && mkdir -p /var/app

WORKDIR /etc/supervisor/conf.d/
