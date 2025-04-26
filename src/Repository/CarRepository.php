<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

//    /**
//     * @return Car[] Returns an array of Car objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Car
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function findPopularCars(int $limit = 5): array
{
    return $this->createQueryBuilder('c')
        ->select('c, COUNT(r.id) AS HIDDEN rentalCount') // Compter les locations associées
        ->leftJoin('c.rentals', 'r') // Joindre la table des locations
        ->groupBy('c.id') // Grouper par voiture
        ->orderBy('rentalCount', 'DESC') // Trier par nombre de locations décroissant
        ->setMaxResults($limit) // Limiter le nombre de résultats
        ->getQuery()
        ->getResult();
}
}
