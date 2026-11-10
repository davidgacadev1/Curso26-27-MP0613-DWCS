<?php

namespace App\Repository;

use App\Entity\Plato;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Consultas de los platos. Symfony lo inyecta donde se pida (autowiring).
 *
 * @extends ServiceEntityRepository<Plato>
 */
class PlatoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plato::class);
    }

    /**
     * La carta agrupada por tipo, en el orden entrante, principal, postre.
     *
     * @return array<string, array{titulo: string, platos: Plato[]}>
     */
    public function cartaPorTipo(): array
    {
        $carta = [];
        foreach (\App\Enum\TipoPlato::cases() as $tipo) {
            $platos = $this->findBy(['tipo' => $tipo], ['nombre' => 'ASC']);
            if ($platos) {
                $carta[$tipo->value] = ['titulo' => $tipo->titulo(), 'platos' => $platos];
            }
        }
        return $carta;
    }
}
