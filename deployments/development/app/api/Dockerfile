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

COPY deployments/development/config/nginx/snippets/error-page.conf /etc/nginx/snippets/error-page.conf
COPY deployments/development/config/nginx/error-html /var/www/html/
COPY deployments/development/config/nginx/error-json /var/www/json/
COPY deployments/development/config/nginx/basic.conf /etc/nginx/h5bp/basic.conf
COPY deployments/development/config/nginx/nginx.conf /etc/nginx/sites-available/default

COPY deployments/development/config/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY deployments/development/config/supervisor/app.conf /etc/supervisor/conf.d-available/app.conf

COPY deployments/development/app/api/entrypoint.sh /usr/local/bin/entrypoint

EXPOSE 80

ENTRYPOINT ["bash", "/usr/local/bin/entrypoint"]
