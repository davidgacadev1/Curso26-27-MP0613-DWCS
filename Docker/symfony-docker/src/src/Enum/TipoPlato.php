<?php

namespace App\Enum;

/**
 * Tipos de plato. Doctrine guarda el valor ('entrante'...) y devuelve el caso del enum.
 */
enum TipoPlato: string
{
    case Entrante = 'entrante';
    case Principal = 'principal';
    case Postre = 'postre';

    /** Título para la carta: "Entrantes", "Principales", "Postres" */
    public function titulo(): string
    {
        return match ($this) {
            self::Entrante => 'Entrantes',
            self::Principal => 'Principales',
            self::Postre => 'Postres',
        };
    }
}
