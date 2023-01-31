FROM cr.nbs.dev/sanf/core/runtime:latest as app

COPY --chown=www-data:www-data . /var/www/app

WORKDIR /var/www/app

RUN chgrp -R www-data storage \
    && chmod -R ug+rwx storage

FROM cr.nbs.dev/sanf/core/runtime:latest as runtime

COPY --from=cr.nbs.dev/sanf/core/vendor:latest /var/www/app/vendor /var/www/app/vendor
COPY --from=app /var/www/app /var/www/app

COPY deployments/v2/config/php/php-ext-custom.ini /usr/local/etc/php/conf.d/php.ini
COPY deployments/v2/config/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY deployments/v2/config/php/fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY deployments/v2/config/php/php-fpm-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY deployments/v2/config/nginx/basic.conf /etc/nginx/h5bp/basic.conf
COPY deployments/v2/config/nginx/custom.conf /etc/nginx/conf.d/custom.conf
COPY deployments/v2/config/nginx/nginx.conf /etc/nginx/sites-available/default
COPY deployments/v2/config/nginx/snippets/error-page.conf /etc/nginx/snippets/error-page.conf
COPY deployments/v2/config/nginx/error-pages/error-page.html /var/www/html/error-page.htmlv2/

COPY deployments/v2/config/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY deployments/v2/config/supervisor/app.conf /etc/supervisor/conf.d-available/app.conf
COPY deployments/v2/app/worker/supervisord.d/* /etc/supervisor/conf.d/

COPY deployments/v2/app/worker/entrypoint.sh /usr/local/bin/entrypoint

WORKDIR /var/www/app

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENTRYPOINT ["sh", "/usr/local/bin/entrypoint"]
