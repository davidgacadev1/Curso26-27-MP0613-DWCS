# =============================================================================
#  Imagen Apache + PHP para desarrollo (equivalente al Apache + PHP de XAMPP)
# =============================================================================
ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-apache

# Herramientas que usan Composer y la consola
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip nano less default-mysql-client \
 && rm -rf /var/lib/apt/lists/*

# Extensiones de PHP. install-php-extensions instala también las librerías del sistema
# que necesita cada una (https://github.com/mlocati/docker-php-extension-installer)
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql mysqli intl gd zip bcmath exif opcache xdebug

# Composer y el cliente "sendmail" de Mailpit (para que mail() funcione)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=axllent/mailpit:latest /mailpit /usr/local/bin/mailpit

# php.ini de desarrollo (muestra errores) + nuestros ajustes
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY php/dwcs.ini php/xdebug.ini "$PHP_INI_DIR/conf.d/"

# Apache: módulos y sitios virtuales (*.localhost)
RUN a2enmod rewrite headers vhost_alias
COPY apache/sitios.conf /etc/apache2/sites-available/000-default.conf

# En Linux, el usuario de Apache (www-data) toma el UID/GID de tu usuario
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data

WORKDIR /var/www
