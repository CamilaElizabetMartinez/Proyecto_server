<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 */
class News
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img_principal;

    /**
     * @ORM\OneToMany(targetEntity=ImageNews::class, mappedBy="news")
     */
    private $imageNews;

    public function __construct()
    {
        $this->imageNews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImgPrincipal(): ?string
    {
        return $this->img_principal;
    }

    public function setImgPrincipal(?string $img_principal): self
    {
        $this->img_principal = $img_principal;

        return $this;
    }

    /**
     * @return Collection|ImageNews[]
     */
    public function getImageNews(): Collection
    {
        return $this->imageNews;
    }

    public function addImageNews(ImageNews $imageNews): self
    {
        if (!$this->imageNews->contains($imageNews)) {
            $this->imageNews[] = $imageNews;
            $imageNews->setNews($this);
        }

        return $this;
    }

    public function removeImageNews(ImageNews $imageNews): self
    {
        if ($this->imageNews->removeElement($imageNews)) {
            // set the owning side to null (unless already changed)
            if ($imageNews->getNews() === $this) {
                $imageNews->setNews(null);
            }
        }

        return $this;
    }

    
}
