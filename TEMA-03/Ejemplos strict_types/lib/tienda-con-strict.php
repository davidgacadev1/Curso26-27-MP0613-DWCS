<?php
declare(strict_types=1);
/*
 * lib/tienda-con-strict.php (para e03-STRICT.php)
 *  Las mismas funciones, en un fichero CON declare(strict_types=1).
 */

function totalConStrict(float $precio, int $unidades): float
{
    return $precio * $unidades;
}

function mitadConStrict(int $n): int
{
    return $n / 2;
}

function llamarConStrict(callable $funcion, mixed $valor): mixed
{
    return $funcion($valor);
}

class CarritoConStrict
{
    public int $unidades = 0;

    public function poner(mixed $valor): void
    {
        $this->unidades = $valor;
    }
}
