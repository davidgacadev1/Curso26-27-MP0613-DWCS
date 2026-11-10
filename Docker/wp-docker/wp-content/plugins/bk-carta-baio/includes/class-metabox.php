<?php
namespace BK\CartaBaio;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Caja "Precio" en la pantalla de edición del plato.
 */
final class Metabox
{
    public static function registrar(): void
    {
        add_action('add_meta_boxes_plato', [self::class, 'anadir']);
        add_action('save_post_plato', [self::class, 'guardar']);
    }

    public static function anadir(): void
    {
        add_meta_box('baio_precio', 'Precio', [self::class, 'pintar'], 'plato', 'side');
    }

    public static function pintar(\WP_Post $post): void
    {
        $precio = (float) get_post_meta($post->ID, '_baio_precio', true);
        // El nonce demuestra que el formulario sale de esta pantalla (protección CSRF)
        wp_nonce_field('baio_guardar_precio', 'baio_nonce');
        printf(
            '<label for="baio_precio">Precio (%s)</label>
             <input type="number" step="0.01" min="0" id="baio_precio" name="baio_precio" value="%s" style="width:100%%">',
            esc_html(Ajustes::get('moneda')),
            esc_attr(number_format($precio, 2, '.', ''))
        );
    }

    public static function guardar(int $idPlato): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!isset($_POST['baio_nonce'])
            || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['baio_nonce'])), 'baio_guardar_precio')) {
            return;
        }
        if (!current_user_can('edit_post', $idPlato) || !isset($_POST['baio_precio'])) {
            return;
        }
        // El sanitize_callback de register_post_meta() redondea y evita negativos
        update_post_meta($idPlato, '_baio_precio', wp_unslash($_POST['baio_precio']));
    }
}
