<?php

namespace App\Controller\superAdmin;

use App\Entity\User;
use App\Service\UserNormalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiUserController extends AbstractController
{
    /**
    * @Route("api/register", name="api_register", methods={"POST"})
    */
    public function index(
        Request $request ,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserNormalize $userNormalize
    ): Response
    {
        $data = json_decode($request->getContent(), true);

        $userNew = new User();

        $plainPassword = $data['newPassword'];
        $hashedPassword = $hasher->hashPassword($userNew,$plainPassword);

        // $userNew->setName($data['name']);
        // $userNew->setSurname1($data['surname1']);
        // $userNew->setSurname2($data['surname2']);
        // $userNew->setPhoneNumber($data['phone_number']);
        // $userNew->setCity($data['city']);
        // $userNew->setAddress($data['address']);
        // $userNew->setEmail($data['email']);
        // $userNew->setPassword($hashedPassword);

        //Hacemos la validación según las constraints
        // $errors = $validator->validate($userNew);

        // if (count($errors) > 0) {
        //     $dataErrors = [];

        //     /** @var \Symfony\Component\Validator\ConstraintViolation $error */
        //     foreach($errors as $error){
        //         $dataErrors[] = $error->getMessage();
        //     }

        //     return $this->json([
        //         'status' => 'error',
        //         'data' => [
        //             'errors' => $dataErrors
        //             ]
        //         ],
        //     Response::HTTP_BAD_REQUEST);
        // }

        $entityManager->persist($userNew);
        $entityManager->flush();

        return $this->json([
            'message' => "Account created."
        ],
        Response::HTTP_CREATED
    );
    }

}