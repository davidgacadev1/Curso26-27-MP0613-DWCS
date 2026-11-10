#!/usr/bin/env bash
# =============================================================================
#  Cambia en el .env de Laravel los valores que dependen de Docker:
#  la base de datos es el servicio "db", Redis es "redis" y el correo va a "mailpit".
#
#    bash /scripts/configurar-env.sh .env
# =============================================================================
set -euo pipefail
FICHERO="${1:-.env}"

poner() {   # poner CLAVE valor  -> sustituye la línea (aunque esté comentada) o la añade
    if grep -qE "^#? *$1=" "$FICHERO"; then
        sed -i -E "s|^#? *$1=.*|$1=$2|" "$FICHERO"
    else
        echo "$1=$2" >> "$FICHERO"
    fi
}

poner APP_NAME '"Bar Baio"'
poner APP_URL http://localhost:8110
poner APP_LOCALE es
poner APP_FAKER_LOCALE es_ES

poner DB_CONNECTION mariadb
poner DB_HOST db
poner DB_PORT 3306
poner DB_DATABASE laravel
poner DB_USERNAME laravel
poner DB_PASSWORD laravel

poner REDIS_CLIENT phpredis
poner REDIS_HOST redis
poner CACHE_STORE redis
poner SESSION_DRIVER redis
poner QUEUE_CONNECTION redis

poner MAIL_MAILER smtp
poner MAIL_HOST mailpit
poner MAIL_PORT 1025
poner MAIL_FROM_ADDRESS '"cocina@barbaio.test"'

echo "$FICHERO configurado para Docker"
