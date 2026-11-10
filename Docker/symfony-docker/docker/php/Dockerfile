# =============================================================================
#  FrankenPHP: servidor de aplicaciones PHP escrito en Go sobre el servidor web
#  Caddy. Un solo contenedor sirve los ficheros estáticos y ejecuta PHP (sin
#  Apache ni Nginx ni PHP-FPM). Es la base de la plantilla Docker oficial de Symfony.
# =============================================================================
ARG PHP_VERSION=8.4
FROM dunglas/frankenphp:1-php${PHP_VERSION}

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip nano less postgresql-client \
 && rm -rf /var/lib/apt/lists/*

# install-php-extensions ya viene en la imagen de FrankenPHP
RUN install-php-extensions pdo_pgsql intl zip opcache apcu xdebug

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY php/symfony.ini php/xdebug.ini "$PHP_INI_DIR/conf.d/"
COPY caddy/Caddyfile /etc/frankenphp/Caddyfile

# Usuario sin privilegios con tu UID/GID (en Linux, los ficheros de var/ serán tuyos).
# setcap: permite a FrankenPHP escuchar en el puerto 80 sin ser root.
ARG UID=1000
ARG GID=1000
RUN groupadd -o -g ${GID} app && useradd -o -m -u ${UID} -g app app \
 && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
 && mkdir -p /app/var && chown -R app:app /config/caddy /data/caddy /app
USER app

ENV COMPOSER_HOME=/home/app/.composer
WORKDIR /app
