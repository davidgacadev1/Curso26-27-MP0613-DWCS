<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Un plato de la carta. Eloquent relaciona la clase Plato con la tabla "platos".
 */
class Plato extends Model
{
    use HasFactory;

    /** Campos que se pueden rellenar con Plato::create($datos) */
    protected $fillable = ['nombre', 'tipo', 'precio', 'vegano'];

    /** Conversión de tipos al leer de la base de datos */
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'vegano' => 'boolean',
        ];
    }

    /** Precio con formato español: "16,50 €" */
    public function precioFormateado(): string
    {
        return number_format((float) $this->precio, 2, ',', '.') . ' €';
    }
}
