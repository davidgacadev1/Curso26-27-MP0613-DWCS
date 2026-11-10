<?php
namespace BK\CartaBaio;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * GET /wp-json/baio/v1/carta           toda la carta en JSON
 * GET /wp-json/baio/v1/carta?tipo=postre
 */
final class Rest
{
    public static function registrar(): void
    {
        add_action('rest_api_init', [self::class, 'rutas']);
    }

    public static function rutas(): void
    {
        register_rest_route('baio/v1', '/carta', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [self::class, 'carta'],
            'permission_callback' => '__return_true',     // pública
            'args'                => [
                'tipo' => ['type' => 'string', 'default' => '', 'sanitize_callback' => 'sanitize_key'],
            ],
        ]);
    }

    public static function carta(\WP_REST_Request $peticion): \WP_REST_Response
    {
        $respuesta = [];
        foreach (Shortcodes::platosPorTipo($peticion['tipo']) as $tipo => $platos) {
            foreach ($platos as $plato) {
                $respuesta[] = [
                    'id'     => $plato->ID,
                    'nombre' => get_the_title($plato),
                    'tipo'   => $tipo,
                    'precio' => (float) get_post_meta($plato->ID, '_baio_precio', true),
                    'url'    => get_permalink($plato),
                ];
            }
        }
        return new \WP_REST_Response($respuesta, 200);
    }
}
