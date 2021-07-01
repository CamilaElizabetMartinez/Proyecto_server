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
     * @ORM\ManyToOne(targetEntity=product::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img_file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?product
    {
        return $this->product;
    }

    public function setProduct(?product $product): self
    {
        $this->product = $product;

        return $this;
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
}
