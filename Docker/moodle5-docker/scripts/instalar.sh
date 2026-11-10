#!/usr/bin/env bash
# =============================================================================
#  Instala Moodle desde la línea de órdenes. Se ejecuta DENTRO del contenedor:
#
#    docker compose exec -u www-data web bash /scripts/instalar.sh
#
#  Se puede repetir: si Moodle ya está instalado, solo actualiza (upgrade.php),
#  que es lo que hay que hacer cada vez que subes la versión de un plugin.
# =============================================================================
set -euo pipefail
cd /var/www/moodle

if [ ! -f public/version.php ] && [ ! -f version.php ]; then
    echo "No encuentro el código de Moodle en ./moodle. Ejecuta antes scripts/obtener-moodle." >&2
    exit 1
fi

# 1) Librerías de terceros: desde Moodle 5.1 el repositorio Git no trae vendor/
if [ -f composer.json ] && [ ! -f vendor/autoload.php ]; then
    echo "== composer install (unos minutos la primera vez) =="
    composer install --no-dev --classmap-authoritative --no-interaction --no-progress
fi

# 2) Instalar la base de datos o actualizar
if php admin/cli/cfg.php --name=version >/dev/null 2>&1; then
    echo "== Moodle ya estaba instalado: actualizando (plugins nuevos o con versión nueva) =="
    php admin/cli/upgrade.php --non-interactive
else
    echo "== Instalando Moodle (crea unas 500 tablas: paciencia) =="
    php admin/cli/install_database.php \
        --agree-license \
        --lang=en \
        --adminuser=admin \
        --adminpass="${MOODLE_ADMIN_PASSWORD}" \
        --adminemail=admin@bkprogramacion.test \
        --fullname="Campus do Bar Baio" \
        --shortname="Baio" \
        --summary="Moodle de desarrollo de BK Programación"
fi

# 3) Ajustes de desarrollo que se guardan en la base de datos
php admin/cli/cfg.php --name=timezone --set=Europe/Madrid
php admin/cli/cfg.php --name=country --set=ES
php admin/cli/cfg.php --name=theme --set=baio
php admin/cli/cfg.php --name=cronclionly --set=1
# Depuración de desarrollador (32767 = E_ALL = DEBUG_DEVELOPER) y mostrar los avisos.
# Se guarda en la BD y no en config.php: ver la nota de config/config.php.
php admin/cli/cfg.php --name=debug --set=32767
php admin/cli/cfg.php --name=debugdisplay --set=1

# 4) Paquete de idioma español (necesita Internet; si falla, se instala desde el panel:
#    Administración del sitio > General > Idioma > Paquetes de idioma)
php -r 'define("CLI_SCRIPT", true); require "config.php";
        $c = new \tool_langimport\controller();
        $c->install_languagepacks("es");
        echo implode(PHP_EOL, $c->info), PHP_EOL;' \
    || echo "AVISO: no se pudo instalar el idioma español"

php admin/cli/purge_caches.php
echo
echo "Listo: ${MOODLE_URL}   (admin / ${MOODLE_ADMIN_PASSWORD})"
