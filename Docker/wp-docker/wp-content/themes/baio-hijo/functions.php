<?php
/**
 * Funciones del tema hijo. WordPress carga este fichero ANTES que el functions.php del padre.
 */
if (!defined('ABSPATH')) {
    exit;
}

// En un tema de bloques el style.css del hijo no se carga solo: hay que encolarlo
add_action('wp_enqueue_scripts', function (): void {
    wp_enqueue_style('baio-hijo', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
});

// Ejemplo de filtro: los extractos de los platos, más cortos (20 palabras)
add_filter('excerpt_length', function (int $palabras): int {
    return get_post_type() === 'plato' ? 20 : $palabras;
});
