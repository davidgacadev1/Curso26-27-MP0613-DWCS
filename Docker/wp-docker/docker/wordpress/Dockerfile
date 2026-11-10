# =============================================================================
#  Imagen oficial de WordPress + herramientas de desarrollo
# =============================================================================
FROM wordpress:php8.3-apache

# Xdebug (install-php-extensions resuelve las dependencias)
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug

# WP-CLI (el programa "wp" de la imagen oficial wordpress:cli), Composer y Mailpit
COPY --from=wordpress:cli /usr/local/bin/wp /usr/local/bin/wp
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY --from=axllent/mailpit:latest /mailpit /usr/local/bin/mailpit

RUN apt-get update \
 && apt-get install -y --no-install-recommends less nano unzip git default-mysql-client \
 && rm -rf /var/lib/apt/lists/*

COPY desarrollo.ini xdebug.ini "$PHP_INI_DIR/conf.d/"

# En Linux, www-data toma tu UID/GID para que los ficheros de wp-content sean tuyos
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g www-data www-data \
 && mkdir -p /var/www/.wp-cli && chown -R www-data:www-data /var/www/.wp-cli
