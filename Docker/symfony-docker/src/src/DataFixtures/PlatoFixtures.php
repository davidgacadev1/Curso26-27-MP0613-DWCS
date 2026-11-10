<?php

namespace App\DataFixtures;

use App\Entity\Plato;
use App\Enum\TipoPlato;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * La carta del Bar Baio: php bin/console doctrine:fixtures:load
 * (¡vacía antes las tablas! con --append añade sin borrar)
 */
class PlatoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $platos = [
            ['Pulpo á feira', TipoPlato::Principal, '16.50', false],
            ['Pimientos de Padrón', TipoPlato::Entrante, '6.00', true],
            ['Empanada de zamburiñas', TipoPlato::Entrante, '9.80', false],
            ['Caldo gallego', TipoPlato::Entrante, '7.20', false],
            ['Merluza á galega', TipoPlato::Principal, '18.90', false],
            ['Tarta de Santiago', TipoPlato::Postre, '5.50', false],
        ];
        foreach ($platos as [$nombre, $tipo, $precio, $vegano]) {
            $manager->persist((new Plato())->setNombre($nombre)->setTipo($tipo)->setPrecio($precio)->setVegano($vegano));
        }
        $manager->flush();
    }
}
