<?php
declare(strict_types=1);
/*
 * e04-STRICT_problema_solucion.php
 *  (FICHERO CON LAS SOLUCIONES)
 *  Problemas de: datos de fuera en modo estricto (formularios, líneas de texto, salida, comparaciones)
 *  (repasa los ejemplos de ../e04-STRICT.php)
 *
 *  Este fichero tiene declare(strict_types=1). Resuelve cada problema donde se indica y ejecútalo en modo
 *  depuración para comprobarlo.
 */
require_once __DIR__ . '/../utilidades.php';

// =============================================================================================
// PROBLEMA 1. Leer la matrícula
//  Escribe leerAlumno(array $post): array que devuelva [datos, errores]. Datos con su tipo: nombre (string, sin
//  espacios a los lados, no vacío), edad (int de 12 a 99), nota (float de 0 a 10, con coma o punto) y beca
//  (bool: true si llega el checkbox). Cada dato incorrecto añade un error; si un dato no vale, guarda null.
// =============================================================================================
titulo('Problema 1');
$bien = ['nombre' => '  Uxía ', 'edad' => '16', 'nota' => '7,25', 'beca' => 'on'];
$mal = ['nombre' => '   ', 'edad' => '160', 'nota' => 'diez'];
// completa aquí tu solución
// ---- SOLUCIÓN ----
function leerAlumno(array $post): array
{
    $errores = [];
    $nombre = is_string($post['nombre'] ?? null) ? trim($post['nombre']) : '';
    if ($nombre === '') {
        $errores[] = 'Falta el nombre.';
    }
    $edad = filter_var($post['edad'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 12, 'max_range' => 99]]);
    if ($edad === false) {
        $errores[] = 'La edad debe ser un entero de 12 a 99.';
    }
    $nota = is_string($post['nota'] ?? null)
        ? filter_var(str_replace(',', '.', $post['nota']), FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0, 'max_range' => 10]])
        : false;
    if ($nota === false) {
        $errores[] = 'La nota debe ser un número de 0 a 10.';
    }
    $datos = [
        'nombre' => $nombre === '' ? null : $nombre,
        'edad' => $edad === false ? null : $edad,
        'nota' => $nota === false ? null : $nota,
        'beca' => isset($post['beca']),
    ];
    return [$datos, $errores];
}

ver('bien', leerAlumno($bien));                                            // [0 => ['nombre' => 'Uxía', 'edad' => 16, 'nota' => 7.25, 'beca' => true], 1 => []]
[$datosMal, $erroresMal] = leerAlumno($mal);
ver('mal: datos', $datosMal);                                              // ['nombre' => NULL, 'edad' => NULL, 'nota' => NULL, 'beca' => false]
ver('mal: errores', $erroresMal);                                          // ['Falta el nombre.', 'La edad debe ser un entero de 12 a 99.', 'La nota debe ser un número de 0 a 10.']

// =============================================================================================
// PROBLEMA 2. Una línea de texto
//  Las ventas del Bar Baio se guardan en un fichero, una por línea: "cafe;1.20;3". Escribe
//  leerVenta(string $linea): ?array que devuelva ['producto' => string, 'precio' => float, 'unidades' => int] o
//  null si la línea no tiene ese formato.
// =============================================================================================
titulo('Problema 2');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function leerVenta(string $linea): ?array
{
    $partes = explode(';', trim($linea));
    if (count($partes) !== 3) {
        return null;
    }
    $precio = filter_var($partes[1], FILTER_VALIDATE_FLOAT);
    $unidades = filter_var($partes[2], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($partes[0] === '' || $precio === false || $unidades === false) {
        return null;
    }
    return ['producto' => $partes[0], 'precio' => $precio, 'unidades' => $unidades];
}

ver('cafe;1.20;3', leerVenta('cafe;1.20;3'));                              // ['producto' => 'cafe', 'precio' => 1.2, 'unidades' => 3]
ver('tostada;1,5;2', leerVenta('tostada;1,5;2'));                          // NULL
ver('zumo;2.00', leerVenta('zumo;2.00'));                                  // NULL

// =============================================================================================
// PROBLEMA 3. Una celda de tabla para cualquier valor
//  Escribe celda(mixed $valor): string que devuelva el valor listo para meter en un <td>, escapado: los float
//  con dos decimales y coma ("7,25"), los int tal cual, los bool como "Sí" / "No", null como "—" y el resto como
//  texto escapado con htmlspecialchars.
// =============================================================================================
titulo('Problema 3');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function celda(mixed $valor): string
{
    return match (true) {
        is_float($valor) => number_format($valor, 2, ',', '.'),
        is_int($valor) => (string) $valor,
        is_bool($valor) => $valor ? 'Sí' : 'No',
        $valor === null => '—',
        default => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'),
    };
}

ver('celdas', array_map('celda', [7.25, 16, true, null, '<b>Uxía</b>']));  // ['7,25', '16', 'Sí', '—', '&lt;b&gt;Uxía&lt;/b&gt;']

// =============================================================================================
// PROBLEMA 4. El número de página
//  listado.php?pagina=... Escribe paginaPedida(array $get): int que devuelva la página pedida si es un entero
//  >= 1 y, en cualquier otro caso (no existe, "abc", "-1", "2.5" o un array como pagina[]=3), la página 1.
// =============================================================================================
titulo('Problema 4');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function paginaPedida(array $get): int
{
    $pagina = filter_var($get['pagina'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $pagina === false ? 1 : $pagina;
}

$pruebas = [['pagina' => '3'], [], ['pagina' => 'abc'], ['pagina' => '-1'], ['pagina' => '2.5'], ['pagina' => ['3']]];
ver('páginas', array_map('paginaPedida', $pruebas));                       // [3, 1, 1, 1, 1, 1]

// =============================================================================================
// PROBLEMA 5. El cupón "1e1"
//  Los cupones válidos son ["10", "25", "BAIO"]. Comprueba con in_array normal si "1e1" y "25.0" son válidos
//  y explica el resultado en un comentario. Después escribe cuponValido(string $escrito, array $cupones): bool
//  que compare sin conversiones (y sin distinguir mayúsculas en los cupones de letras).
// =============================================================================================
titulo('Problema 5');
$cupones = ['10', '25', 'BAIO'];
ver('in_array("1e1")', in_array('1e1', $cupones));                         // true
ver('in_array("25.0")', in_array('25.0', $cupones));                       // true
// completa aquí tu solución
// ---- SOLUCIÓN ----
// in_array compara con ==: dos textos que parecen números se comparan como números ("1e1" vale 10 y "25.0"
// vale 25). strict_types no cambia los operadores; hay que pedir la comparación estricta (tercer parámetro).
function cuponValido(string $escrito, array $cupones): bool
{
    return in_array(strtoupper(trim($escrito)), $cupones, true);
}

ver('cuponValido("1e1")', cuponValido('1e1', $cupones));                   // false
ver('cuponValido("25")', cuponValido('25', $cupones));                     // true
ver('cuponValido(" baio ")', cuponValido(' baio ', $cupones));             // true
