<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['property:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(['message' => 'The title is required.'])]
    #[Assert\Length(['min' => 5, 'minMessage' => 'Title must be at least 5 characters.'])]
    #[Groups(['property:list'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(['message' => 'Description is required.'])]
    #[Groups(['property:list'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(['message' => 'Price is required.'])]
    #[Assert\Positive(['message' => 'The price must be greater than zero.'])]
    #[Assert\Type(['type' => 'numeric', 'message' => 'The price must be a number.'])]
    #[Groups(['property:list'])]
    private ?string $price = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(['message' => 'Type is required.'])]
    #[Groups(['property:list'])]
    private ?string $type = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(['message' => 'Status is required.'])]
    #[Assert\Choice([
        'choices' => ['available', 'sold', 'rented'],
        'message' => 'Invalid status.'
    ])]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    private ?User $user_id = null;

    #[ORM\OneToMany(targetEntity: Visit::class, mappedBy: 'property_id')]
    private Collection $visits;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(['message' => 'Slug is required.'])]
    #[Groups(['property:list'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Publish status is required.')]
    private ?bool $isPublished = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainImage = null;

    #[ORM\OneToMany(mappedBy: 'property_id', targetEntity: PropertyImage::class, cascade: ['remove'])]
    #[Groups(['property:list'])]
    private Collection $propertyImages;

    public function __construct()
    {
        $this->visits = new ArrayCollection();
        $this->propertyImages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return Collection<int, Visit>
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): static
    {
        if (!$this->visits->contains($visit)) {
            $this->visits->add($visit);
            $visit->setPropertyId($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): static
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getPropertyId() === $this) {
                $visit->setPropertyId(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(?string $mainImage): static
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Collection<int, PropertyImage>
     */
    public function getPropertyImages(): Collection
    {
        return $this->propertyImages;
    }

    public function addPropertyImage(PropertyImage $propertyImage): static
    {
        if (!$this->propertyImages->contains($propertyImage)) {
            $this->propertyImages->add($propertyImage);
            $propertyImage->setPropertyId($this);
        }

        return $this;
    }

    public function removePropertyImage(PropertyImage $propertyImage): static
    {
        if ($this->propertyImages->removeElement($propertyImage)) {
            // set the owning side to null (unless already changed)
            if ($propertyImage->getPropertyId() === $this) {
                $propertyImage->setPropertyId(null);
            }
        }

        return $this;
    }
}
