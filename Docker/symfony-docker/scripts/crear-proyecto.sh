#!/usr/bin/env bash
# =============================================================================
#  Crea un proyecto Symfony NUEVO en src/ (si quieres empezar de cero en lugar
#  de usar el ejemplo del Bar Baio). Se ejecuta DENTRO del contenedor:
#
#    docker compose exec php bash /scripts/crear-proyecto.sh          # última versión
#    docker compose exec php bash /scripts/crear-proyecto.sh 7.4      # Symfony 7.4 LTS
#
#  src/ tiene que estar vacía.
# =============================================================================
set -euo pipefail
cd /app
VERSION="${1:-}"

if [ -n "$(ls -A . 2>/dev/null)" ]; then
    echo "src/ no está vacía. Vacíala antes (o usa scripts/preparar.sh con el ejemplo)." >&2
    exit 1
fi

# Se crea en /tmp (disco del contenedor, mucho más rápido) y después se copia a src/
rm -rf /tmp/nuevo
composer create-project "symfony/skeleton${VERSION:+:$VERSION.*}" /tmp/nuevo --no-interaction
cd /tmp/nuevo

# Las recetas de Symfony Flex añaden su propio compose.yaml al instalar Doctrine o Mailer:
# en este entorno ya tenemos uno, así que se desactiva
composer config --json extra.symfony.docker false

# "webapp": Twig, Doctrine ORM, formularios, validación, seguridad, Mailer, Messenger,
# AssetMapper, la barra de depuración (profiler), MakerBundle y PHPUnit
composer require webapp --no-interaction

# (cp sin -a: en una carpeta montada desde Windows no se pueden copiar fechas ni propietarios)
cp -r /tmp/nuevo/. /app/
rm -rf /tmp/nuevo
cd /app

php bin/console doctrine:database:create --if-not-exists
php bin/console messenger:setup-transports
echo
echo "Proyecto creado: http://localhost:8120"
