<?php

namespace Database\Seeders;

use App\Models\Plato;
use Illuminate\Database\Seeder;

/**
 * La carta de verdad del Bar Baio: php artisan db:seed --class=PlatoSeeder
 */
class PlatoSeeder extends Seeder
{
    public function run(): void
    {
        $platos = [
            ['Pulpo á feira', 'principal', 16.50, false],
            ['Pimientos de Padrón', 'entrante', 6.00, true],
            ['Empanada de zamburiñas', 'entrante', 9.80, false],
            ['Caldo gallego', 'entrante', 7.20, false],
            ['Merluza á galega', 'principal', 18.90, false],
            ['Tarta de Santiago', 'postre', 5.50, false],
        ];
        foreach ($platos as [$nombre, $tipo, $precio, $vegano]) {
            // updateOrCreate: se puede ejecutar varias veces sin duplicar platos
            Plato::updateOrCreate(['nombre' => $nombre], compact('tipo', 'precio', 'vegano'));
        }
    }
}
