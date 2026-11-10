# Entorno de desarrollo de Symfony en Docker

**FrankenPHP** (servidor web Caddy + PHP 8.4 en un solo contenedor, con Xdebug y Composer) +
**PostgreSQL 17** + Adminer + Mailpit + trabajador de **Symfony Messenger**. `src/` contiene un proyecto
**Symfony 8.1** (paquete `webapp`) con el ejemplo de la carta del Bar Baio. Las explicaciones completas
están en los apuntes (`../Apuntes`).

> En Windows, trabaja **dentro de WSL 2** (`\\wsl.localhost\Ubuntu\home\...`): en modo `dev` Symfony
> comprueba miles de ficheros en cada petición y con el código en `C:` o `D:` cada página tarda varios
> segundos (medido: unos 7 s frente a 0,25 s).

## Puesta en marcha

```bash
cp .env.example .env                                # entorno: puertos y versiones
docker compose up -d --build
docker compose exec php bash /scripts/preparar.sh   # composer install, BD, migraciones, datos, BD de pruebas
```

> En **Git Bash** antepón `MSYS_NO_PATHCONV=1` a las órdenes con rutas como `/scripts/...`.

| URL | Qué es |
|---|---|
| http://localhost:8120 | la aplicación (FrankenPHP) |
| http://localhost:8120/carta | ejemplo del Bar Baio (formulario, validación, correo por Messenger) |
| http://localhost:8120/api/carta | la carta en JSON |
| http://localhost:8120/_profiler | perfilador de Symfony |
| http://localhost:8121 | Adminer (sistema PostgreSQL, servidor `database`, usuario y clave `app`) |
| http://localhost:8029 | Mailpit |

## Órdenes habituales

```bash
docker compose exec php bin/console debug:router
docker compose exec php bin/console make:entity
docker compose exec php bin/console make:migration
docker compose exec php bin/console doctrine:migrations:migrate
docker compose exec php composer require paquete/nombre
docker compose exec php bin/console importmap:require bootstrap     # JS/CSS sin Node (AssetMapper)
docker compose exec -e XDEBUG_MODE=off php bin/phpunit
docker compose exec php tail -f var/log/dev.log                     # var/ está en un volumen de Docker
docker compose restart worker                                       # tras cambiar mensajes o manejadores
```

## Proyecto nuevo desde cero

Vacía `src/` y ejecuta `docker compose exec php bash /scripts/crear-proyecto.sh` (última versión) o
`... crear-proyecto.sh 7.4` (Symfony 7.4 LTS).

## Estructura

```
compose.yaml                 php (FrankenPHP), worker, database, adminer, mailpit
docker/php/Dockerfile        dunglas/frankenphp + extensiones + Composer + Xdebug
docker/caddy/Caddyfile       Caddy: public/ como raíz y php_server
scripts/                     preparar.sh, crear-proyecto.sh
src/                         proyecto Symfony (bind mount en /app; var/ en un volumen)
.vscode/                     Xdebug (pathMappings /app -> src)
```
