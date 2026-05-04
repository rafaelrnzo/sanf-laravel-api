FROM php:7.4-fpm

LABEL Description="Stable PHP 7.4 + Nginx + Supervisor"

ENV WORKDIR=/var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Nginx
COPY ./deployments/api/nginx.conf /etc/nginx/nginx.conf
RUN rm -f /etc/nginx/sites-enabled/default

# Configure Supervisor
COPY ./deployments/api/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR $WORKDIR

# Copy app
COPY . $WORKDIR

# Install dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# Laravel permission fix
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start services
CMD ["/usr/bin/supervisord"]