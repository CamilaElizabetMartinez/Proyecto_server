<?php

namespace App\Controller\open;

use App\Repository\NewsRepository;
use App\Service\NewsNormalize;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        if ($pageNumber == true) {
            $fromPosition = ($pageNumber -1) * $quantityNewsForPage;
        } else {
            $fromPosition = 0;
        }

        //Recupero las noticias con sus respectivas imagenes segun el intervalo
        $newsEntities = $newsRepository->findBy(array(), array(), $quantityNewsForPage, $fromPosition);

        //Declaro un array vacio para guardar los datos normalizados
        $data = [];

        //Se normaliza cada entidad de noticias y se guarda en un array vacio
        foreach ($newsEntities as $theNewsEntity) {
            $data[] = $newsNormalize->newsNormalize($theNewsEntity);
        }
        //Declaro el numero total de paginas
        $totalPages = ceil($quantityTheNews/$quantityNewsForPage);

        //Declaro los valores y lo retorno en objecto
        $response = [
            'pageNumber' => $pageNumber,
            'newsEntities' => $data,
            'totalPage' => $totalPages
        ];

        return $this->json($response);
    }

    /**
    * @Route(
    *      "/detail/{slug}",
    *      name="get",
    *      methods={"GET"}
    * )
    */
    public function detail(
        NewsRepository $newsRepository,
        NewsNormalize $newsNormalize,
        string $slug
    ): Response {

        //Se recupera una noticia por Slug
        $theNewsEntity = $newsRepository->findOneBy(['slug' => $slug]);

        //Se normaliza la noticia recuperada
        $theNewstEntityNormalize = $newsNormalize->NewsNormalize($theNewsEntity);

        //Se retorna en formato JSON
        return $this->json($theNewstEntityNormalize,Response::HTTP_OK);
    }
};
