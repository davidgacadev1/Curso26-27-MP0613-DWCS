<?php
/**
 * Script de consola para depurar dentro del contenedor:
 *
 *   docker compose exec web php dwcs/consola.php Uxía
 *
 * Con VS Code escuchando ("Xdebug en Docker"), se para en los puntos de ruptura.
 */
declare(strict_types=1);

$nombre = $argv[1] ?? 'Carlos';
$letras = mb_strlen($nombre);

echo "Hola, $nombre: tu nombre tiene $letras letras.", PHP_EOL;
echo 'PHP ', PHP_VERSION, ' en ', php_uname('s'), ' (', gethostname(), ')', PHP_EOL;
