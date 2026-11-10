# Entorno de desarrollo de Laravel en Docker

Arquitectura de producción: **Nginx** + **PHP-FPM 8.4** (Xdebug, Composer) + **MariaDB 11.4** + **Redis**
(caché, sesiones, colas) + trabajador de colas + **Vite** (Node 22) + phpMyAdmin + Mailpit.
`src/` contiene un proyecto **Laravel 13** con el ejemplo de la carta del Bar Baio. Las explicaciones
completas están en los apuntes (`../Apuntes`).

> En Windows, para Laravel trabaja **dentro de WSL 2** (`\\wsl.localhost\Ubuntu\home\...`): con el código
> en `C:` o `D:` todo va decenas de veces más lento (ver apuntes, apartado 7.3).

## Puesta en marcha

```bash
cp .env.example .env                                # entorno: puertos y versiones
docker compose up -d --build
docker compose exec app bash /scripts/preparar.sh   # composer install, .env, clave, migraciones
docker compose logs -f vite                         # la primera vez hace npm install
```

> En **Git Bash** antepón `MSYS_NO_PATHCONV=1` a las órdenes con rutas como `/scripts/...`.

| URL | Qué es |
|---|---|
| http://localhost:8110 | la aplicación (Nginx) |
| http://localhost:8110/carta | ejemplo del Bar Baio (formulario, caché, correo en cola) |
| http://localhost:8110/api/carta | la carta en JSON |
| http://localhost:8111 | phpMyAdmin |
| http://localhost:8028 | Mailpit |

## Órdenes habituales

```bash
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app composer require paquete/nombre
docker compose exec vite npm install paquete
docker compose exec -e XDEBUG_MODE=off app php artisan test
docker compose restart queue          # tras cambiar correos o trabajos en cola
```

## Proyecto nuevo desde cero

Vacía `src/` y ejecuta `docker compose exec app bash /scripts/crear-proyecto.sh`.

## Estructura

```
compose.yaml                 app, web, queue, vite, db, redis, phpmyadmin, mailpit
docker/php/Dockerfile        php:8.4-fpm + extensiones + Composer + Xdebug
docker/nginx/default.conf    Nginx -> PHP-FPM (app:9000)
scripts/                     preparar.sh, crear-proyecto.sh, configurar-env.sh
src/                         proyecto Laravel (bind mount en /var/www/html)
.vscode/                     Xdebug (pathMappings /var/www/html -> src)
```
