<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $streetNumberExtension = null;

    #[ORM\Column(length: 255)]
    private ?string $streetType = null;

    #[ORM\Column(length: 100)]
    private ?string $streetName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complement = null;

    #[ORM\Column(length: 10)]
    private ?string $postalcode = null;

    #[ORM\Column(length: 200)]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    private ?User $author = null;

    #[ORM\Column(nullable: true)]
    private ?bool $selected = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreetNumberExtension(): ?string
    {
        return $this->streetNumberExtension;
    }

    public function setStreetNumberExtension(?string $streetNumberExtension): self
    {
        $this->streetNumberExtension = $streetNumberExtension;

        return $this;
    }

    public function getStreetType(): ?string
    {
        return $this->streetType;
    }

    public function setStreetType(string $streetType): self
    {
        $this->streetType = $streetType;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): self
    {
        $this->streetName = $streetName;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTime();

        return $this;
    }

    public function getauthor(): ?User
    {
        return $this->author;
    }

    public function setauthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function isSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(?bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }
}
