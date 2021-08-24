<?php

namespace App\Entity;

use App\Repository\ImageProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageProductRepository::class)
 */
class ImageProduct
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
    private $img_file;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="imageProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImgFile(): ?string
    {
        return $this->img_file;
    }

    public function setImgFile(string $img_file): self
    {
        $this->img_file = $img_file;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
