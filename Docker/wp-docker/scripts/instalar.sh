#!/usr/bin/env bash
# =============================================================================
#  Instala y configura WordPress con WP-CLI. Se ejecuta DENTRO del contenedor:
#
#    docker compose exec -u www-data wordpress bash /scripts/instalar.sh
#
#  Se puede repetir sin problema: lo que ya está hecho se salta.
# =============================================================================
set -euo pipefail
cd /var/www/html

if wp core is-installed 2>/dev/null; then
    echo "WordPress ya estaba instalado."
else
    wp core install \
        --url="${WP_URL}" \
        --title="Bar Baio" \
        --admin_user="${WP_ADMIN}" \
        --admin_password="${WP_ADMIN_PASSWORD}" \
        --admin_email="${WP_ADMIN_EMAIL}" \
        --skip-email
fi

# Idioma, zona horaria y enlaces permanentes "bonitos"
wp language core install es_ES --activate || true
wp option update timezone_string "Europe/Madrid"
wp option update date_format "d/m/Y"
wp rewrite structure '/%postname%/'

# Fuera el contenido de ejemplo de WordPress ("Hello world!" y "Sample Page")
for slug in hello-world sample-page; do
    ids=$(wp post list --post_type=post,page --name="$slug" --format=ids)
    [ -n "$ids" ] && wp post delete $ids --force
done

# Plugins de ayuda al desarrollo
wp plugin install query-monitor --activate || true
wp plugin delete hello akismet 2>/dev/null || true

# Nuestro plugin y nuestro tema hijo (están en ./wp-content del proyecto)
wp plugin activate bk-carta-baio
wp theme activate baio-hijo

# Contenido de ejemplo: platos de la carta (solo si aún no hay ninguno)
if [ "$(wp post list --post_type=plato --format=count)" = "0" ]; then
    crear() {  # crear "Nombre" "tipo" precio "descripción"
        id=$(wp post create --post_type=plato --post_status=publish --post_title="$1" \
             --post_content="$4" --porcelain)
        wp post term set "$id" tipo_plato "$2" >/dev/null
        wp post meta update "$id" _baio_precio "$3" >/dev/null
        echo "Plato $id: $1"
    }
    crear "Pulpo á feira"        principal 16.50 "Con cachelos, pimentón de la Vera y aceite de oliva."
    crear "Pimientos de Padrón"  entrante   6.00 "Uns pican e outros non."
    crear "Caldo gallego"        entrante   7.20 "Grelos, patata, alubia blanca y unto."
    crear "Merluza á galega"     principal 18.90 "Con patatas, ajada y guisantes."
    crear "Tarta de Santiago"    postre     5.50 "Almendra, azúcar y huevo. Nada más."
fi

# Página con el shortcode de la carta
if [ -z "$(wp post list --post_type=page --name=carta --format=ids)" ]; then
    wp post create --post_type=page --post_status=publish --post_title="Carta" \
        --post_name=carta --post_content='[carta_baio]' --porcelain
fi

echo
echo "Listo: ${WP_URL}  (panel: ${WP_URL}/wp-admin  ${WP_ADMIN} / ${WP_ADMIN_PASSWORD})"
