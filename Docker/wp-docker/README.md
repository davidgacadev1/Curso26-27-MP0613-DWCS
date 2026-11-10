# Entorno de desarrollo de WordPress en Docker

WordPress (imagen oficial, PHP 8.3) + Xdebug + WP-CLI + Composer, MariaDB 11.4, phpMyAdmin y Mailpit.
Solo la carpeta **`wp-content`** está en tu equipo: ahí programas plugins y temas. Las explicaciones
completas están en los apuntes (`../Apuntes`).

## Puesta en marcha

```bash
cp .env.example .env                 # solo si no existe .env
docker compose up -d --build
docker compose exec -u www-data wordpress bash /scripts/instalar.sh
```

> En **Git Bash** antepón `MSYS_NO_PATHCONV=1` a las órdenes que llevan rutas como `/scripts/...`
> (si no, Git Bash las convierte en `C:/Program Files/Git/scripts/...`). En PowerShell no hace falta.

| URL | Qué es |
|---|---|
| http://localhost:8090 | la web (página de ejemplo: `/carta/`) |
| http://localhost:8090/wp-admin | panel (`admin` / `admin`) |
| http://localhost:8090/wp-json/baio/v1/carta | ruta REST del plugin de ejemplo |
| http://localhost:8091 | phpMyAdmin |
| http://localhost:8026 | Mailpit (correos de WordPress) |

## Qué incluye

```
wp-content/plugins/bk-carta-baio/   plugin de ejemplo: tipo "Plato", metacaja, shortcode, ajustes, REST
wp-content/themes/baio-hijo/        tema hijo de Twenty Twenty-Five (theme.json + plantilla single-plato)
scripts/instalar.sh                 instalación con WP-CLI (idioma, enlaces, Query Monitor, contenido)
docker/wordpress/                   Dockerfile (Xdebug, WP-CLI, Composer, Mailpit) y php.ini
.vscode/                            Xdebug (pathMappings de wp-content) y stubs de WordPress
```

## WP-CLI

```bash
docker compose exec -u www-data wordpress wp plugin list
docker compose exec -u www-data wordpress wp scaffold plugin mi-plugin
docker compose exec -u www-data wordpress wp post list --post_type=plato
docker compose exec -u www-data wordpress wp db export /var/www/html/wp-content/copia.sql
```

## Volver a empezar

```bash
docker compose down -v    # borra la BD y el núcleo (wp-content se conserva)
```
