<?php
/*
 * e01-STRICT.php
 *  STRICT_TYPES (1): EL MODO NORMAL (COERCITIVO), SIN declare(strict_types=1)
 *  Tipos en los parámetros: int, float, string, bool, ?int, int|float
 *  (apartado 1 de los apuntes)
 *
 *  Este fichero NO tiene declare(strict_types=1). Cuando una función declara el tipo de un parámetro
 *  (function precio(int $unidades)), PHP intenta CONVERTIR lo que le llega a ese tipo ("5" pasa a ser 5)
 *  y solo da un TypeError si no puede. Aquí se ve qué convierte y qué no; en e02 se repite lo mismo con
 *  strict_types=1 para comparar.
 *
 *  Cómo trabajar este fichero:
 *   1. Pon un punto de ruptura en la primera línea de código y ejecuta en modo depuración.
 *   2. Entra con F11 en entero() o decimal() y mira en VARIABLES de qué tipo llega $n.
 *   3. Los comentarios a la derecha indican el resultado esperado.
 */
require_once __DIR__ . '/utilidades.php';

// Funciones que solo devuelven lo que reciben: así se ve en qué lo ha convertido PHP
function entero(int $n): int
{
    return $n;
}

function decimal(float $n): float
{
    return $n;
}

function texto(string $s): string
{
    return $s;
}

function logico(bool $b): bool
{
    return $b;
}

// ---------------------------------------------------------------------------------------------
// int — Textos numéricos que se convierten
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php#language.types.declarations.strict
// ENUNCIADO EJEMPLO: unidades(int $n) recibe lo que el usuario escribió en un formulario (siempre texto).
// NOTA: en modo coercitivo, un texto que es un número entero ("5", " 5", "5 ", "1e3") se convierte a int.
//       Desde PHP 8, un texto que solo EMPIEZA por un número ("5 manzanas") ya no se acepta: TypeError.
//       La cadena vacía tampoco es un número.
// ---------------------------------------------------------------------------------------------
titulo('int con textos');
probar('entero("5")', fn() => entero("5"));                                // 5
probar('entero(" 5")', fn() => entero(" 5"));                              // 5
probar('entero("5 ")', fn() => entero("5 "));                              // 5
probar('entero("1e3")', fn() => entero("1e3"));                            // 1000
probar('entero("5 manzanas")', fn() => entero("5 manzanas"));              // "TypeError: entero(): Argument #1 ($n) must be of type int, string given"
probar('entero("abc")', fn() => entero("abc"));                            // "TypeError: entero(): Argument #1 ($n) must be of type int, string given"
probar('entero("")', fn() => entero(""));                                  // "TypeError: entero(): Argument #1 ($n) must be of type int, string given"

// ---------------------------------------------------------------------------------------------
// int — Decimales, lógicos y null
// ENLACE: https://www.php.net/manual/es/migration81.deprecated.php
// ENUNCIADO EJEMPLO: unidades(1.5): ¿una unidad y media?
// NOTA: un float con decimales se TRUNCA y, desde PHP 8.1, da el aviso "Deprecated: Implicit conversion
//       from float 1.5 to int loses precision" (probarConAvisos() lo enseña sin parar el código). 2.0 sí entra
//       sin aviso. true se convierte en 1. null NO se acepta en un parámetro int (habría que poner ?int).
// ---------------------------------------------------------------------------------------------
titulo('int con otros tipos');
probar('entero(2.0)', fn() => entero(2.0));                                // 2
probarConAvisos('entero(1.5)', fn() => entero(1.5));                       // "1 (aviso: Implicit conversion from float 1.5 to int loses precision)"
probar('entero(true)', fn() => entero(true));                              // 1
probar('entero(null)', fn() => entero(null));                              // "TypeError: entero(): Argument #1 ($n) must be of type int, null given"
probar('entero([5])', fn() => entero([5]));                                // "TypeError: entero(): Argument #1 ($n) must be of type int, array given"

// ---------------------------------------------------------------------------------------------
// float, string y bool — Las demás conversiones
// ENLACE: https://www.php.net/manual/es/language.types.type-juggling.php
// ENUNCIADO EJEMPLO: precio(float $p), etiqueta(string $s) y activo(bool $b) con datos "de cualquier tipo".
// NOTA: a string se convierte cualquier número (y true pasa a "1"); a bool, cualquier escalar con las reglas
//       de siempre: 0, "0" y "" son false; "no" es true (es un texto no vacío). Es cómodo... y peligroso.
// ---------------------------------------------------------------------------------------------
titulo('float, string y bool');
probar('decimal("3.14")', fn() => decimal("3.14"));                        // 3.14
probar('decimal(5)', fn() => decimal(5));                                  // 5.0
probar('texto(5)', fn() => texto(5));                                      // "5"
probar('texto(1.5)', fn() => texto(1.5));                                  // "1.5"
probar('texto(true)', fn() => texto(true));                                // "1"
probar('logico(0)', fn() => logico(0));                                    // false
probar('logico("0")', fn() => logico("0"));                                // false
probar('logico("no")', fn() => logico("no"));                              // true

// ---------------------------------------------------------------------------------------------
// ?int e int|float — Tipos que admiten null o varios tipos
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php#language.types.declarations.composite
// ENUNCIADO EJEMPLO: descuento(?int $porcentaje) admite "sin descuento" (null); medida(int|float $m).
// NOTA: con un tipo unión, el texto numérico se convierte al tipo que le va: "5" a int y "5.5" a float.
// ---------------------------------------------------------------------------------------------
titulo('?int e int|float');

function opcional(?int $n): ?int
{
    return $n;
}

function medida(int|float $m): int|float
{
    return $m;
}

probar('opcional(null)', fn() => opcional(null));                          // NULL
probar('opcional("8")', fn() => opcional("8"));                            // 8
probar('medida("5")', fn() => medida("5"));                                // 5
probar('medida("5.5")', fn() => medida("5.5"));                            // 5.5

// ---------------------------------------------------------------------------------------------
// El problema — Un error que no se ve
// ENLACE: https://www.php.net/manual/es/function.intdiv.php
// ENUNCIADO EJEMPLO: repartir(int $caramelos, int $ninos) recibe por error el precio (2.99) en lugar de
//                    los caramelos.
// NOTA: el modo coercitivo "arregla" el dato y el programa sigue con un valor que no es el que se quería
//       (2.99 -> 2, con solo un aviso Deprecated que muchas veces nadie ve). Con strict_types=1 (e02) el
//       error salta en la línea exacta de la llamada.
// ---------------------------------------------------------------------------------------------
titulo('Un error silencioso');

function repartir(int $caramelos, int $ninos): int
{
    return intdiv($caramelos, $ninos);
}

$precio = 2.99;
probarConAvisos('repartir($precio, 2)', fn() => repartir($precio, 2));     // "1 (aviso: Implicit conversion from float 2.99 to int loses precision)"
probar('repartir("12", "5")', fn() => repartir("12", "5"));                // 2
