<?php

namespace App\Repository;

use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }

    //    /**
    //     * @return Rental[] Returns an array of Rental objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rental
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function calculateTotalRevenue(): string
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.totalAmount) as totalRevenue')
            ->getQuery();

        $result = $qb->getSingleScalarResult();

        // Formater le résultat en Ariary
        $formattedRevenue = number_format((float)$result, 0, '.', ' ') . ' Ariary';

        return $formattedRevenue;
    }
    public function findRecentRentals(int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC') // Trier par date de création décroissante
            ->setMaxResults($limit)          // Limiter le nombre de résultats
            ->getQuery()
            ->getResult();
    }
}
