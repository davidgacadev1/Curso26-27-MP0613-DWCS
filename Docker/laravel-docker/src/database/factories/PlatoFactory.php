<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Platos inventados para las pruebas: Plato::factory()->count(3)->create()
 *
 * @extends Factory<\App\Models\Plato>
 */
class PlatoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => ucfirst($this->faker->unique()->words(3, true)),
            'tipo'   => $this->faker->randomElement(['entrante', 'principal', 'postre']),
            'precio' => $this->faker->randomFloat(2, 3, 25),
            'vegano' => $this->faker->boolean(25),
        ];
    }
}
