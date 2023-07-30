<?php

namespace App\Entity;

use App\Repository\DataCryptRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataCryptRepository::class)]
class DataCrypt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $iv = null;

    #[ORM\Column(length: 255)]
    private ?string $tag = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?CreditCard $creditCard = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIv(): ?string
    {
        return $this->iv;
    }

    public function setIv(string $iv): self
    {
        $this->iv = $iv;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getCreditCard(): ?CreditCard
    {
        return $this->creditCard;
    }

    public function setCreditCard(CreditCard $creditCard): self
    {
        $this->creditCard = $creditCard;

        return $this;
    }
}
