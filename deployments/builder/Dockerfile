FROM php:7.4-cli

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

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
	&& php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Clean up
RUN apt-get remove --purge -y ${BUILD_PACKAGES} \
	&& rm -rf /var/lib/apt/lists/*

# Set work dir
WORKDIR /var/app
