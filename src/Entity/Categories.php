<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type :'integer')]
    private $categoryOrder;

    #[ORM\OneToMany(mappedBy: 'categories', targetEntity: Candys::class)]
    private Collection $candys;

    public function __construct()
    {
        $this->candys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategoryOrder(): ?int
    {
        return $this->categoryOrder;
    }

    public function setCategoryOrder(int $categoryOrder): self
    {
        $this->categoryOrder = $categoryOrder;
        return $this;
    }

    /**
     * @return Collection<int, Candys>
     */
    public function getCandys(): Collection
    {
        return $this->candys;
    }

    public function addCandy(Candys $candy): static
    {
        if (!$this->candys->contains($candy)) {
            $this->candys->add($candy);
            $candy->setCategories($this);
        }

        return $this;
    }

    public function removeCandy(Candys $candy): static
    {
        if ($this->candys->removeElement($candy)) {
            // set the owning side to null (unless already changed)
            if ($candy->getCategories() === $this) {
                $candy->setCategories(null);
            }
        }

        return $this;
    }
}
