<?php
declare(strict_types=1);
/*
 * e04-STRICT_problema.php
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

// =============================================================================================
// PROBLEMA 2. Una línea de texto
//  Las ventas del Bar Baio se guardan en un fichero, una por línea: "cafe;1.20;3". Escribe
//  leerVenta(string $linea): ?array que devuelva ['producto' => string, 'precio' => float, 'unidades' => int] o
//  null si la línea no tiene ese formato.
// =============================================================================================
titulo('Problema 2');
// completa aquí tu solución

// =============================================================================================
// PROBLEMA 3. Una celda de tabla para cualquier valor
//  Escribe celda(mixed $valor): string que devuelva el valor listo para meter en un <td>, escapado: los float
//  con dos decimales y coma ("7,25"), los int tal cual, los bool como "Sí" / "No", null como "—" y el resto como
//  texto escapado con htmlspecialchars.
// =============================================================================================
titulo('Problema 3');
// completa aquí tu solución

// =============================================================================================
// PROBLEMA 4. El número de página
//  listado.php?pagina=... Escribe paginaPedida(array $get): int que devuelva la página pedida si es un entero
//  >= 1 y, en cualquier otro caso (no existe, "abc", "-1", "2.5" o un array como pagina[]=3), la página 1.
// =============================================================================================
titulo('Problema 4');
// completa aquí tu solución

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
