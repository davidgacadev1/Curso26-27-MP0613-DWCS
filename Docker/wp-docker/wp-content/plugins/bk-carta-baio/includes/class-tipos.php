<?php
namespace BK\CartaBaio;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Tipo de contenido "plato", taxonomía "tipo_plato" y campo personalizado del precio.
 */
final class Tipos
{
    public const TIPOS_INICIALES = [
        'entrante'  => 'Entrantes',
        'principal' => 'Principales',
        'postre'    => 'Postres',
    ];

    public static function registrar(): void
    {
        add_action('init', [self::class, 'registrarTipos']);
    }

    public static function registrarTipos(): void
    {
        register_post_type('plato', [
            'labels' => [
                'name'          => 'Platos',
                'singular_name' => 'Plato',
                'add_new_item'  => 'Añadir plato',
                'edit_item'     => 'Editar plato',
                'all_items'     => 'Todos los platos',
            ],
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => ['slug' => 'platos'],
            'menu_icon'    => 'dashicons-carrot',
            // custom-fields: necesario para que los campos (meta) salgan en la API REST y en el editor
            'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest' => true,          // necesario para el editor de bloques
        ]);

        register_taxonomy('tipo_plato', 'plato', [
            'labels'            => ['name' => 'Tipos de plato', 'singular_name' => 'Tipo de plato'],
            'hierarchical'      => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'tipo'],
        ]);

        // El guion bajo inicial oculta el campo en la caja "Campos personalizados"
        register_post_meta('plato', '_baio_precio', [
            'type'              => 'number',
            'single'            => true,
            'default'           => 0,
            'show_in_rest'      => true,
            'sanitize_callback' => fn($valor) => round(max(0, (float) $valor), 2),
            'auth_callback'     => fn() => current_user_can('edit_posts'),
        ]);
    }

    /** Se ejecuta una sola vez, al activar el plugin. */
    public static function activar(): void
    {
        self::registrarTipos();
        foreach (self::TIPOS_INICIALES as $slug => $nombre) {
            if (!term_exists($slug, 'tipo_plato')) {
                wp_insert_term($nombre, 'tipo_plato', ['slug' => $slug]);
            }
        }
        flush_rewrite_rules();
    }

    /** Precio de un plato con el formato y la moneda de los ajustes: "16,50 €". */
    public static function precioFormateado(int $idPlato): string
    {
        $precio = (float) get_post_meta($idPlato, '_baio_precio', true);
        return number_format_i18n($precio, 2) . ' ' . Ajustes::get('moneda');
    }
}
