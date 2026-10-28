<?php
/*
 * lib/tienda-sin-strict.php (para e03-STRICT.php)
 *  Funciones de una "biblioteca" escrita SIN declare(strict_types=1).
 */

/** Precio de N unidades. */
function totalSinStrict(float $precio, int $unidades): float
{
    return $precio * $unidades;
}

/** Devuelve la mitad... prometiendo un int (si es impar, el return no es un int). */
function mitadSinStrict(int $n): int
{
    return $n / 2;
}

/** Llama a otra función DESDE este fichero: esa llamada sigue las reglas de este fichero (coercitivo). */
function llamarSinStrict(callable $funcion, mixed $valor): mixed
{
    return $funcion($valor);
}

/** Un carrito con una propiedad tipada; el método asigna lo que recibe sin convertirlo. */
class CarritoSinStrict
{
    public int $unidades = 0;

    public function poner(mixed $valor): void
    {
        $this->unidades = $valor;
    }
}
