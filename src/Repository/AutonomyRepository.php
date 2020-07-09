<?php

namespace App\Repository;

use App\Entity\Autonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Autonomy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Autonomy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Autonomy[]    findAll()
 * @method Autonomy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutonomyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autonomy::class);
    }

    // /**
    //  * @return Autonomy[] Returns an array of Autonomy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Autonomy
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
