<?php

namespace App\Controller\superAdmin;

use App\Repository\NewsRepository;
use App\Service\NewsNormalize;
/**
 * @Route("/api/super_admin/news", name="api_news_super_admin")
 */

class ApiNewsController extends AbstractController
{
    /**
    * @Route(
    *      "",
    *      name="cget",
    *      methods={"GET"}
    * )
    * @IsGranted("ROLE_SUPER_ADMIN")
    */
    
    public function Index(
        NewsRepository $newsRepository,
        NewsNormalize $newsNormalize
    ): Response {
        
        //Recupero todas las noticias
        $newsEntities = $newsRepository->findAll();

        //Declaro un array vacio
        $newsNormalized = [];

    }

}
