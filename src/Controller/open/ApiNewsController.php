<?php

namespace App\Controller\open;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\NewsNormalize;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserNormalize;
use App\Controller\ValidatorInterface;
use App\Controller\UserPasswordHasherInterface;
use App\Entity\ImageNews;
use App\Repository\ImageNewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/news", name="api_news")
 */
class ApiNewsController extends AbstractController
{
    /**
     * @Route(
     *      "",
     *      name="get",
     *      methods={"GET"}
     * )
     */
    public function index(
        Request $request,
        NewsRepository $newsRepository,
        NewsNormalize $newsNormalize
    ): Response {
        //Guardo el numero de la pagina
        $pageNumber = $request->query->get('pageNumber');

        //Declaro el numero de noticias por pagina
        $quantityNewsForPage = 8;
        
        //Consulto cuántas filas hay en la tabla News
        $quantityTheNews = $newsRepository->createQueryBuilder('tableNews')->select('count(tableNews.id)')
        ->getQuery()->getSingleScalarResult();

        //Si pageNumber es verdadero devuelve el numero de la primer posición
        if ($pageNumber == true){
            $fromPosition = ($pageNumber -1) * $quantityNewsForPage;
        }else{
            $fromPosition = 0;
        }

        //Recupero las noticias con sus respectivas imagenes segun el intervalo 
        $newsEntities = $newsRepository->findBy(array(),array(),$quantityNewsForPage,$fromPosition);

    }
};
