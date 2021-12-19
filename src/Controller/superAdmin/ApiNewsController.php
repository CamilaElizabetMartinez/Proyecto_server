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

        //Se normaliza cada entidad de noticias y se guarda en un array vacio
        foreach ($newsEntities as $theNewsEntity) {
            $newsNormalized[] = $newsNormalize->newsNormalize($theNewsEntity);
        }

        //Retorno en formato JSON las categorias normalizadas
        return $this->json(
            $newsNormalized,
            Response::HTTP_OK
        );
    }
    }

}
