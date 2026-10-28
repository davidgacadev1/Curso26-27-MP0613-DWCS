<?php
declare(strict_types=1);
/*
 * e03-STRICT.php
 *  STRICT_TYPES (3): ¿A QUÉ AFECTA? EL FICHERO QUE LLAMA, EL QUE DEVUELVE Y EL QUE ASIGNA
 *  Ficheros con y sin strict_types mezclados (lib/tienda-sin-strict.php y lib/tienda-con-strict.php),
 *  tipos de retorno, propiedades tipadas y callbacks que llama PHP (array_map, usort)
 *  (apartado 3 de los apuntes)
 *
 *  strict_types es una opción de CADA FICHERO, no del programa entero, y no se hereda con include/require.
 *  Las reglas son tres, y aquí se comprueban todas:
 *   - argumentos: manda el fichero desde el que se hace la LLAMADA;
 *   - return: manda el fichero donde está la FUNCIÓN;
 *   - propiedades tipadas: manda el fichero donde está la ASIGNACIÓN ($obj->prop = ...).
 *
 *  Cómo trabajar este fichero:
 *   1. Abre también los dos ficheros de lib/ (solo cambia su primera línea).
 *   2. Pon un punto de ruptura en la primera línea de código y entra con F11 en las llamadas.
 *   3. Los comentarios a la derecha indican el resultado esperado.
 */
require_once __DIR__ . '/utilidades.php';
require_once __DIR__ . '/lib/tienda-sin-strict.php';
require_once __DIR__ . '/lib/tienda-con-strict.php';

// ---------------------------------------------------------------------------------------------
// Argumentos — Manda el fichero que llama
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php#language.types.declarations.strict
// ENUNCIADO EJEMPLO: este fichero (estricto) usa una biblioteca escrita sin strict_types.
// NOTA: aunque totalSinStrict() esté en un fichero SIN strict_types, la llamada se hace desde aquí (CON
//       strict_types): "3" no se convierte. Y al revés: si la llamada se hace desde el fichero sin strict
//       (llamarSinStrict hace la llamada por nosotros), se convierte aunque la función sea "estricta".
// ---------------------------------------------------------------------------------------------
titulo('Argumentos');
probar('totalSinStrict(2.5, "3") desde aquí', fn() => totalSinStrict(2.5, "3")); // "TypeError: totalSinStrict(): Argument #2 ($unidades) must be of type int, string given"
probar('totalSinStrict(2.5, 3)', fn() => totalSinStrict(2.5, 3));          // 7.5
probar('llamarSinStrict("mitadConStrict", "8")', fn() => llamarSinStrict('mitadConStrict', "8")); // 4
probar('llamarConStrict("mitadSinStrict", "8")', fn() => llamarConStrict('mitadSinStrict', "8")); // "TypeError: mitadSinStrict(): Argument #1 ($n) must be of type int, string given"

// ---------------------------------------------------------------------------------------------
// return — Manda el fichero de la función
// ENLACE: https://www.php.net/manual/es/language.types.declarations.php#language.types.declarations.return-only
// ENUNCIADO EJEMPLO: mitad(5) en las dos bibliotecas; las dos prometen devolver un int.
// NOTA: llamamos a las dos desde el mismo sitio (este fichero), pero el return se comprueba con las reglas del
//       fichero donde está escrita cada función: la de la biblioteca sin strict trunca 2.5 a 2 con un aviso
//       Deprecated; la estricta da TypeError. ¡Ojo! Si un manejador de errores convierte ese aviso en
//       excepción (como hace probar()), PHP lo trata como un fallo de la conversión y da también TypeError.
// ---------------------------------------------------------------------------------------------
titulo('return');
probar('mitadSinStrict(4)', fn() => mitadSinStrict(4));                    // 2
probarConAvisos('mitadSinStrict(5)', fn() => mitadSinStrict(5));           // "2 (aviso: Implicit conversion from float 2.5 to int loses precision)"
probar('mitadSinStrict(5) con el aviso como excepción', fn() => mitadSinStrict(5)); // "TypeError: mitadSinStrict(): Return value must be of type int, float returned"
probar('mitadConStrict(5)', fn() => mitadConStrict(5));                    // "TypeError: mitadConStrict(): Return value must be of type int, float returned"

// ---------------------------------------------------------------------------------------------
// Propiedades tipadas — Manda el fichero de la asignación
// ENLACE: https://www.php.net/manual/es/language.oop5.properties.php#language.oop5.properties.typed-properties
// ENUNCIADO EJEMPLO: un carrito con public int $unidades; las unidades llegan como texto ("7").
// NOTA: poner() hace la asignación dentro de su clase, así que manda el fichero de la clase. Si la hacemos
//       nosotros ($carrito->unidades = "7"), manda este fichero: TypeError aunque la clase sea "no estricta".
// ---------------------------------------------------------------------------------------------
titulo('Propiedades tipadas');
$sin = new CarritoSinStrict();
$con = new CarritoConStrict();
probar('CarritoSinStrict->poner("7")', function () use ($sin) {
    $sin->poner("7");
    return $sin->unidades;
});
probar('CarritoConStrict->poner("7")', function () use ($con) {
    $con->poner("7");
    return $con->unidades;
});
probar('$sin->unidades = "7" desde aquí', function () use ($sin) {
    $sin->unidades = "7";
    return $sin->unidades;
});

// ---------------------------------------------------------------------------------------------
// Callbacks que llama PHP — No son estrictos
// ENLACE: https://www.php.net/manual/es/function.array-map.php
// ENUNCIADO EJEMPLO: las notas llegan como textos (["7", "10", "4"]) y se pasan por una función con int.
// NOTA: cuando es una función de PHP (array_map, usort, array_filter...) la que llama a nuestra función, la
//       llamada no está en ningún fichero nuestro y se hace en modo coercitivo: "7" se convierte en 7 aunque
//       todo el programa tenga strict_types=1. Llamada directamente desde aquí, sí falla.
// ---------------------------------------------------------------------------------------------
titulo('Callbacks');
$notas = ["7", "10", "4"];
$duplicar = fn(int $n): int => $n * 2;
probar('array_map($duplicar, ["7", "10", "4"])', fn() => array_map($duplicar, $notas)); // [14, 20, 8]
probar('$duplicar("7") directamente', fn() => $duplicar("7"));             // "TypeError: {closure}(): Argument #1 ($n) must be of type int, string given"
probar('usort con fn(int $a, int $b)', function () use ($notas) {
    usort($notas, fn(int $a, int $b): int => $a <=> $b);
    return $notas;
});
ver('ojo: usort ordena los textos, pero siguen siendo textos', gettype($notas[0])); // "string"
