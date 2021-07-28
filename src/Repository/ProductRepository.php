<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByTermStrict(string $termino)
    {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder->where('e.name = :termino');
        
        $queryBuilder->orWhere('e.category = :termino');

        $queryBuilder->orwhere('e.slug = :termino');

        $queryBuilder->orWhere('e.img_principal = :termino');

        $queryBuilder->orWhere('e.weight = :termino');

        $queryBuilder->orWhere('e.price = :termino');

        $queryBuilder->orWhere('e.user = :termino');


        $queryBuilder->setParameter('term', $termino);
        $queryBuilder->orderBy('e.id', 'ASC');

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    
    /**
     * Buscador de productos por nombre y categoria
     */
    public function findByTerm(string $termino)
    {
        $queryBuilder = $this->createQueryBuilder('e');

        $queryBuilder->where(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('e.name' , ':termino'),
                $queryBuilder->expr()->like('e.category' , ':termino'),
            )
        )
        ->setParameter('termino', '%'.$termino.'%')
        ->orderBy('e.id', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
