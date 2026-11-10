<?php
/**
 * El mismo código en dos versiones de PHP:
 *   http://dwcs.localhost:8080/versiones.php   (PHP 8.2)
 *   http://dwcs.localhost:8084/versiones.php   (PHP 8.4, con compose.php84.yaml)
 */

// Parámetro con valor por defecto null pero sin "?" en el tipo:
// PHP 8.4 avisa "Implicitly marking parameter $nombre as nullable is deprecated"
function saludar(string $nombre = null): string
{
    return 'Hola, ' . ($nombre ?? 'desconocido');
}

echo '<p>PHP ', PHP_VERSION, '</p>';
echo '<p>', saludar(), '</p>';
