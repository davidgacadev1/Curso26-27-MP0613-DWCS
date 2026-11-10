<?php
// Cualquier carpeta de www/ se publica como http://CARPETA.localhost:8080
// (sitio "comodín" de Apache con mod_vhost_alias)
echo '<h1>Ejercicio 1</h1>';
echo '<p>Servido desde <code>' . htmlspecialchars(__DIR__) . '</code></p>';
echo '<p>DOCUMENT_ROOT = <code>' . htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . '</code></p>';
