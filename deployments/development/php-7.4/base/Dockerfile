FROM php:8.3-fpm

ENV PATH="./vendor/bin:${PATH}" \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS="1" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="6000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="128"

ENV BUILD_PACKAGES="libicu-dev libzip-dev libpng-dev libgmp-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libpq-dev"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        unzip \
        procps \
        supervisor \
        nginx \
        procps \
        $PHPIZE_DEPS \
        $BUILD_PACKAGES

RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pgsql pdo_pgsql mbstring opcache gd zip intl \
    && pecl install apcu \
    && pecl install excimer \
    && docker-php-ext-enable apcu excimer

ARG INSTALL_GRPC=false
RUN if [ ${INSTALL_GRPC} = true ]; then \
    apt-get update \
    && apt-get install -y libstdc++-12-dev \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && pecl install protobuf \
    && docker-php-ext-enable protobuf \
;fi

ARG INSTALL_NEW_RELIC=false
ARG NEW_RELIC_KEY
RUN if [ ${INSTALL_NEW_RELIC} = true ]; then \
    echo 'deb http://apt.newrelic.com/debian/ newrelic non-free' | tee /etc/apt/sources.list.d/newrelic.list \
    && apt-get install -y wget gnupg \
    && wget -O- https://download.newrelic.com/548C16BF.gpg | apt-key add - \
    && apt update \
    && apt-get -y install newrelic-php5 \
    && NR_INSTALL_SILENT=1 newrelic-install install \
    && sed -i -e "s/REPLACE_WITH_REAL_KEY/${NEW_RELIC_KEY}/" \
         -e "s/newrelic.appname[[:space:]]=[[:space:]].*/newrelic.appname=\"cms\"/" \
         $(php -r "echo(PHP_CONFIG_FILE_SCAN_DIR);")/newrelic.ini \
;fi

ARG INSTALL_DATADOG=false
RUN if [ ${INSTALL_DATADOG} = true ]; then \
    curl -LO https://github.com/DataDog/dd-trace-php/releases/latest/download/datadog-setup.php \
    && php datadog-setup.php --php-bin=all --enable-profiling \
;fi

RUN apt-get remove --purge -y ${PHPIZE_DEPS} ${BUILD_PACKAGES} \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apt/archives /var/lib/apt/lists /usr/lib/gcc

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app
