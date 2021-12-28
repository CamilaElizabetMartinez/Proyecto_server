<?php

namespace App\Controller\superAdmin;

use App\Entity\ImageNews;
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\NewsNormalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        
        //Recupero el slug y se le concatena un unico ID
        $slugNews = $slug->slug($data->get('title')).'-'.uniqid();

        //Creo una nueva entidad de noticia
        $newsEntity = new News();
        //Seteo el titulo
        $newsEntity->setTitle($data->get('title'));
        //Seteo la fecha
        $newsEntity->setCreationTimestamp(new \DateTime());
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
        
        //Guardo los datos que llegan de la solicitud
        $data = $request->request;
        
        //Obtengo el slug
        $slugNews = $slug->slug($data->get('title'));
        
        //Seteo titulo
        $newsEntity->setTitle($data->get('title'));

        //Seteo subtitulo
        $newsEntity->setSubtitle($data->get('subtitle'));

        //Seteo la descripción
        $newsEntity->setDescription($data->get('description'));

        //Seteo el slug
        $newsEntity->setSlug($slugNews);

        //Se ejecuta la sentencia
        $entityManager->flush();

        //Se normaliza la entidad noticia y se guarda en una variable
        $newsNormalized= $newsNormalize->NewsNormalize($newsEntity);
        
        //Retorno en formato JSON la noticia normalizada
        return $this->json(
            $newsNormalized,
            Response::HTTP_OK
        );
    }

    /**
    * @Route(
    *      "/{id}",
    *      name="delete",
    *      methods={"DELETE"},
    *      requirements={
    *          "id": "\d+"
    *      }
    * )
    * @IsGranted("ROLE_SUPER_ADMIN")
    */
    public function remove(
        int $id,
        EntityManagerInterface $entityManager,
        NewsRepository $newsRepository
    ): Response {
        //Recupero la noticia por ID
        $newsEntity = $newsRepository->find($id);

        //Se prepara y ejecuta la sentencia
        $entityManager->remove($newsEntity);
        $entityManager->flush();

        return $this->json(
            Response::HTTP_NO_CONTENT
        );
    }

    /**
    * @Route(
    *      "/uploadNewsImage/{id}",
    *      name="uploadNewsImage",
    *      methods={"POST"},
    *      requirements={
    *          "id": "\d+"
    *      }
    * )
    * @IsGranted("ROLE_SUPER_ADMIN")
    */
    public function uploadNewsImage(
        News $newsEntity,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slug
    ): Response {
        //Si el archivo que llega de la solicitud posee img_principal
        //Lo obtiene y guarda en una variable
        if ($request->files->has('img_principal')) {
            $imageFile = $request->files->get('img_principal');

            //Recupera el nombre original de la imagen
            $imgOriginalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            
            //Se genera un unico ID,recupera la extencion,la concatena
            //Y guarda en una variable
            $newFilename =  $imgOriginalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            //Intenta mover el archivo y necesita 2 parametros
            try {
                $imageFile->move(
                    //1º La ruta donde se va a mover
                    $request->server->get('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'media/img/news',
                    //2º El nuevo nombre del archivo
                    $newFilename
                );
                // Si sucede algo inesperado captura el error
            } catch (FileException $e) {
                // Y lanza la excepción
                throw new \Exception($e->getMessage());
            }
            
            //Setea la imagen principal por el nuevo nombre del archivo
            $newsEntity->setImgPrincipal($newFilename);
            
            //Prepara y ejecuta la sentencia
            $entityManager->persist($newsEntity);
            $entityManager->flush();
        }

        //Si el archivo que llega de la solicitud posee img_file
        //Lo obtiene y guarda en una variable
        if ($request->files->has('img_file')) {
            $imageNewsArray = $request->files->get('img_file');

            //Recorre el array de imagen de noticias
            foreach ($imageNewsArray as $theImageNews) {
                //Recupera el nombre original de la imagen
                $imgOriginalFilename = pathinfo($theImageNews->getClientOriginalName(), PATHINFO_FILENAME);

                //Genera un Slug
                $slugImagePrincipal = $slug->slug($imgOriginalFilename);

                //Al slug se concatena el unido ID generado + la extension
                $imgNewFilename = $slugImagePrincipal.'-'.uniqid().'.'. $theImageNews->guessExtension();

                //Intenta mover el archivo y necesita 2 parametros
                try {
                    $theImageNews->move(
                        //1º La ruta donde se va a mover
                        $request->server->get('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'media/img/product',
                        //2º El nuevo nombre del archivo
                        $imgNewFilename
                    );
                    // Si sucede algo inesperado captura el error
                } catch (FileException $e) {
                    // Y lanza la excepción
                    throw new \Exception($e->getMessage());
                }

                //Se crea una instancia
                $theImageNewsEntity = new ImageNews();

                //Setea la imagen de la noticia por el nuevo nombre del archivo
                $theImageNewsEntity->setImgFile($imgNewFilename);

                //Setea la noticia
                $theImageNewsEntity->setNews($newsEntity);
            
                //Se envia a la BBDD
                $entityManager->flush();
            }//End foreach

            //Retorna en formato JSON
            return $this->json(
                Response::HTTP_CREATED
            );
        }
    }
}
