<?php

namespace App\Entity;

use App\Repository\PropertyImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PropertyImageRepository::class)]
class PropertyImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['property:list'])]
    private ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'propertyImages')]
    private ?Property $property_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $setOrder = null;

    #[Groups(['property:list'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altText = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getPropertyId(): ?Property
    {
        return $this->property_id;
    }

    public function setPropertyId(?Property $property_id): static
    {
        $this->property_id = $property_id;

        return $this;
    }

    public function getSetOrder(): ?int
    {
        return $this->setOrder;
    }

    public function setSetOrder(?int $setOrder): static
    {
        $this->setOrder = $setOrder;

        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(?string $altText): static
    {
        $this->altText = $altText;

        return $this;
    }
}
