<?php

namespace App\Controller\open;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductNormalize;
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
}
