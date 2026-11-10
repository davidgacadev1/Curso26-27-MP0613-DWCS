<?php
/**
 * Datos de conexión a la base de datos.
 *
 * En Docker el servidor de BD se llama "db" (el nombre del servicio en compose.yaml)
 * y los datos llegan como variables de entorno. Si ejecutas el mismo código en
 * XAMPP, getenv() devuelve false y se usan los valores de la derecha.
 */
declare(strict_types=1);

const BD = [
    'host'     => 'localhost',
    'puerto'   => '3306',
    'nombre'   => 'dwcs',
    'usuario'  => 'dwcs',
    'clave'    => 'dwcs',
];

function conectar(): PDO
{
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        getenv('DB_HOST') ?: BD['host'],
        getenv('DB_PORT') ?: BD['puerto'],
        getenv('DB_NAME') ?: BD['nombre']
    );
    return new PDO($dsn, getenv('DB_USER') ?: BD['usuario'], getenv('DB_PASSWORD') ?: BD['clave'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}
