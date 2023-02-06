<?php

namespace App\Controller\open;

use App\Repository\ProductRepository;
use App\Service\ProductNormalize;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/product", name="api_product")
 */
class ApiProductController extends AbstractController
{
    /**
     * @Route(
     *      name="cget",
     *      methods={"GET"}
     * )
     */
    public function index(
        Request $request,
        ProductRepository $productRepository,
        ProductNormalize $productNormalize
    ): Response {
        //Guardo los datos que llegan de la solicitud
        $data = $request->request;

        //Recupero los valores del parametro por GET
        $filterName =$request->query->get('filterName');
        $filterCategory =$request->query->get('filterCategory');
        
        //Guardo el numero de la pagina
        $pageNumber = $request->query->get('pageNumber');
        
        //Declaro el numero de producto por pagina
        $quantityProductForPage = 8;

        //Consulto cuántas filas hay en la tabla Producto por filtrado
        $qb = $productRepository
            ->createQueryBuilder('tableProduct')
            ->select('count(tableProduct.id)')
        ;
        $quantityTheProduct = [];

        if ($filterName) {
            $quantityTheProduct= $qb->where('tableProduct.name like :filterName')
            ->setParameter('filterName', "%$filterName%");
        }
        if ($filterCategory) {
            $quantityTheProduct= $qb->andWhere('tableProduct.category = :filterCategory')
            ->setParameter('filterCategory', $filterCategory);
        }

        $quantityTheProduct = $qb->getQuery()->getSingleScalarResult();

        //Si pageNumber es verdadero devuelve el numero de la primer posición
        if ($pageNumber == true) {
            $fromPosition = ($pageNumber -1) * $quantityProductForPage;
        } else {
            $fromPosition = 0;
        }
    
        //Recupero los productos por nombre,categoria y posicion segun el intervalo.
        $qb = $productRepository->createQueryBuilder('tableProduct');

        $productEntities = [];

        if ($filterName) {
            $productEntities= $qb->where('tableProduct.name like :filterName')
            ->setParameter('filterName', "%$filterName%");
        }
        if ($filterCategory) {
            $productEntities= $qb->andWhere('tableProduct.category = :filterCategory')
            ->setParameter('filterCategory', $filterCategory);
        }

        $productEntities = $qb->setFirstResult($fromPosition)
            ->setMaxResults($quantityProductForPage)
            ->getQuery()
            ->getResult()
        ;
                
        //Declaro un array vacio para guardar los datos normalizados
        $data = [];

        //Se normaliza cada entidad de producto y se guarda en un array vacio
        foreach ($productEntities as $theProductEntity) {
            $data[] = $productNormalize->ProductNormalize($theProductEntity);
        }

        //Declaro el numero total de paginas
        $totalPages = ceil($quantityTheProduct/$quantityProductForPage);

        //Declaro los valores y lo retorno
        $response = [
            'pageNumber' => $pageNumber,
            'productEntities' => $data,
            'filterName' => $filterName,
            'filterCategory' => $filterCategory,
            'totalPage' => $totalPages
        ];
     
        return $this->json($response, Response::HTTP_OK);
    }

    /**
     * @Route(
     *      "/detail/{slug}",
     *      name="get",
     *      methods={"GET"}
     * )
     */
    public function details(
        string $slug,
        productRepository $productRepository,
        ProductNormalize $productNormalize
    ): Response {
        $theProductEntity = $productRepository->findOneBy(['slug' => $slug]);
        
        $theProductEntityNormalize = $productNormalize->ProductNormalize($theProductEntity);

        return $this->json($theProductEntityNormalize);
    }
}
