FROM php:7.4-apache

# Define dependencies
ENV BUILD_PACKAGES="libpng-dev libwebp-dev libgmp-dev libjpeg62-turbo-dev libfreetype6-dev zlib1g-dev libzip-dev libicu-dev g++ libonig-dev" \
    DB_DRIVER_PACKAGE="libpq-dev" \
    DB_DRIVER="pdo_pgsql"
#    DB_DRIVER="pdo_mysql"

# Install required app
RUN apt-get update \
  && apt-get install -y git unzip ${BUILD_PACKAGES} ${DB_DRIVER_PACKAGE}

# Install extensions
RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg \
	&& docker-php-ext-configure intl \
    && docker-php-ext-install ${DB_DRIVER} mbstring opcache bcmath iconv exif zip gd pdo intl

# Enable mod_rewrite
RUN if command -v a2enmod >/dev/null 2>&1; then \
      a2enmod rewrite headers \
    ;fi

# Copy virtual host file
COPY 000-default.conf /etc/apache2/sites-available/
COPY php.ini /usr/local/etc/php/conf.d/

# Copy fix permission script
COPY setup-permission.sh /usr/local/bin/setup-permission
RUN chmod +x /usr/local/bin/setup-permission

# Clean up
RUN apt-get remove --purge -y ${BUILD_PACKAGES} \
	&& rm -rf /var/lib/apt/lists/*

# Set workdir
WORKDIR /var/app
