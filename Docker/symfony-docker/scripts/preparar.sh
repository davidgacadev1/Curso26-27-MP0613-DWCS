#!/usr/bin/env bash
# =============================================================================
#  Deja listo el proyecto Symfony de src/ (el ejemplo o uno clonado de Git).
#  Se ejecuta DENTRO del contenedor:
#
#    docker compose exec php bash /scripts/preparar.sh
#
#  Dependencias, base de datos, migraciones, datos de ejemplo y la base de datos
#  de las pruebas. Se puede repetir sin problema.
# =============================================================================
set -euo pipefail
cd /app

if [ ! -f bin/console ]; then
    echo "src/ no contiene un proyecto Symfony (usa scripts/crear-proyecto.sh)." >&2
    exit 1
fi

composer install --no-interaction --no-progress

# Base de datos de desarrollo: tablas (migraciones) y carta de ejemplo (fixtures)
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
# Tabla messenger_messages de la cola de Messenger (transporte doctrine con auto_setup=0)
php bin/console messenger:setup-transports
if [ -d src/DataFixtures ]; then
    php bin/console doctrine:fixtures:load --no-interaction
fi

# Base de datos de las pruebas (app_test): mismas tablas, sin datos
php bin/console --env=test doctrine:database:create --if-not-exists
php bin/console --env=test doctrine:migrations:migrate --no-interaction --allow-no-migration
php bin/console --env=test messenger:setup-transports

php bin/console cache:clear
echo
echo "Listo: http://localhost:8120/carta"
