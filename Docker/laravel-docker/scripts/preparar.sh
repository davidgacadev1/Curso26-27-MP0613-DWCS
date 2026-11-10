#!/usr/bin/env bash
# =============================================================================
#  Deja listo el proyecto Laravel de src/ (el ejemplo o uno clonado de Git).
#  Se ejecuta DENTRO del contenedor:
#
#    docker compose exec app bash /scripts/preparar.sh
#
#  Es lo mismo que haces al clonar cualquier proyecto Laravel: dependencias,
#  .env, clave, tablas y datos de ejemplo. Se puede repetir sin problema.
# =============================================================================
set -euo pipefail
cd /var/www/html

if [ ! -f artisan ]; then
    echo "src/ no contiene un proyecto Laravel (usa scripts/crear-proyecto.sh)." >&2
    exit 1
fi

composer install --no-interaction --no-progress

if [ ! -f .env ]; then
    cp .env.example .env
    bash /scripts/configurar-env.sh .env
fi
grep -q '^APP_KEY=base64:' .env || php artisan key:generate

# Tablas y datos de ejemplo (--force: no preguntar)
php artisan migrate --seed --force
php artisan optimize:clear

echo
echo "Listo: http://localhost:8110/carta"
echo "(el servicio vite instala sus dependencias y arranca solo: docker compose logs -f vite)"
