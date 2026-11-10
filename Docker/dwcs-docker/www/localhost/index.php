<?php
/**
 * Panel del entorno Docker (equivale al "dashboard" de XAMPP).
 * Comprueba PHP, las extensiones, la conexión a MariaDB, Xdebug y el correo.
 */
declare(strict_types=1);

$puertoWeb = $_SERVER['SERVER_PORT'] === '80' ? '' : ':' . $_SERVER['SERVER_PORT'];
// Dentro del contenedor el puerto es siempre 80; el de tu equipo llega en la cabecera Host
if (preg_match('/:(\d+)$/', $_SERVER['HTTP_HOST'] ?? '', $m)) {
    $puertoWeb = ':' . $m[1];
}

// --- Conexión a la base de datos ----------------------------------------------
$bd = ['ok' => false, 'mensaje' => '', 'version' => ''];
try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: 'db', getenv('DB_PORT') ?: '3306', getenv('DB_NAME') ?: 'dwcs'),
        getenv('DB_USER') ?: 'dwcs',
        getenv('DB_PASSWORD') ?: 'dwcs',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $bd['ok'] = true;
    $bd['version'] = (string) $pdo->query('SELECT VERSION()')->fetchColumn();
    $bd['mensaje'] = 'Conectado como ' . (getenv('DB_USER') ?: 'dwcs') . ' a la base de datos '
        . (getenv('DB_NAME') ?: 'dwcs');
} catch (PDOException $e) {
    $bd['mensaje'] = $e->getMessage();
}

// --- Correo de prueba (lo recoge Mailpit) -------------------------------------
$correo = null;
if (isset($_GET['correo'])) {
    $correo = mail('carlos@bkprogramacion.test', 'Prueba desde Docker', "¡Funciona mail()!\n",
        'From: web@dwcs.localhost');
}

if (isset($_GET['xdebug']) && function_exists('xdebug_info')) {
    xdebug_info();
    exit;
}
if (isset($_GET['phpinfo'])) {
    phpinfo();
    exit;
}

$extensiones = ['pdo_mysql', 'mysqli', 'intl', 'gd', 'zip', 'mbstring', 'bcmath', 'Zend OPcache', 'xdebug'];
// Modos activos de Xdebug (la variable de entorno XDEBUG_MODE manda sobre php.ini)
$modoXdebug = function_exists('xdebug_info') ? implode(',', xdebug_info('mode')) : 'off';

function e(string $t): string
{
    return htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
}

function enlace(string $host, string $puerto, string $texto): string
{
    return '<a href="http://' . e($host . $puerto) . '/">' . e($texto) . '</a>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entorno DWCS en Docker</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 900px; margin: 2em auto; color: #222; }
        h1 { color: #5b3cc4; }
        .ok { color: #2e7d32; font-weight: bold; }
        .mal { color: #c62828; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #ccc; padding: .4em .6em; text-align: left; }
        th { background: #ece2fb; }
        code { background: #f1f1f1; padding: 0 .3em; }
    </style>
</head>
<body>
<h1>Entorno DWCS en Docker</h1>
<p>PHP <strong><?= PHP_VERSION ?></strong> sobre <?= e($_SERVER['SERVER_SOFTWARE'] ?? 'Apache') ?>
    · zona horaria <?= e(date_default_timezone_get()) ?> · <?= date('d/m/Y H:i:s') ?></p>

<h2>Sitios</h2>
<table>
    <tr><th>URL</th><th>Carpeta</th></tr>
    <tr><td><?= enlace('dwcs.localhost', $puertoWeb, 'dwcs.localhost' . $puertoWeb) ?></td><td><code>www/dwcs</code></td></tr>
    <tr><td><?= enlace('dwcs-profe.localhost', $puertoWeb, 'dwcs-profe.localhost' . $puertoWeb) ?></td><td><code>www/dwcs-profe</code></td></tr>
    <tr><td><?= enlace('ejercicio1.localhost', $puertoWeb, 'ejercicio1.localhost' . $puertoWeb) ?></td><td><code>www/ejercicio1</code> (cualquier carpeta)</td></tr>
    <tr><td><a href="http://localhost:8081/">localhost:8081</a></td><td>phpMyAdmin</td></tr>
    <tr><td><a href="http://localhost:8025/">localhost:8025</a></td><td>Mailpit</td></tr>
</table>

<h2>Comprobaciones</h2>
<table>
    <tr><th>Elemento</th><th>Estado</th></tr>
    <tr>
        <td>MariaDB</td>
        <td class="<?= $bd['ok'] ? 'ok' : 'mal' ?>"><?= e($bd['mensaje']) ?> <?= e($bd['version']) ?></td>
    </tr>
    <?php foreach ($extensiones as $ext): ?>
        <tr>
            <td>Extensión <code><?= e($ext) ?></code></td>
            <td class="<?= extension_loaded($ext) ? 'ok' : 'mal' ?>"><?= extension_loaded($ext) ? 'cargada' : 'no cargada' ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td>Modo de Xdebug</td>
        <td><?= e($modoXdebug ?: 'off') ?> (variable <code>XDEBUG_MODE</code> del <code>.env</code>)</td>
    </tr>
    <tr>
        <td>Correo</td>
        <td>
            <?php if ($correo === null): ?>
                <a href="?correo=1">Enviar un correo de prueba</a>
            <?php else: ?>
                <span class="<?= $correo ? 'ok' : 'mal' ?>"><?= $correo ? 'Enviado: míralo en Mailpit' : 'mail() ha fallado' ?></span>
            <?php endif; ?>
        </td>
    </tr>
</table>

<p><a href="?phpinfo=1">phpinfo()</a> · <a href="?xdebug=1">xdebug_info()</a></p>
</body>
</html>
