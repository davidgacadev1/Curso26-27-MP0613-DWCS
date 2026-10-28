<?php
declare(strict_types=1);
/*
 * e02-STRICT_problema_solucion.php
 *  (FICHERO CON LAS SOLUCIONES)
 *  Problemas de: el modo estricto (llamadas, retornos, funciones de PHP y conversiones explícitas)
 *  (repasa los ejemplos de ../e02-STRICT.php)
 *
 *  Este fichero, como e02, tiene declare(strict_types=1). Resuelve cada problema donde se indica y
 *  ejecútalo en modo depuración para comprobarlo.
 */
require_once __DIR__ . '/../utilidades.php';

// =============================================================================================
// PROBLEMA 1. Arreglar la llamada
//  Los datos del alumno llegan todos como texto. Escribe la llamada a ficha() convirtiendo cada dato de forma
//  explícita (sin cambiar ficha()). "0" en repetidor significa que no repite.
//  Esperado: "Ana (17 años): 8,5 · no repite"
// =============================================================================================
titulo('Problema 1');
$datos = ['nombre' => 'Ana', 'edad' => '17', 'nota' => '8.5', 'repetidor' => '0'];

function ficha(string $nombre, int $edad, float $nota, bool $repetidor): string
{
    return "$nombre ($edad años): " . number_format($nota, 1, ',', '.') . ' · ' . ($repetidor ? 'repite' : 'no repite');
}

probar('ficha() con los datos tal cual', fn() => ficha($datos['nombre'], $datos['edad'], $datos['nota'], $datos['repetidor'])); // "TypeError: ficha(): Argument #2 ($edad) must be of type int, string given"
// completa aquí tu solución
// ---- SOLUCIÓN ----
$texto = ficha($datos['nombre'], (int) $datos['edad'], (float) $datos['nota'], $datos['repetidor'] === '1');
ver('ficha() convirtiendo', $texto);                                       // "Ana (17 años): 8,5 · no repite"

// =============================================================================================
// PROBLEMA 2. Una media que devuelve int
//  Escribe mediaRedondeada(int ...$notas): int que devuelva la media redondeada al entero más cercano (6,5 -> 7).
//  En modo estricto no vale return $suma / $n (es un float): hay que convertir a propósito.
// =============================================================================================
titulo('Problema 2');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function mediaRedondeada(int ...$notas): int
{
    return (int) round(array_sum($notas) / count($notas));
}

ver('mediaRedondeada(6, 7)', mediaRedondeada(6, 7));                       // 7
ver('mediaRedondeada(5, 6, 6)', mediaRedondeada(5, 6, 6));                 // 6
ver('mediaRedondeada(10)', mediaRedondeada(10));                           // 10

// =============================================================================================
// PROBLEMA 3. Funciones de PHP con tipos
//  Escribe, sin desactivar strict_types:
//   - barra(int $n): string -> $n asteriscos;
//   - etiquetaPrecio(float $precio): string -> "12,50 €" (debe valer también con un int: etiquetaPrecio(12));
//   - cifras(int $n): int -> cuántas cifras tiene $n (sin contar el signo), usando strlen.
// =============================================================================================
titulo('Problema 3');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function barra(int $n): string
{
    return str_repeat('*', $n);
}

function etiquetaPrecio(float $precio): string
{
    return number_format($precio, 2, ',', '.') . ' €';
}

function cifras(int $n): int
{
    return strlen((string) abs($n));          // strlen($n) sería un TypeError en modo estricto
}

ver('barra(5)', barra(5));                                                 // "*****"
ver('etiquetaPrecio(12)', etiquetaPrecio(12));                             // "12,00 €"
ver('etiquetaPrecio(1234.5)', etiquetaPrecio(1234.5));                     // "1.234,50 €"
ver('cifras(-2026)', cifras(-2026));                                       // 4

// =============================================================================================
// PROBLEMA 4. Sumar textos validando
//  Escribe sumarTextos(string ...$numeros): int que sume los textos, comprobando cada uno con filter_var; si
//  alguno no es un entero, lanza InvalidArgumentException con el mensaje "«x» no es un número entero".
// =============================================================================================
titulo('Problema 4');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function sumarTextos(string ...$numeros): int
{
    $suma = 0;
    foreach ($numeros as $texto) {
        $numero = filter_var($texto, FILTER_VALIDATE_INT);
        if ($numero === false) {
            throw new InvalidArgumentException("«{$texto}» no es un número entero");
        }
        $suma += $numero;
    }
    return $suma;
}

probar('sumarTextos("3", "4", "-2")', fn() => sumarTextos("3", "4", "-2")); // 5
probar('sumarTextos("3", "x")', fn() => sumarTextos("3", "x"));            // "InvalidArgumentException: «x» no es un número entero"
probar('sumarTextos(3, 4)', fn() => sumarTextos(3, 4));                    // "TypeError: sumarTextos(): Argument #1 must be of type string, int given"

// =============================================================================================
// PROBLEMA 5. Datos de un JSON
//  Una tienda envía {"precio":"9.95","unidades":2}: el precio viene como texto y las unidades como número.
//  Escribe totalDesdeJson(string $json): float que acepte el precio como número o como texto numérico, exija
//  que las unidades sean un int y lance InvalidArgumentException si algo no cuadra.
// =============================================================================================
titulo('Problema 5');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function totalDesdeJson(string $json): float
{
    $datos = json_decode($json, true);
    if (!is_array($datos) || !isset($datos['precio'], $datos['unidades'])) {
        throw new InvalidArgumentException('JSON sin precio o sin unidades');
    }
    if (!is_numeric($datos['precio']) || !is_int($datos['unidades'])) {
        throw new InvalidArgumentException('Precio o unidades con un tipo incorrecto');
    }
    return round((float) $datos['precio'] * $datos['unidades'], 2);
}

probar('precio como texto', fn() => totalDesdeJson('{"precio":"9.95","unidades":2}')); // 19.9
probar('precio como número', fn() => totalDesdeJson('{"precio":9.95,"unidades":3}')); // 29.85
probar('unidades como texto', fn() => totalDesdeJson('{"precio":9.95,"unidades":"3"}')); // "InvalidArgumentException: Precio o unidades con un tipo incorrecto"
probar('JSON roto', fn() => totalDesdeJson('{precio: 9.95}'));             // "InvalidArgumentException: JSON sin precio o sin unidades"
