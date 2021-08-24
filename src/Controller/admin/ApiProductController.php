<?php

namespace App\Controller\admin;

use App\Entity\ImageProduct;
use App\Entity\Product;
use App\Repository\CategoryRepository;
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
        ProductRepository $productRepository,
        ProductNormalize $productNormalize
    ): Response {
        
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $productEntities = $productRepository->findBy(['user' => $user]);
        } else {
            $productEntities = $productRepository->findAll();
        };

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
        SluggerInterface $slug
    ): Response {
        /* falta recuperar entidad de usuario segun quien este logueado */
        $data = $request->request;
    

        $theUserEntity = $this->getUser();
        $theCategoryEntity = $categoryRepository->find($data->get('category_id'));
        $slugProduct = $slug->slug($data->get('name'));

        $theProductEntity = new Product();

        $theProductEntity->setName($data->get('name'));
        $theProductEntity->setCategory($theCategoryEntity);
        $theProductEntity->setSlug($slugProduct);
        $theProductEntity->setWeight($data->get('weight'));
        $theProductEntity->setPrice($data->get('price'));

        $theProductEntity->setUser($theUserEntity);

        if ($request->files->has('image_product')) {
            $imagesProducts = $request->files->get('image_product');

            foreach ($imagesProducts as $theImageProduct) {
                $imgOriginalFilename = pathinfo($theImageProduct->getClientOriginalName(), PATHINFO_FILENAME);

                $slugImagePrincipal = $slug->slug($imgOriginalFilename);
                $imgNewFilename = $slugImagePrincipal.'-'.uniqid().'.'. $theImageProduct->guessExtension();
                try {
                    $theImageProduct->move(
                        $request->server->get('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'media/img/product',
                        $imgNewFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }

                $theImageProductEntity = new ImageProduct();

                $theImageProductEntity->setImgFile($imgNewFilename);

                $theImageProductEntity->setProduct($theProductEntity);
                $theProductEntity->addImageProduct($theImageProductEntity);

               /*  $entityManager->persist($theImageProductEntity); */
            }
        }
        

        if ($request->files->has('img_principal')) {
            $imgPrincipalFile= $request->files->get('img_principal');

            $imgOriginalFilename = pathinfo($imgPrincipalFile->getClientOriginalName(), PATHINFO_FILENAME);

            $slugImagePrincipal = $slug->slug($imgOriginalFilename);
            $imgNewFilename = $slugImagePrincipal.'-'.uniqid().'.'.$imgPrincipalFile->guessExtension();
            try {
                $imgPrincipalFile->move(
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

            return $this->json(
                [
                'status' => 'error',
                'data' => [
                    'errors' => $dataErrors
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($theProductEntity);

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
        SluggerInterface $slug,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        
        Request $request
    ): Response {
        
        $theProductEntity = $productRepository->find($id);

        if(!$theProductEntity) {
            return $this->json([
                'message' => sprintf('No he encontrado el producto con id.: %s', $id)
            ], Response::HTTP_NOT_FOUND);
        }
        
        $data = $request->request;
        
        if ($request->files->has('img_principal')) {
            $imgPrincipalFile= $request->files->get('img_principal');

            $theProductEntity->setImgPrincipal($imgPrincipalFile);
        }
        
        $theCategoryEntity = $categoryRepository->find($data->get('category_id'));
        $slugProduct = $slug->slug($data->get('name'));
        
        $theProductEntity->setName($data->get('name'));
        $theProductEntity->setCategory($theCategoryEntity);
        $theProductEntity->setWeight($data->get('weight'));
        $theProductEntity->setPrice($data->get('price'));
        $theProductEntity->setSlug($slugProduct);
        
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
        
        /* if ($request->files->has('img_principal')) {
            $imgPrincipalFile= $request->files->get('img_principal');

            $theProductEntity->setImgPrincipal($imgPrincipalFile);

        } */
        /* $data->$request->request->get(); */

        /* $slugProduct = $slug->slug($data->get('name'));

        $theProductEntity->setName($data->get('name'));
        $theProductEntity->setCategory($data->get('category'));
        $theProductEntity->setWeight($data->get('weight'));
        $theProductEntity->setPrice($data->get('price'));
        $theProductEntity->setSlug($slugProduct);

        return $this->json(null, Response::HTTP_NO_CONTENT); */
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
     */
    public function remove(
        int $id,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ): Response {
        $theProductEntity = $productRepository->find($id);

        $entityManager->remove($theProductEntity);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
