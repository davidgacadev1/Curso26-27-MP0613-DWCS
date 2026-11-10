<?php
namespace BK\CartaBaio;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * [carta_baio]               la carta completa, agrupada por tipo de plato
 * [carta_baio tipo="postre"] solo un tipo
 * [precio_plato]             precio del plato que se está mostrando (para plantillas)
 */
final class Shortcodes
{
    public static function registrar(): void
    {
        add_shortcode('carta_baio', [self::class, 'carta']);
        add_shortcode('precio_plato', [self::class, 'precio']);
        add_action('wp_enqueue_scripts', [self::class, 'estilos']);
    }

    public static function estilos(): void
    {
        // Solo se registra; se encola cuando una página usa el shortcode
        wp_register_style('baio-carta', BAIO_URL . 'assets/carta.css', [], BAIO_VERSION);
    }

    /** Platos publicados agrupados por slug del tipo: ['entrante' => [WP_Post, ...], ...] */
    public static function platosPorTipo(string $tipo = ''): array
    {
        $args = [
            'post_type'      => 'plato',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
        if ($tipo !== '') {
            $args['tax_query'] = [['taxonomy' => 'tipo_plato', 'field' => 'slug', 'terms' => $tipo]];
        }
        $grupos = [];
        foreach (get_posts($args) as $plato) {
            $terminos = get_the_terms($plato, 'tipo_plato');
            $slug = $terminos && !is_wp_error($terminos) ? $terminos[0]->slug : 'otros';
            $grupos[$slug][] = $plato;
        }
        // Orden de la carta: entrantes, principales, postres y el resto
        $orden = array_keys(Tipos::TIPOS_INICIALES);
        uksort($grupos, function (string $a, string $b) use ($orden): int {
            $pa = array_search($a, $orden, true);
            $pb = array_search($b, $orden, true);
            return ($pa === false ? 99 : $pa) <=> ($pb === false ? 99 : $pb);
        });
        return $grupos;
    }

    public static function carta(array|string $atributos = []): string
    {
        $atributos = shortcode_atts(['tipo' => ''], $atributos, 'carta_baio');
        wp_enqueue_style('baio-carta');

        $grupos = self::platosPorTipo(sanitize_key($atributos['tipo']));
        if (!$grupos) {
            return '<p class="baio-carta">Hoy no hay platos en la carta.</p>';
        }

        // Un shortcode DEVUELVE el HTML (no lo imprime con echo)
        $html = '<div class="baio-carta"><h2>' . esc_html(Ajustes::get('titulo')) . '</h2>';
        foreach ($grupos as $slug => $platos) {
            $termino = get_term_by('slug', $slug, 'tipo_plato');
            $html .= '<h3>' . esc_html($termino ? $termino->name : ucfirst($slug)) . '</h3><ul>';
            foreach ($platos as $plato) {
                $html .= sprintf(
                    '<li><a href="%s">%s</a> <span class="baio-precio">%s</span></li>',
                    esc_url(get_permalink($plato)),
                    esc_html(get_the_title($plato)),
                    esc_html(Tipos::precioFormateado($plato->ID))
                );
            }
            $html .= '</ul>';
        }
        return $html . '</div>';
    }

    public static function precio(): string
    {
        $id = get_the_ID();
        if (!$id || get_post_type($id) !== 'plato') {
            return '';
        }
        return '<p class="baio-precio">' . esc_html(Tipos::precioFormateado($id)) . '</p>';
    }
}
