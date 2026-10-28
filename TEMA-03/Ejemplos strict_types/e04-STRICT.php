<?php
declare(strict_types=1);
/*
 * e04-STRICT.php
 *  STRICT_TYPES (4): DATOS DE FUERA EN MODO ESTRICTO
 *  Formularios ($_GET / $_POST siempre son textos), explode, salida con htmlspecialchars y number_format,
 *  y lo que strict_types NO cambia (== frente a ===, in_array)
 *  (apartado 4 de los apuntes)
 *
 *  En una página web casi todo llega como texto: $_GET, $_POST, $_COOKIE, una línea de un fichero, el resultado
 *  de explode() o de preg_match(). En modo estricto ya no se puede pasar "tal cual" a funciones con tipos: hay
 *  que validar y convertir UNA VEZ, al principio, y a partir de ahí trabajar con tipos de verdad. Es más
 *  trabajo... y es exactamente lo que hay que hacer de todas formas para no fiarse del usuario.
 *
 *  Cómo trabajar este fichero:
 *   1. Pon un punto de ruptura en la primera línea de código y ejecuta en modo depuración.
 *   2. Mira en VARIABLES el tipo de cada dato antes y después de leerPedido().
 *   3. Los comentarios a la derecha indican el resultado esperado.
 */
require_once __DIR__ . '/utilidades.php';

/** Total de un pedido: precio por unidad (float) x unidades (int), con envío opcional (bool). */
function totalPedido(float $precio, int $unidades, bool $conEnvio): float
{
    return round($precio * $unidades + ($conEnvio ? 3.95 : 0.0), 2);
}

// ---------------------------------------------------------------------------------------------
// $_GET y $_POST — Siempre son textos (o arrays de textos)
// ENLACE: https://www.php.net/manual/es/reserved.variables.get.php
// ENUNCIADO EJEMPLO: pedido.php?precio=2.50&unidades=3&envio=on llega del formulario de la tienda.
// NOTA: aquí rellenamos $_GET a mano con lo que mandaría el navegador. Incluso "3" o "2.50" son string: en
//       modo estricto totalPedido($_GET['precio'], ...) es un TypeError. Un checkbox marcado llega como "on" y
//       uno sin marcar NO llega.
// ---------------------------------------------------------------------------------------------
titulo('Datos de un formulario');
$_GET = ['precio' => '2.50', 'unidades' => '3', 'envio' => 'on'];
ver('tipos que llegan', array_map('gettype', $_GET));                      // ['precio' => 'string', 'unidades' => 'string', 'envio' => 'string']
probar('totalPedido con $_GET tal cual', fn() => totalPedido($_GET['precio'], $_GET['unidades'], $_GET['envio'])); // "TypeError: totalPedido(): Argument #1 ($precio) must be of type float, string given"

// ---------------------------------------------------------------------------------------------
// Validar y convertir una vez — filter_var y valores por defecto
// ENLACE: https://www.php.net/manual/es/filter.filters.validate.php
// ENUNCIADO EJEMPLO: leerPedido() comprueba los datos del formulario y devuelve [datos con su tipo, errores].
// NOTA: filter_var con FILTER_VALIDATE_INT / FILTER_VALIDATE_FLOAT devuelve el número con su tipo o false. Se
//       admite la coma decimal cambiándola por punto. El checkbox se convierte en bool con isset(). Después,
//       totalPedido() recibe tipos correctos y no hace falta ninguna conversión más.
// ---------------------------------------------------------------------------------------------
titulo('Validar y convertir');

function leerPedido(array $datos): array
{
    $errores = [];
    $precio = filter_var(str_replace(',', '.', (string) ($datos['precio'] ?? '')), FILTER_VALIDATE_FLOAT,
        ['options' => ['min_range' => 0.01]]);
    if ($precio === false) {
        $errores[] = 'El precio debe ser un número mayor que 0.';
    }
    $unidades = filter_var($datos['unidades'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
    if ($unidades === false) {
        $errores[] = 'Las unidades deben ser un entero de 1 a 99.';
    }
    $pedido = ['precio' => $precio, 'unidades' => $unidades, 'envio' => isset($datos['envio'])];
    return [$pedido, $errores];
}

[$pedido, $errores] = leerPedido($_GET);
ver('pedido', $pedido);                                                    // ['precio' => 2.5, 'unidades' => 3, 'envio' => true]
ver('total', totalPedido($pedido['precio'], $pedido['unidades'], $pedido['envio'])); // 11.45
[$malo, $errores] = leerPedido(['precio' => '2,5', 'unidades' => '3 cajas']);
ver('con coma y "3 cajas"', $malo);                                        // ['precio' => 2.5, 'unidades' => false, 'envio' => false]
ver('errores', $errores);                                                  // ['Las unidades deben ser un entero de 1 a 99.']

// ---------------------------------------------------------------------------------------------
// explode y compañía — Los trozos también son textos
// ENLACE: https://www.php.net/manual/es/function.explode.php
// ENUNCIADO EJEMPLO: las notas de un alumno llegan como "7;5;9" y hay que calcular la media con una función
//                    que recibe int.
// NOTA: explode() devuelve textos. array_map('intval', ...) los convierte (sin validar: "x" sería 0);
//       si hay que validar, mejor un bucle con filter_var.
// ---------------------------------------------------------------------------------------------
titulo('explode');

function mediaNotas(int ...$notas): float
{
    return round(array_sum($notas) / count($notas), 2);
}

$texto = '7;5;9';
$trozos = explode(';', $texto);
ver('trozos', $trozos);                                                    // ['7', '5', '9']
probar('mediaNotas(...$trozos)', fn() => mediaNotas(...$trozos));          // "TypeError: mediaNotas(): Argument #1 must be of type int, string given"
ver('mediaNotas(...array_map("intval", $trozos))', mediaNotas(...array_map('intval', $trozos))); // 7.0

// ---------------------------------------------------------------------------------------------
// La salida — htmlspecialchars, number_format, str_repeat
// ENLACE: https://www.php.net/manual/es/function.htmlspecialchars.php
// ENUNCIADO EJEMPLO: escribir en la página las unidades (int) y el total (float) del pedido.
// NOTA: htmlspecialchars() solo acepta string: htmlspecialchars($pedido['unidades']) es un TypeError en modo
//       estricto. La función e() de siempre se escribe con mixed y (string) dentro, y sirve para todo.
//       number_format() quiere un float (un int también vale: int -> float).
// ---------------------------------------------------------------------------------------------
titulo('La salida');

function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

probar('htmlspecialchars($pedido["unidades"])', fn() => htmlspecialchars($pedido['unidades'])); // "TypeError: htmlspecialchars(): Argument #1 ($string) must be of type string, int given"
ver('e($pedido["unidades"])', e($pedido['unidades']));                     // "3"
ver('number_format(12.5, 2, ",", ".")', number_format(12.5, 2, ',', '.')); // "12,50"
ver('number_format(1200, 2, ",", ".")', number_format(1200, 2, ',', '.')); // "1.200,00"
probar('str_repeat("*", $_GET["unidades"])', fn() => str_repeat('*', $_GET['unidades'])); // "TypeError: str_repeat(): Argument #2 ($times) must be of type int, string given"
ver('str_repeat("*", $pedido["unidades"])', str_repeat('*', $pedido['unidades'])); // "***"

// ---------------------------------------------------------------------------------------------
// Lo que NO cambia — Comparaciones con == e in_array
// ENLACE: https://www.php.net/manual/es/language.operators.comparison.php
// ENUNCIADO EJEMPLO: comprobar si el cupón escrito ("1e1") es el cupón "10".
// NOTA: strict_types solo afecta a los TIPOS de parámetros, retornos y propiedades. Los operadores siguen igual:
//       "1e1" == "10" es true (los dos "parecen" el número 10). Para comparar sin conversiones se usa === y, en
//       in_array / array_search, el tercer parámetro true. Es otra cosa distinta, aunque también se llame
//       "estricta".
// ---------------------------------------------------------------------------------------------
titulo('Lo que no cambia');
ver('"1e1" == "10"', "1e1" == "10");                                       // true
ver('"1e1" === "10"', "1e1" === "10");                                     // false
ver('in_array("1e1", ["10"])', in_array("1e1", ["10"]));                   // true
ver('in_array("1e1", ["10"], true)', in_array("1e1", ["10"], true));       // false
ver('"5" + "3" (operador)', "5" + "3");                                    // 8
