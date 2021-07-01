<?php

namespace App\Repository;

use App\Entity\ImageNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageNews[]    findAll()
 * @method ImageNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageNews::class);
    }

    // /**
    //  * @return ImageNews[] Returns an array of ImageNews objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageNews
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
