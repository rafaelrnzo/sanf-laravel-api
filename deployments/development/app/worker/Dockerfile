ARG CI_REGISTRY_IMAGE

FROM ${CI_REGISTRY_IMAGE}/lib:latest as lib
FROM ${CI_REGISTRY_IMAGE}/base:latest as app

COPY --from=lib /var/www/app/vendor /var/www/app/vendor
COPY --chown=www-data:www-data . /var/www/app

WORKDIR /var/www/app

FROM ${CI_REGISTRY_IMAGE}/base:latest as runtime

COPY --from=app /var/www/app /var/www/app

COPY deployments/development/config/php-7.4/php-ext-custom.ini /usr/local/etc/php/conf.d/php.ini
COPY deployments/development/config/php-7.4/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY deployments/development/config/php-7.4/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY deployments/development/config/php-7.4/php-fpm-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY deployments/development/config/supervisor/supervisord-worker.conf /etc/supervisor/supervisord.conf
COPY deployments/development/config/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

WORKDIR /var/www/app

RUN chown -R www-data:www-data /var/www/app

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
