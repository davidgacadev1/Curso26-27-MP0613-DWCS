#!/usr/bin/env bash
# =============================================================================
#  Crea un proyecto Laravel NUEVO y vacío en src/ (si quieres empezar de cero
#  en lugar de usar el ejemplo del Bar Baio). Se ejecuta DENTRO del contenedor:
#
#    docker compose exec app bash /scripts/crear-proyecto.sh
#
#  src/ tiene que estar vacía (borra antes su contenido si es el ejemplo).
# =============================================================================
set -euo pipefail
cd /var/www/html

if [ -n "$(ls -A . 2>/dev/null)" ]; then
    echo "src/ no está vacía. Vacíala antes (o usa scripts/preparar.sh con el ejemplo)." >&2
    exit 1
fi

# composer create-project exige una carpeta vacía y no la crea dentro de un bind mount
# con los permisos adecuados en todos los sistemas: se crea en /tmp y se copia.
composer create-project laravel/laravel /tmp/nuevo --no-interaction --prefer-dist
# (cp sin -a: en una carpeta montada desde Windows no se pueden copiar fechas ni propietarios)
cp -r /tmp/nuevo/. /var/www/html/
rm -rf /tmp/nuevo

# Ajustar .env a los servicios de compose.yaml (db, redis, mailpit)
bash /scripts/configurar-env.sh .env
# create-project deja una base de datos SQLite de prueba que ya no se usa
rm -f database/database.sqlite

php artisan key:generate
php artisan migrate --force
echo
echo "Proyecto creado: http://localhost:8110"
