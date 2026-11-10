<?php
/**
 * Ejemplo para comprobar el entorno: la carta del Bar Baio leída de MariaDB.
 *
 * PRUEBA DE DEPURACIÓN: pon un punto de ruptura en la línea del "foreach",
 * arranca "Xdebug en Docker" en VS Code (F5) y recarga http://dwcs.localhost:8080
 */
declare(strict_types=1);

require __DIR__ . '/config.php';

$platos = conectar()->query('SELECT nombre, categoria, precio, vegano FROM platos ORDER BY categoria, nombre')
    ->fetchAll();

// Agrupa los platos por categoría y suma lo que costaría pedir uno de cada
$carta = [];
$total = 0.0;
foreach ($platos as $plato) {
    $carta[$plato['categoria']][] = $plato;
    $total += (float) $plato['precio'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carta del Bar Baio</title>
</head>
<body>
<h1>Carta del Bar Baio</h1>
<?php foreach ($carta as $categoria => $lista): ?>
    <h2><?= htmlspecialchars(ucfirst($categoria)) ?></h2>
    <ul>
        <?php foreach ($lista as $p): ?>
            <li><?= htmlspecialchars($p['nombre']) ?> — <?= number_format((float) $p['precio'], 2, ',', '.') ?> €
                <?= $p['vegano'] ? '🌱' : '' ?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
<p>Uno de cada: <strong><?= number_format($total, 2, ',', '.') ?> €</strong></p>
</body>
</html>
