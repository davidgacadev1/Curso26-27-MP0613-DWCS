<?php
declare(strict_types=1);
/*
 * e03-STRICT_problema_solucion.php
 *  (FICHERO CON LAS SOLUCIONES)
 *  Problemas de: a qué afecta strict_types (llamadas desde cada fichero, retornos, propiedades, callbacks)
 *  (repasa los ejemplos de ../e03-STRICT.php y los ficheros de ../lib/)
 *
 *  Este fichero tiene declare(strict_types=1). Resuelve cada problema donde se indica y ejecútalo en modo
 *  depuración para comprobarlo.
 */
require_once __DIR__ . '/../utilidades.php';
require_once __DIR__ . '/../lib/tienda-sin-strict.php';

$duplicar = fn(int $n): int => $n * 2;

// =============================================================================================
// PROBLEMA 1. Un array_map hecho a mano
//  Escribe aplicar(callable $funcion, array $valores): array que haga lo mismo que array_map pero con un foreach.
//  Comprueba que, con $duplicar y ["7", "10"], array_map funciona y aplicar() no: explica por qué en un
//  comentario. Después escribe aplicarEnteros() que convierta cada valor con intval antes de llamar.
// =============================================================================================
titulo('Problema 1');
// completa aquí tu solución
// ---- SOLUCIÓN ----
// En aplicar() la llamada $funcion($valor) está escrita en ESTE fichero (estricto): "7" no se convierte. En
// array_map la llamada la hace PHP por dentro, en modo coercitivo.
function aplicar(callable $funcion, array $valores): array
{
    $resultado = [];
    foreach ($valores as $clave => $valor) {
        $resultado[$clave] = $funcion($valor);
    }
    return $resultado;
}

function aplicarEnteros(callable $funcion, array $valores): array
{
    return aplicar($funcion, array_map('intval', $valores));
}

probar('array_map', fn() => array_map($duplicar, ["7", "10"]));            // [14, 20]
probar('aplicar', fn() => aplicar($duplicar, ["7", "10"]));                // "TypeError: {closure}(): Argument #1 ($n) must be of type int, string given"
probar('aplicarEnteros', fn() => aplicarEnteros($duplicar, ["7", "10"]));  // [14, 20]

// =============================================================================================
// PROBLEMA 2. Usar una biblioteca sin strict desde un fichero estricto
//  Las líneas de un pedido llegan como textos: [["2.50", "3"], ["1.20", "10"]] (precio, unidades). Calcula el
//  total del pedido con totalSinStrict() de ../lib/tienda-sin-strict.php, convirtiendo los datos aquí.
//  Esperado: 19.5
// =============================================================================================
titulo('Problema 2');
$lineas = [['2.50', '3'], ['1.20', '10']];
// completa aquí tu solución
// ---- SOLUCIÓN ----
$total = 0.0;
foreach ($lineas as [$precio, $unidades]) {
    $total += totalSinStrict((float) $precio, (int) $unidades);
}
ver('total', round($total, 2));                                            // 19.5

// =============================================================================================
// PROBLEMA 3. Una propiedad tipada que se deja poner bien
//  Escribe la clase Termometro con public float $grados = 0.0 y un método fijar(mixed $valor): void que acepte
//  un int, un float o un texto con coma o punto decimal ("21,5", "21.5") y lance InvalidArgumentException con
//  cualquier otra cosa. Recuerda: en este fichero, $this->grados = "21.5" sería un TypeError.
// =============================================================================================
titulo('Problema 3');
// completa aquí tu solución
// ---- SOLUCIÓN ----
class Termometro
{
    public float $grados = 0.0;

    public function fijar(mixed $valor): void
    {
        if (is_string($valor)) {
            $valor = filter_var(str_replace(',', '.', $valor), FILTER_VALIDATE_FLOAT);
        }
        if (!is_int($valor) && !is_float($valor)) {
            throw new InvalidArgumentException('Temperatura no válida');
        }
        $this->grados = $valor;                // int -> float está permitido incluso en modo estricto
    }
}

$termometro = new Termometro();
$lecturas = [];
foreach ([21, '21,5', '19.25', 'calor', true] as $lectura) {
    try {
        $termometro->fijar($lectura);
        $lecturas[] = $termometro->grados;
    } catch (InvalidArgumentException $e) {
        $lecturas[] = $e->getMessage();
    }
}
ver('lecturas', $lecturas);                                                // [21.0, 21.5, 19.25, 'Temperatura no válida', 'Temperatura no válida']

// =============================================================================================
// PROBLEMA 4. Ordenar notas de verdad
//  usort() con fn(int $a, int $b) "funciona" con textos porque el callback lo llama PHP (modo coercitivo), pero
//  el array sigue teniendo textos. Escribe ordenarNotas(array $textos): array que devuelva las notas como int y
//  ordenadas de mayor a menor.
// =============================================================================================
titulo('Problema 4');
// completa aquí tu solución
// ---- SOLUCIÓN ----
function ordenarNotas(array $textos): array
{
    $notas = array_map('intval', $textos);
    rsort($notas);
    return $notas;
}

$ordenadas = ordenarNotas(['7', '10', '4', '9']);
ver('ordenadas', $ordenadas);                                              // [10, 9, 7, 4]
ver('tipo de la primera', get_debug_type($ordenadas[0]));                  // "int"

// =============================================================================================
// PROBLEMA 5. ¿Está bien puesto el declare?
//  Escribe declareBienPuesto(string $codigo): bool que diga si la PRIMERA instrucción del código (después de
//  "<?php" y de los espacios y comentarios /* ... */ o // ...) es declare(strict_types=1);
//  Esperado: true, true, false, false.
// =============================================================================================
titulo('Problema 5');
$codigos = [
    "<?php\ndeclare(strict_types=1);\necho 1;",
    "<?php\n/* cabecera */\n// otra\ndeclare(strict_types=1);",
    "<?php\nrequire 'x.php';\ndeclare(strict_types=1);",
    "<?php\ndeclare(strict_types=0);",
];
// completa aquí tu solución
// ---- SOLUCIÓN ----
function declareBienPuesto(string $codigo): bool
{
    $resto = preg_replace('/^<\?php/', '', $codigo);
    // quitar espacios y comentarios del principio, uno detrás de otro
    $resto = preg_replace('#^(\s+|/\*.*?\*/|//[^\n]*)*#s', '', $resto);
    return (bool) preg_match('/^declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;/', $resto);
}

ver('declareBienPuesto', array_map('declareBienPuesto', $codigos));        // [true, true, false, false]
