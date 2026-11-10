# Entorno base DWCS en Docker (tipo XAMPP)

Apache 2.4 + PHP 8.2 (Xdebug 3, Composer) + MariaDB 11.4 + phpMyAdmin + Mailpit.
Las explicaciones completas están en los apuntes (`../Apuntes`).

## Puesta en marcha

```bash
cp .env.example .env          # solo si no existe .env (en PowerShell: copy .env.example .env)
docker compose up -d --build  # la primera vez tarda unos minutos (construye la imagen)
```

| URL | Qué es | Carpeta |
|---|---|---|
| http://localhost:8080 | panel del entorno (comprobaciones) | `www/localhost` |
| http://dwcs.localhost:8080 | tu código | `www/dwcs` |
| http://dwcs-profe.localhost:8080 | repositorio del profesor | `www/dwcs-profe` |
| http://CARPETA.localhost:8080 | cualquier otra carpeta de `www/` | `www/CARPETA` |
| http://localhost:8081 | phpMyAdmin (entra como root) | |
| http://localhost:8025 | Mailpit: correos enviados con `mail()` | |

Base de datos: servidor **`db`** desde PHP (no `localhost`), puerto 3306, base `dwcs`, usuario `dwcs` / `dwcs`
(root / `root`). Desde tu equipo: `127.0.0.1:3308`.

## Depurar con VS Code

1. Abre esta carpeta (o `dwcs.code-workspace`) e instala las extensiones recomendadas.
2. Pon un punto de ruptura, pulsa F5 → **Xdebug en Docker** y recarga la página.
3. Consola: `docker compose exec web php dwcs/consola.php` (también se para en los puntos de ruptura).

Para desactivar Xdebug: `XDEBUG_MODE=off` en `.env` y `docker compose up -d`.

## Órdenes útiles

```bash
docker compose ps                     # estado de los servicios
docker compose logs -f web            # log de Apache y errores de PHP
docker compose exec web bash          # consola dentro del contenedor (composer, php...)
docker compose exec db mariadb -u root -proot dwcs     # cliente de MariaDB
docker compose stop                   # parar (no borra nada)
docker compose down                   # borrar contenedores (los datos de la BD se conservan)
docker compose down -v                # borrar también la BD (vuelve a ejecutar docker/mariadb/init)
```

## Estructura

```
compose.yaml             servicios web, db, phpmyadmin, mailpit
.env(.example)           versiones, puertos y contraseñas
docker/php/Dockerfile    imagen php:8.2-apache + extensiones + Xdebug + Composer
docker/php/*.ini         ajustes de PHP y Xdebug
docker/apache/sitios.conf  sitios virtuales *.localhost
docker/mariadb/          configuración y scripts SQL iniciales (carta del Bar Baio)
www/                     TU CÓDIGO (bind mount en /var/www)
.vscode/                 launch.json (Xdebug), settings.json, extensions.json
```
