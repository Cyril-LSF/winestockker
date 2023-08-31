<?php

namespace App\Entity;

use App\Repository\BottleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BottleRepository::class)]
class Bottle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $year = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $origin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $enbottler = null;

    #[ORM\ManyToOne(inversedBy: 'bottles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Cellar::class, inversedBy: 'bottles')]
    private Collection $cellars;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $price = null;

    #[ORM\OneToMany(mappedBy: 'bottle', targetEntity: Quantity::class, orphanRemoval: true)]
    private Collection $quantities;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'bottles')]
    private Collection $categories;

    private ?bool $disabled = false;

    public function __construct()
    {
        $this->cellars = new ArrayCollection();
        $this->quantities = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getVine(): ?string
    {
        return $this->vine;
    }

    public function setVine(?string $vine): self
    {
        $this->vine = $vine;

        return $this;
    }

    public function getEnbottler(): ?string
    {
        return $this->enbottler;
    }

    public function setEnbottler(?string $enbottler): self
    {
        $this->enbottler = $enbottler;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Cellar>
     */
    public function getCellars(): Collection
    {
        return $this->cellars;
    }

    public function addCellar(Cellar $cellar): self
    {
        if (!$this->cellars->contains($cellar)) {
            $this->cellars->add($cellar);
        }

        return $this;
    }

    public function removeCellar(Cellar $cellar): self
    {
        $this->cellars->removeElement($cellar);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Quantity>
     */
    public function getQuantities(): Collection
    {
        return $this->quantities;
    }

    public function addQuantity(Quantity $quantity): self
    {
        if (!$this->quantities->contains($quantity)) {
            $this->quantities->add($quantity);
            $quantity->setBottle($this);
        }

        return $this;
    }

    public function removeQuantity(Quantity $quantity): self
    {
        if ($this->quantities->removeElement($quantity)) {
            // set the owning side to null (unless already changed)
            if ($quantity->getBottle() === $this) {
                $quantity->setBottle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addBottle($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeBottle($this);
        }

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(?bool $disabled): self
    {
        $this->disabled = $disabled;
        return $this;
    }

}
