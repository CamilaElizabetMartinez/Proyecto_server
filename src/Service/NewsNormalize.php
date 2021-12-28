<?php

namespace App\Service;

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
    {   //Declaramos una variable con strin vacio
        $imgPrincipal = '';

        //Si la noticia obtiene imgPrincipal se obtiene la url absoluta
        //Y se guarda en la variable vacia
        if ($newsEntity->getImgPrincipal()) {
            $imgPrincipal = $this->urlHelper->getAbsoluteUrl('/media/img/news/'.$newsEntity->getImgPrincipal());
        }

        //Declaramos un array vacio.
        $newsImage = [];

        //Por cada entidad de noticia
        //obtengo la url absoluta de cada imagen y lo guardo en un array vacio
        foreach($newsEntity->getImageNews() as $imageNewsEntity) {
            array_push($newsImage, 
                $this->urlHelper->getAbsoluteUrl('/media/img/news/'.$imageNewsEntity->getImgFile())
            );
        }
        
        //Retorna los datos en formato JSON
        return [
            'id' => $newsEntity->getId(),
            'title' => $newsEntity->getTitle(),
            'slug' => $newsEntity->getslug(),
            'creation_timestamp' => $newsEntity->getCreationTimestamp(),
            'subtitle' => $newsEntity->getSubtitle(),
            'description' => $newsEntity->getDescription(),
            'img_principal'=> $imgPrincipal,
            'images' => $newsImage,
        ];
    }

};