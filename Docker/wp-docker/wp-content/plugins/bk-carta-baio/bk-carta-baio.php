<?php
/**
 * Plugin Name:       BK Carta del Bar Baio
 * Description:       Plugin de ejemplo de BK Programación: tipo de contenido "Plato", precio en una metacaja, shortcode [carta_baio], página de ajustes y ruta REST /wp-json/baio/v1/carta.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.2
 * Author:            BK Programación
 * License:           GPL-2.0-or-later
 * Text Domain:       bk-carta-baio
 */

// Nunca se ejecuta un fichero del plugin directamente: solo a través de WordPress
if (!defined('ABSPATH')) {
    exit;
}

const BAIO_VERSION = '1.0.0';
define('BAIO_DIR', plugin_dir_path(__FILE__));
define('BAIO_URL', plugin_dir_url(__FILE__));

require_once BAIO_DIR . 'includes/class-tipos.php';
require_once BAIO_DIR . 'includes/class-metabox.php';
require_once BAIO_DIR . 'includes/class-shortcodes.php';
require_once BAIO_DIR . 'includes/class-ajustes.php';
require_once BAIO_DIR . 'includes/class-rest.php';

use BK\CartaBaio\{Tipos, Metabox, Shortcodes, Ajustes, Rest};

// Cada clase engancha sus funciones a las acciones y filtros (hooks) de WordPress
Tipos::registrar();
Metabox::registrar();
Shortcodes::registrar();
Ajustes::registrar();
Rest::registrar();

// Al activar: crear los tipos de plato y regenerar los enlaces permanentes
register_activation_hook(__FILE__, [Tipos::class, 'activar']);
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
