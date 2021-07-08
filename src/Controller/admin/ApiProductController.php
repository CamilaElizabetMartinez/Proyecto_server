<?php

namespace App\Controller\admin;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Service\ProductNormalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/admin/product", name="api_product_admin")
 */
class ApiProductController extends AbstractController
{
    /**
     * @Route(
     *      "",
     *      name="cget",
     *      methods={"GET"}
     * )
     */
    public function index(
        Request $request,
        ProductRepository $productRepository,
        ProductNormalize $productNormalize
    ): Response {
        /* USAR PARA EL CONTROLADOR PRODUCT DE ADMIN*/
        $user = $this->getUser();

        if ($user->$this->isGranted('ROLE_ADMIN')) {
            return $productRepository->findBy(['user' => $user]);
        } else {
            return $productRepository->findAll();
        }

        $productEntities = $productRepository->findAll();

        $data = [];

        foreach ($productEntities as $theProductEntity) {
            $data[] = $productNormalize->productNormalize($theProductEntity);
        }

        return $this->json($data);
    }

    /**
     * @Route(
     *      "",
     *      name="post",
     *      methods={"POST"}
     * )
     */
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        CategoryRepository $categoryRepository,
        ProductNormalize $productNormalize,
        UserRepository $userRepository,
    
        SluggerInterface $slug
    ): Response {

        /* falta subida de imagenes de producto */
        /* falta recuperar entidad de usuario segun quien este logueado */
        $data = $request->request;

        dump($data);
        dump($request->files);

        /* $theUserEntity = $this->getUser(); */
        $theCategoryEntity = $categoryRepository->find($data->get('category_id'));
        $theUserEntity = $userRepository->find(4);
        $slugProduct = $slug->slug($data->get('name'));

        $theProductEntity = new Product();

        $theProductEntity->setName($data->get('name'));
        $theProductEntity->setCategory($theCategoryEntity);
        $theProductEntity->setSlug($slugProduct);
       /*  $theProductEntity->setImgPrincipal($data->get('img_principal')); */
        $theProductEntity->setWeight($data->get('weight'));
        $theProductEntity->setPrice($data->get('price'));
        /* $theProductEntity->setImageProduct($data->get('image_product')); */
        /* $theProductEntity->setUser($theUserEntity); */
        $theProductEntity->setUser($theUserEntity);

        if ($request->files->has('img_principal')) {
            $imgPrinciplaFile= $request->files->get('img_principal');
            
           $imgOriginalFilename = pathinfo($imgPrinciplaFile->getClientOriginalName(), PATHINFO_FILENAME);
            dump($imgOriginalFilename);

           
            $imgNewFilename = $slugProduct.'-'.uniqid().'.'.$imgPrinciplaFile->guessExtension();
            dump($imgNewFilename);

            try {
                $imgPrinciplaFile->move(
                    $request->server->get('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'media/img/product',
                    $imgNewFilename
                );
            } catch (FileException $e) {
                throw new \Exception($e->getMessage());
            }

            $theProductEntity->setImgPrincipal($imgNewFilename);
        }

        $errors = $validator->validate($theProductEntity);
        
        if (count($errors) > 0) {
            $dataErrors = [];

            /** @var \Symfony\Component\Validator\ConstraintViolation $error */
            foreach ($errors as $error) {
                $dataErrors[] = $error->getMessage();
            }

            return $this->json([
                'status' => 'error',
                'data' => [
                    'errors' => $dataErrors
                    ]
                ],
                Response::HTTP_BAD_REQUEST);
        } 

        $entityManager->persist($theProductEntity);

        // $theProductentity no tiene id.

        $entityManager->flush();

        return $this->json(
            $productNormalize->productNormalize($theProductEntity),
            Response::HTTP_CREATED
        );
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
     */
    public function update(
        int $id,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        Request $request
    ): Response {
        $theProductEntity = $productRepository->find($id);

        if (!$theProductEntity) {
            return $this->json([
                'message' => sprintf('No he encontrado el producto con id.: %s', $id)
            ], Response::HTTP_NOT_FOUND);
        }
        $data = $request->request;

        $theProductEntity->setName($data->get('name'));
        $theProductEntity->setCategory($data->get('category'));
        $theProductEntity->setImgPrincipal($data->get('img_principal'));
        $theProductEntity->setWeight($data->get('weight'));
        $theProductEntity->setPrice($data->get('price'));
        $theProductEntity->setSlug($data->get('slug'));

        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}

