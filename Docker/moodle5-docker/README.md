# Entorno de desarrollo de Moodle 5 en Docker

Apache + PHP 8.3 (todas las extensiones de Moodle, Xdebug, Composer) + MariaDB 11.4 + cron + phpMyAdmin +
Mailpit. Probado con **Moodle 5.3 LTS** (rama `MOODLE_503_STABLE`). Tus plugins están en `plugins/` y se
montan dentro del código de Moodle. Las explicaciones completas están en los apuntes (`../Apuntes`).

> En Windows, trabaja **dentro de WSL 2** si puedes: Moodle tiene más de 60.000 ficheros y con el código en
> `C:` o `D:` todo es mucho más lento (instalación de 30-60 minutos, páginas de 1-4 s).

## Puesta en marcha

```powershell
copy .env.example .env                      # solo la primera vez
.\scripts\obtener-moodle.ps1                # git clone (en Linux/WSL: bash scripts/obtener-moodle.sh)
docker compose up -d --build
docker compose exec -u www-data web bash /scripts/instalar.sh
```

> En **Git Bash** antepón `MSYS_NO_PATHCONV=1` a las órdenes con rutas como `/scripts/...`.

`instalar.sh` ejecuta `composer install` (el repositorio Git de Moodle 5.1+ no trae `vendor/`), instala la
base de datos, activa la depuración de desarrollador, el tema `baio` y el idioma español. Si lo vuelves a
ejecutar, solo actualiza (lo que hay que hacer cada vez que subes la versión de un plugin).

| URL | Qué es |
|---|---|
| http://localhost:8100 | Moodle (`admin` / `Admin.1234`) |
| http://localhost:8100/local/baio/index.php | página del plugin local de ejemplo |
| http://localhost:8101 | phpMyAdmin |
| http://localhost:8027 | Mailpit (correos de Moodle) |

## Plugins de ejemplo (`plugins/`)

| Carpeta | Componente | Dentro de Moodle |
|---|---|---|
| `plugins/local_baio` | `local_baio`: página, ajuste, capacidad, hook de navegación, plantilla Mustache | `public/local/baio` |
| `plugins/block_baio` | `block_baio`: bloque «Menú del día» configurable | `public/blocks/baio` |
| `plugins/theme_baio` | `theme_baio`: tema hijo de Boost con color de marca y SCSS | `public/theme/baio` |

Para añadir un plugin nuevo: crea su carpeta en `plugins/`, añade su línea en `volumes` de `compose.yaml`
(y en `pathMappings` de `.vscode/launch.json`), `docker compose up -d` y ejecuta de nuevo `instalar.sh`.

## Órdenes útiles

```bash
docker compose exec -u www-data web php admin/cli/purge_caches.php
docker compose exec -u www-data web php admin/cli/upgrade.php --non-interactive
docker compose exec -u www-data web php admin/cli/cfg.php --name=debug          # leer un ajuste
docker compose exec -u www-data web php admin/cli/scheduled_task.php --list
docker compose logs -f web
docker compose down -v      # borra BD y moodledata (el código y tus plugins se conservan)
```

## Moodle 5.0

5.0 no tiene carpeta `public`: en `.env` usa `MOODLE_BRANCH=MOODLE_500_STABLE`,
`MOODLE_DOCROOT=/var/www/moodle`, `MOODLE_PUBLIC=/var/www/moodle` y cambia las rutas de `launch.json`.
(Preparado, pero el entorno se ha probado con 5.3.)
