<?php

namespace App\Controller\open;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductNormalize;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserNormalize;
use App\Controller\ValidatorInterface;
use App\Controller\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/product", name="api_product_")
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
        if ($request->query->has('termino')) {
            $productEntities = $productRepository->findByTerm($request->query->get('termino'));

            $data = [];

            foreach ($productEntities as $theProductEntity) {
                $data[] = $productNormalize->productNormalize($theProductEntity);
            }
    
            return $this->json($data);
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
     *      "/{slug}",
     *      name="get",
     *      methods={"GET"}
     * )
     */
    public function details(
        string $slug,
        productRepository $productRepository,
        ProductNormalize $productNormalize
    ): Response
    {
        $theProductEntity = $productRepository->findOneBy(['slug' => $slug]);
        
        $theProductEntityNormalize = $productNormalize->ProductNormalize($theProductEntity);

        return $this->json($theProductEntityNormalize);
    }

   ## /**
   ##  * @Route(
   ##  *      "/register",
    ## *      name="post",
    ## *      methods={"POST"}
    ## * )
    ## */
    ##public function add(
    ##   UserNormalize $userNormalize,
    ##    Request $request,
    ##    EntityManagerInterface $entityManager,
    ##    ValidatorInterface $validator,     
    ##    UserPasswordHasherInterface $hasher
    ##    ): Response {
    ##    $data = json_decode($request->getContent());
    ##             
    ##    $user = new User();

    ##    $user->setName($data->name);
    ##    $user->setSurname1($data->surname1);
    ##    $user->setCity($data->city);
    ##    $user->setAddress($data->address);
    ##    $user->setPhoneNumber($data->phoneNumber);
    ##    $user->setEmail($data->email);

    ##    $hash = $hasher->hashPassword($user, $data->password);
    ##    $user->setPassword($hash);                        

    ##    $errors = $validator->validate($user);

    ##    if(count($errors) > 0) {
    ##        $dataErrors = [];
    ##        /** @var \Symfony\Component\Validator\ConstraintViolation $error */
    ##        foreach($errors as $error) {
    ##            $dataErrors[] = $error->getMessage();
    ##        }
    ##        
    ##        return $this->json([
    ##            'status' => 'error',
    ##            'data' => [
    ##                'errors' => $dataErrors
    ##            ],
    ##        ],
    ##        Response::HTTP_BAD_REQUEST);
    ##    }

    ##    $entityManager->persist($user);
    ##    
    ##    $entityManager->flush();

    ##   
    ##    return $this->json(
    ##        $userNormalize->UserNormalize($user),
    ##        Response::HTTP_CREATED,        

    ##    );
    ##
    ##}
}
