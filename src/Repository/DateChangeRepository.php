<?php

namespace App\Repository;

use App\Entity\DateChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DateChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateChange[]    findAll()
 * @method DateChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateChangeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DateChange::class);
    }

    // /**
    //  * @return DateChange[] Returns an array of DateChange objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DateChange
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
