# =============================================================================
#  Apache + PHP con todo lo que pide Moodle 5 (y Xdebug para desarrollar)
# =============================================================================
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-apache

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip nano less default-mysql-client ghostscript \
 && rm -rf /var/lib/apt/lists/*

# Extensiones obligatorias y recomendadas por Moodle (Administración > Servidor > Entorno)
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions mysqli pgsql intl gd zip soap exif opcache sodium xsl redis apcu xdebug

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY php/moodle.ini php/xdebug.ini "$PHP_INI_DIR/conf.d/"
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Apache: DocumentRoot = carpeta public de Moodle (5.1 o posterior) y router (r.php)
ENV MOODLE_DOCROOT=/var/www/moodle/public
COPY apache/moodle.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite headers

# En Linux, www-data toma tu UID/GID (ficheros de plugins a tu nombre)
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data \
 && mkdir -p /var/www/moodledata /var/www/phpunitdata \
 && chown -R www-data:www-data /var/www/moodledata /var/www/phpunitdata

WORKDIR /var/www/moodle
