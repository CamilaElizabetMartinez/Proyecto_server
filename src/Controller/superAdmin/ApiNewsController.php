<?php

namespace App\Controller\superAdmin;

use App\Repository\NewsRepository;
use App\Service\NewsNormalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route(
     *      "",
     *      name="post",
     *      methods={"POST"}
     * )
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function Add(
        Request $request,
        SluggerInterface $slug,
        EntityManagerInterface $entityManager
    ): Response {

        //Guardo los datos que llegan de la solicitud
        $data = $request->request;

        //Recupero el slug
        $slugNews = $slug->slug($data->get('title'));

        //Creo una nueva entidad de noticia
        $newsEntity = new News();
        //Seteo el titulo
        $newsEntity->setTitle($data->get('title'));
        //Seteo el slug
        $newsEntity->setSlug($slugNews);
        //Seteo el subtitulo
        $newsEntity->setSubtitle($data->get('subtitle'));
        //seteo la descripción
        $newsEntity->setDescription($data->get('description'));

        //Se prepara y ejecuta la sentencia
        $entityManager->persist($newsEntity);
        $entityManager->flush();

        //Retorno la nueva entidad de noticia normalizada
        return $this->json($newsEntity, Response::HTTP_CREATED);
    }

    /**
    * @Route(
    *      "/{id}",
    *      name="put",
    *      methods={"PUT"},
    *      requirements={
    *          "id": "\d+"
    *      }
    * )
    * @IsGranted("ROLE_SUPER_ADMIN")
    */
    public function Update(
        int $id,
        SluggerInterface $slug,
        EntityManagerInterface $entityManager,
        NewsRepository $newsRepository,
        NewsNormalize $newsNormalize,
        Request $request
    ): Response {

        //Recupero la noticia por ID
        $newsEntity = $newsRepository->find($id);

        //Si el ID es distinto a la noticia retorna un mensaje
        if (!$newsEntity) {
            return $this->json([
                'message' => sprintf('No he encontrado la noticia con id.: %s', $id)
            ], Response::HTTP_NOT_FOUND);
        }
    }
    }
    }

}
