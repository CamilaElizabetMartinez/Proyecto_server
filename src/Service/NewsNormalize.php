<?php

namespace App\Service;

use App\Entity\ImageNews;
use App\Entity\News;
use Symfony\Component\HttpFoundation\UrlHelper;

class NewsNormalize
{
    private $urlHelper;

    public function __construct(UrlHelper $constructorDeUrl)
    {
        $this->urlHelper = $constructorDeUrl;
    }

    /**
     * Normalize an News.
     *
     * @param News
     *
     * @return array|null
     */
    public function newsNormalize(News $newsEntity): ?array
    {
        $img_file = '';
        if ($imageNews->getImgFile()) {
            $img_file = $this->urlHelper->getAbsoluteUrl('/media/img/news/'.$imageNews->getImgFile());
        }

        return [
            'id' => $news->getId(),
            'title' => $news->getTitle(),
            'slug' => $news->getslug(),
            'subtitile' => $news->getSubtitle(),
            'description' => $news->getDescription(),
            'img_file' => $img_file,
        ];
    }

};