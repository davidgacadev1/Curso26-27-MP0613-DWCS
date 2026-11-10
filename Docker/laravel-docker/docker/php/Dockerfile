# =============================================================================
#  PHP-FPM para Laravel: PHP se ejecuta aquí y Nginx (otro contenedor) le pasa
#  las peticiones por FastCGI (puerto 9000).
# =============================================================================
ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-fpm

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip nano less default-mysql-client \
 && rm -rf /var/lib/apt/lists/*

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip gd bcmath exif pcntl opcache redis xdebug

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY php/laravel.ini php/xdebug.ini "$PHP_INI_DIR/conf.d/"

# En Linux, www-data (el usuario de PHP-FPM) toma tu UID/GID
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data \
 && mkdir -p /var/www/.composer && chown -R www-data:www-data /var/www

ENV COMPOSER_HOME=/var/www/.composer
WORKDIR /var/www/html
