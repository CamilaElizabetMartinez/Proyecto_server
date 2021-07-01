<?php

namespace App\Entity;

use App\Repository\ImageNewsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageNewsRepository::class)
 */
class ImageNews
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=news::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $news;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img_file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNews(): ?news
    {
        return $this->news;
    }

    public function setNews(?news $news): self
    {
        $this->news = $news;

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
