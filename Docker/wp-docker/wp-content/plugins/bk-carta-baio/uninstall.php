<?php
// Se ejecuta al BORRAR el plugin desde el panel (no al desactivarlo)
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
delete_option('baio_ajustes');
// Los platos se conservan: son contenido del usuario, no configuración del plugin
