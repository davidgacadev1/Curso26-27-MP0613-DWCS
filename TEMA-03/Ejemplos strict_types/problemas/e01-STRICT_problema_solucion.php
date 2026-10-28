<?php
/*
 * e01-STRICT_problema_solucion.php
 *  (FICHERO CON LAS SOLUCIONES)
 *  Problemas de: el modo coercitivo (sin strict_types): qué convierte PHP, qué no y los errores que esconde
 *  (repasa los ejemplos de ../e01-STRICT.php)
 *
 *  Este fichero, como e01, NO tiene declare(strict_types=1). Resuelve cada problema donde se indica y
 *  ejecútalo en modo depuración para comprobarlo.
 */
require_once __DIR__ . '/../utilidades.php';

function entero(int $n): int
{
    return $n;
}

// =============================================================================================
// PROBLEMA 1. ¿En qué se convierte?
//  Escribe trasLlamar(callable $funcion, mixed $valor): string, que llame a la función con el valor y
//  devuelva el tipo del resultado (get_debug_type) o "TypeError" si la llamada falla. Pruébala con entero().
//  Esperado: int, int, TypeError, int, TypeError, TypeError.
// =============================================================================================
titulo('Problema 1');
$valores = ['42', ' 42', '42 años', true, null, ''];
// completa aquí tu solución
// ---- SOLUCIÓN ----
function trasLlamar(callable $funcion, mixed $valor): string
{
    try {
        return get_debug_type($funcion($valor));
    } catch (TypeError) {
        return 'TypeError';
    }
}

$tipos = [];
foreach ($valores as $valor) {
    $tipos[] = trasLlamar('entero', $valor);
}
ver('tipos', $tipos);                                                      // ['int', 'int', 'TypeError', 'int', 'TypeError', 'TypeError']

// =============================================================================================
// PROBLEMA 2. Solo cifras
//  La conversión automática acepta cosas como "1e3" (1000) o true (1), que en un formulario de unidades no
//  tienen sentido. Escribe soloCifras(mixed $valor): ?int que devuelva el entero si $valor es un int o un texto
//  formado SOLO por cifras (sin signo, sin espacios), y null en cualquier otro caso.
// =============================================================================================
titulo('Problema 2');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function soloCifras(mixed $valor): ?int
{
    if (is_int($valor)) {
        return $valor;
    }
    if (is_string($valor) && preg_match('/^\d+$/', $valor)) {
        return (int) $valor;
    }
    return null;
}

$resultados = [];
foreach (['12', '1e3', ' 5', 5, 5.0, true, '007'] as $valor) {
    $resultados[] = soloCifras($valor);
}
ver('soloCifras', $resultados);                                            // [12, NULL, NULL, 5, NULL, NULL, 7]

// =============================================================================================
// PROBLEMA 3. El porcentaje que miente
//  Un compañero escribió porcentajeMal(). Comprueba qué devuelve porcentajeMal(1, 3) y explica en un comentario
//  qué ha pasado. Después escribe porcentaje() con los tipos correctos para que devuelva 33.33 (redondeado a dos
//  decimales) sin ningún aviso.
// =============================================================================================
titulo('Problema 3');

function porcentajeMal(int $parte, int $total): int
{
    return $parte * 100 / $total;
}

probarConAvisos('porcentajeMal(1, 3)', fn() => porcentajeMal(1, 3));       // "33 (aviso: Implicit conversion from float 33.333333333333336 to int loses precision)"
// completa aquí tu solución
// ---- SOLUCIÓN ----
// 100 / 3 = 33.333..., pero la función promete un int: en modo coercitivo PHP lo trunca a 33 y solo da un
// aviso Deprecated. El tipo de retorno correcto es float (y conviene redondear).
function porcentaje(int $parte, int $total): float
{
    return round($parte * 100 / $total, 2);
}

probarConAvisos('porcentaje(1, 3)', fn() => porcentaje(1, 3));             // "33.33"
probarConAvisos('porcentaje(1, 4)', fn() => porcentaje(1, 4));             // "25.0"

// =============================================================================================
// PROBLEMA 4. "false" es verdadero
//  Un parámetro bool convierte cualquier texto no vacío en true: un formulario que envía "false" o "no"
//  activa la opción. Escribe aBool(mixed $valor): ?bool que entienda "1", "true", "on", "yes" (true),
//  "0", "false", "off", "no", "" (false) y devuelva null con cualquier otra cosa. Pista: filter_var con
//  FILTER_VALIDATE_BOOLEAN y la opción FILTER_NULL_ON_FAILURE.
// =============================================================================================
titulo('Problema 4');

function activar(bool $activo): string
{
    return $activo ? 'activado' : 'desactivado';
}

ver('activar("false")', activar("false"));                                 // "activado"
// completa aquí tu solución
// ---- SOLUCIÓN ----
function aBool(mixed $valor): ?bool
{
    return filter_var($valor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
}

$leidos = [];
foreach (['on', 'false', 'no', '0', '', 'quizás'] as $valor) {
    $leidos[$valor] = aBool($valor);
}
ver('aBool', $leidos);                                                     // ['on' => true, 'false' => false, 'no' => false, 0 => false, '' => false, 'quizás' => NULL]
ver('activar(aBool("false"))', activar(aBool('false')));                   // "desactivado"

// =============================================================================================
// PROBLEMA 5. Tipos unión
//  Escribe describir(int|float $numero): string que devuelva el tipo y el valor que ha llegado ("int 3",
//  "float 2.5"). Llámala con los textos "3", "2.5", "1e2" y "abc" (este último debe dar "TypeError").
// =============================================================================================
titulo('Problema 5');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function describir(int|float $numero): string
{
    return get_debug_type($numero) . ' ' . $numero;
}

$descripciones = [];
foreach (['3', '2.5', '1e2', 'abc'] as $texto) {
    try {
        $descripciones[$texto] = describir($texto);
    } catch (TypeError) {
        $descripciones[$texto] = 'TypeError';
    }
}
ver('describir', $descripciones);                                          // [3 => 'int 3', '2.5' => 'float 2.5', '1e2' => 'float 100', 'abc' => 'TypeError']
