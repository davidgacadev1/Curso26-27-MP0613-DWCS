<?php
namespace BK\CartaBaio;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Página Ajustes > Carta Baio con la Settings API. Todo se guarda en una sola opción
 * "baio_ajustes" (un array), que es lo recomendable para no llenar la tabla wp_options.
 */
final class Ajustes
{
    private const OPCION = 'baio_ajustes';
    private const POR_DEFECTO = ['titulo' => 'Nuestra carta', 'moneda' => '€'];

    public static function registrar(): void
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_init', [self::class, 'campos']);
    }

    public static function get(string $clave): string
    {
        $ajustes = wp_parse_args(get_option(self::OPCION, []), self::POR_DEFECTO);
        return (string) $ajustes[$clave];
    }

    public static function menu(): void
    {
        add_options_page('Carta del Bar Baio', 'Carta Baio', 'manage_options', 'baio-ajustes', [self::class, 'pagina']);
    }

    public static function campos(): void
    {
        register_setting('baio', self::OPCION, [
            'type'              => 'array',
            'sanitize_callback' => [self::class, 'limpiar'],
            'default'           => self::POR_DEFECTO,
        ]);
        add_settings_section('baio_general', 'Presentación de la carta', '__return_null', 'baio-ajustes');
        foreach (['titulo' => 'Título de la carta', 'moneda' => 'Símbolo de la moneda'] as $clave => $etiqueta) {
            add_settings_field("baio_$clave", $etiqueta, function () use ($clave): void {
                printf('<input type="text" name="%s[%s]" value="%s" class="regular-text">',
                    esc_attr(self::OPCION), esc_attr($clave), esc_attr(self::get($clave)));
            }, 'baio-ajustes', 'baio_general');
        }
    }

    public static function limpiar(mixed $entrada): array
    {
        $entrada = is_array($entrada) ? $entrada : [];
        return [
            'titulo' => sanitize_text_field($entrada['titulo'] ?? self::POR_DEFECTO['titulo']),
            'moneda' => mb_substr(sanitize_text_field($entrada['moneda'] ?? '€'), 0, 3),
        ];
    }

    public static function pagina(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>Carta del Bar Baio</h1><form method="post" action="options.php">';
        settings_fields('baio');            // campos ocultos + nonce
        do_settings_sections('baio-ajustes');
        submit_button();
        echo '</form><p>Usa el shortcode <code>[carta_baio]</code> en cualquier página.</p></div>';
    }
}
