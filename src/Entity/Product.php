<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img_principal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $weight;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=ImageProduct::class, mappedBy="product", cascade={"persist", "remove"})
     */
    private $imageProducts;

    public function __construct()
    {
        $this->imageProducts = new ArrayCollection();
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

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImgPrincipal(): ?string
    {
        return $this->img_principal;
    }

    public function setImgPrincipal(string $img_principal): self
    {
        $this->img_principal = $img_principal;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|ImageProduct[]
     */
    public function getImageProducts(): Collection
    {
        return $this->imageProducts;
    }

    public function addImageProduct(ImageProduct $imageProduct): self
    {
        if (!$this->imageProducts->contains($imageProduct)) {
            $this->imageProducts[] = $imageProduct;
            $imageProduct->setProduct($this);
        }

        return $this;
    }

    public function removeImageProduct(ImageProduct $imageProduct): self
    {
        if ($this->imageProducts->removeElement($imageProduct)) {
            // set the owning side to null (unless already changed)
            if ($imageProduct->getProduct() === $this) {
                $imageProduct->setProduct(null);
            }
        }

        return $this;
    }

}
