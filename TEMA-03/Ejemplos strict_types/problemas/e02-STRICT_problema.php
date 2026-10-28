<?php
declare(strict_types=1);
/*
 * e02-STRICT_problema.php
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

// =============================================================================================
// PROBLEMA 2. Una media que devuelve int
//  Escribe mediaRedondeada(int ...$notas): int que devuelva la media redondeada al entero más cercano (6,5 -> 7).
//  En modo estricto no vale return $suma / $n (es un float): hay que convertir a propósito.
// =============================================================================================
titulo('Problema 2');
// completa aquí tu solución

// =============================================================================================
// PROBLEMA 3. Funciones de PHP con tipos
//  Escribe, sin desactivar strict_types:
//   - barra(int $n): string -> $n asteriscos;
//   - etiquetaPrecio(float $precio): string -> "12,50 €" (debe valer también con un int: etiquetaPrecio(12));
//   - cifras(int $n): int -> cuántas cifras tiene $n (sin contar el signo), usando strlen.
// =============================================================================================
titulo('Problema 3');
// completa aquí tu solución

// =============================================================================================
// PROBLEMA 4. Sumar textos validando
//  Escribe sumarTextos(string ...$numeros): int que sume los textos, comprobando cada uno con filter_var; si
//  alguno no es un entero, lanza InvalidArgumentException con el mensaje "«x» no es un número entero".
// =============================================================================================
titulo('Problema 4');
// completa aquí tu solución

// =============================================================================================
// PROBLEMA 5. Datos de un JSON
//  Una tienda envía {"precio":"9.95","unidades":2}: el precio viene como texto y las unidades como número.
//  Escribe totalDesdeJson(string $json): float que acepte el precio como número o como texto numérico, exija
//  que las unidades sean un int y lance InvalidArgumentException si algo no cuadra.
// =============================================================================================
titulo('Problema 5');
// completa aquí tu solución
