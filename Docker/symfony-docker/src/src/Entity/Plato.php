<?php

namespace App\Entity;

use App\Enum\TipoPlato;
use App\Repository\PlatoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un plato de la carta. Los atributos #[ORM\...] describen la tabla (Doctrine) y
 * los #[Assert\...], las reglas de validación (formularios y API).
 */
#[ORM\Entity(repositoryClass: PlatoRepository::class)]
#[UniqueEntity('nombre', message: 'Ya hay un plato con ese nombre.')]
class Plato
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $nombre = null;

    #[ORM\Column(enumType: TipoPlato::class)]
    #[Assert\NotNull]
    private ?TipoPlato $tipo = null;

    // DECIMAL: Doctrine lo devuelve como string para no perder precisión
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: 'El precio no puede ser negativo.')]
    #[Assert\LessThan(1000)]
    private ?string $precio = null;

    #[ORM\Column]
    private bool $vegano = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getTipo(): ?TipoPlato
    {
        return $this->tipo;
    }

    public function setTipo(TipoPlato $tipo): static
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): static
    {
        $this->precio = $precio;
        return $this;
    }

    public function isVegano(): bool
    {
        return $this->vegano;
    }

    public function setVegano(bool $vegano): static
    {
        $this->vegano = $vegano;
        return $this;
    }
}
