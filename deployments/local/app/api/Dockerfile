FROM php:7.4-fpm

ENV PATH="./vendor/bin:${PATH}" \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS="1" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="6000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="128"

ENV BUILD_PACKAGES="libicu-dev libzip-dev libpng-dev libgmp-dev libjpeg62-turbo-dev libfreetype6-dev libonig-dev libpq-dev"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        unzip \
        supervisor \
        nginx \
        procps \
        $BUILD_PACKAGES

RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pgsql pdo_pgsql mbstring opcache gd zip intl \
    && pecl install apcu \
    && pecl install excimer \
    && docker-php-ext-enable apcu

ARG INSTALL_XDEBUG=true
RUN if [ ${INSTALL_XDEBUG} = true ]; then \
    pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug \
;fi

ARG INSTALL_GRPC=false
RUN if [ ${INSTALL_GRPC} = true ]; then \
    apt-get update \
    && apt-get install libstdc++-11-dev \
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
    && wget -O- https://download.newrelic.com/548C16BF.gpg | sudo apt-key add - \
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

RUN apt-get remove --purge -y ${BUILD_PACKAGES} \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/cache/apt/archives /var/lib/apt/lists

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY deployments/local/config/php-7.4/php-ext-custom.ini /usr/local/etc/php/conf.d/php.ini
COPY deployments/local/config/php-7.4/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY deployments/local/config/php-7.4/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY deployments/local/config/php-7.4/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY deployments/local/config/php-7.4/php-fpm-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY deployments/local/config/nginx/snippets/error-page.conf /etc/nginx/snippets/error-page.conf
COPY deployments/local/config/nginx/error-html /var/www/html/
COPY deployments/local/config/nginx/error-json /var/www/json/
COPY deployments/local/config/nginx/basic.conf /etc/nginx/h5bp/basic.conf
COPY deployments/local/config/nginx/nginx.conf /etc/nginx/sites-available/default

COPY deployments/local/config/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY deployments/local/config/supervisor/app.conf /etc/supervisor/conf.d-available/app.conf

COPY deployments/local/app/api/entrypoint.sh /usr/local/bin/entrypoint

WORKDIR /var/www/app

RUN mkdir /var/www/app/xdebug

RUN chown -R www-data:www-data /var/www/app

EXPOSE 80

ENTRYPOINT ["sh", "/usr/local/bin/entrypoint"]
