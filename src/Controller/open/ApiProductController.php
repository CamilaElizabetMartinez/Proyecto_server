<?php

namespace App\Controller\open;

use App\Entity\Product;
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
    public function index(Request $request, ProductRepository $productRepository, ProductNormalize $productNormalize): Response
    {
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

    
}

