<?php

namespace App\Controller\admin;

use App\Repository\CategoryRepository;
use App\Service\CategoryNormalize;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/api/admin/category", name="api_category_admin")
*/
class ApiCategoryController extends AbstractController
{
    /**
    * @Route(
    *      "",
    *      name="cget",
    *      methods={"GET"}
    * )
    */
    public function index(
        CategoryRepository $categoryRepository,
        CategoryNormalize $categoryNormalize
    ): Response {

        //Recupero todas las categorias
        $categoryEntities = $categoryRepository->findAll();
        
        //Declaro un array vacio para guardar los datos normalizados
        $categoriesNormalized = [];

        //Se normaliza cada entidad de categoria y se guarda en un array vacio
        foreach ($categoryEntities as $theCategoryEntity) {
            $categoriesNormalized[] = $categoryNormalize->CategoryNormalize($theCategoryEntity);
        }

        //retorno en formato JSON las categorias normalizadas
        return $this->json(
            $categoriesNormalized,
            Response::HTTP_OK
        );
    }
}
