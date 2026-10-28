<?php
declare(strict_types=1);
/*
 * e02-STRICT.php
 *  STRICT_TYPES (2): EL MODO ESTRICTO, CON declare(strict_types=1)
 *  Los mismos casos de e01, ahora en modo estricto; la única conversión que queda (int -> float);
 *  tipos de retorno; cómo convertir a propósito ((int), intval, filter_var)
 *  (apartado 2 de los apuntes)
 *
 *  La primera línea de este fichero es declare(strict_types=1): tiene que ser la PRIMERA instrucción
 *  (antes que cualquier otra cosa salvo el <?php y comentarios). Desde ese momento, las llamadas que se
 *  hacen DESDE este fichero no convierten nada: si el tipo no coincide, TypeError.
 *
 *  Cómo trabajar este fichero:
 *   1. Ábrelo al lado de e01-STRICT.php y compara caso por caso.
 *   2. Pon un punto de ruptura en la primera línea de código y avanza con F10.
 *   3. Los comentarios a la derecha indican el resultado esperado.
 */
require_once __DIR__ . '/utilidades.php';

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
// Nada se convierte — TypeError en la línea de la llamada
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php#language.types.declarations.strict
// ENUNCIADO EJEMPLO: los casos de e01 que funcionaban gracias a la conversión.
// NOTA: "5" ya no es un int, 2.0 tampoco (aunque no tenga decimales), true no es 1 y 0 no es false. El
//       mensaje dice qué se esperaba y qué ha llegado ("must be of type int, string given").
// ---------------------------------------------------------------------------------------------
titulo('Sin conversiones');
probar('entero("5")', fn() => entero("5"));                                // "TypeError: entero(): Argument #1 ($n) must be of type int, string given"
probar('entero(2.0)', fn() => entero(2.0));                                // "TypeError: entero(): Argument #1 ($n) must be of type int, float given"
probar('entero(true)', fn() => entero(true));                              // "TypeError: entero(): Argument #1 ($n) must be of type int, bool given"
probar('texto(5)', fn() => texto(5));                                      // "TypeError: texto(): Argument #1 ($s) must be of type string, int given"
probar('logico(0)', fn() => logico(0));                                    // "TypeError: logico(): Argument #1 ($b) must be of type bool, int given"
probar('decimal("3.14")', fn() => decimal("3.14"));                        // "TypeError: decimal(): Argument #1 ($n) must be of type float, string given"
probar('entero(5)', fn() => entero(5));                                    // 5

// ---------------------------------------------------------------------------------------------
// La única excepción — Un int vale donde se espera un float
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php
// ENUNCIADO EJEMPLO: precioConIva(float $precio) con un precio redondo: precioConIva(10).
// NOTA: pasar un int a un parámetro float es seguro (no se pierde nada) y por eso se permite también en
//       modo estricto: llega como 10.0. Al revés (float a int) no: se podrían perder los decimales.
// ---------------------------------------------------------------------------------------------
titulo('int a float');

function precioConIva(float $precio): float
{
    return round($precio * 1.21, 2);
}

probar('decimal(5)', fn() => decimal(5));                                  // 5.0
probar('precioConIva(10)', fn() => precioConIva(10));                      // 12.1
probar('entero(10.0)', fn() => entero(10.0));                              // "TypeError: entero(): Argument #1 ($n) must be of type int, float given"

// ---------------------------------------------------------------------------------------------
// Tipos de retorno — También se comprueban
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php#language.types.declarations.return-only
// ENUNCIADO EJEMPLO: media(int ...$notas): int devuelve la división, que puede tener decimales.
// NOTA: el return se comprueba con el modo del fichero donde está la FUNCIÓN. Aquí, estricto: un float donde
//       se prometió int es un TypeError (en e01 se habría truncado con un aviso). La solución no es quitar el
//       tipo, sino decidir: ¿la media es float? ¿se redondea? (intdiv, round).
// ---------------------------------------------------------------------------------------------
titulo('Tipos de retorno');

function media(int ...$notas): int
{
    return array_sum($notas) / count($notas);
}

function mediaBien(int ...$notas): float
{
    return array_sum($notas) / count($notas);
}

probar('media(6, 8)', fn() => media(6, 8));                                // 7
probar('media(6, 7)', fn() => media(6, 7));                                // "TypeError: media(): Return value must be of type int, float returned"
probar('mediaBien(6, 7)', fn() => mediaBien(6, 7));                        // 6.5

// ---------------------------------------------------------------------------------------------
// Funciones de PHP — También son estrictas
// ENLACE: https://www.php.net/manual/es/function.str-repeat.php
// ENUNCIADO EJEMPLO: dibujar una barra de asteriscos con str_repeat("*", $n) cuando $n viene de un texto.
// NOTA: el modo estricto afecta también a las llamadas a funciones de PHP desde este fichero: strlen(123),
//       str_repeat("*", "3"), htmlspecialchars(5) o number_format("3.5") son TypeError. Los operadores
//       (+, ., ==) y echo NO cambian: "5" + 3 sigue siendo 8.
// ---------------------------------------------------------------------------------------------
titulo('Funciones de PHP');
probar('strlen(123)', fn() => strlen(123));                                // "TypeError: strlen(): Argument #1 ($string) must be of type string, int given"
probar('str_repeat("*", "3")', fn() => str_repeat("*", "3"));              // "TypeError: str_repeat(): Argument #2 ($times) must be of type int, string given"
probar('htmlspecialchars(5)', fn() => htmlspecialchars(5));                // "TypeError: htmlspecialchars(): Argument #1 ($string) must be of type string, int given"
probar('number_format("3.5")', fn() => number_format("3.5"));              // "TypeError: number_format(): Argument #1 ($num) must be of type float, string given"
probar('round("2.5")', fn() => round("2.5"));                              // "TypeError: round(): Argument #1 ($num) must be of type int|float, string given"
probar('"5" + 3 (un operador)', fn() => "5" + 3);                          // 8
probar('"5" == 5', fn() => "5" == 5);                                      // true
probar('"Nota: " . 7 (concatenar)', fn() => "Nota: " . 7);                 // "Nota: 7"

// ---------------------------------------------------------------------------------------------
// Convertir a propósito — (int), intval() y filter_var()
// ENLACE: https://www.php.net/manual/es/function.filter-var.php
// ENUNCIADO EJEMPLO: el número de unidades llega como texto ("12", "12abc", "") y hay que pasarlo a entero().
// NOTA: en modo estricto hay que convertir de forma EXPLÍCITA. (int) e intval() nunca fallan: "12abc" da 12 y
//       "abc" da 0, así que esconden errores. filter_var(..., FILTER_VALIDATE_INT) devuelve el int o false:
//       permite avisar al usuario. Es la forma recomendada para datos de formularios.
// ---------------------------------------------------------------------------------------------
titulo('Convertir a propósito');
$escritos = ['12', '12abc', 'abc', '', ' 7 ', '3.5'];
$conCast = [];
$conFiltro = [];
foreach ($escritos as $escrito) {
    $conCast[$escrito] = (int) $escrito;
    $conFiltro[$escrito] = filter_var($escrito, FILTER_VALIDATE_INT);
}
ver('(int)', $conCast);                                                    // [12 => 12, '12abc' => 12, 'abc' => 0, '' => 0, ' 7 ' => 7, '3.5' => 3]
ver('filter_var', $conFiltro);                                             // [12 => 12, '12abc' => false, 'abc' => false, '' => false, ' 7 ' => 7, '3.5' => false]
probar('entero((int) "12")', fn() => entero((int) "12"));                  // 12
probar('texto((string) 5)', fn() => texto((string) 5));                    // "5"
probar('decimal((float) "3.14")', fn() => decimal((float) "3.14"));        // 3.14
