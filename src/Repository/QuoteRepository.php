<?php

namespace App\Repository;

use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quote>
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

   
   public function findLatestByRepairOrderId(int $repairOrderId): Quote
   {
       return $this->createQueryBuilder('q')
           ->andWhere('q.repairOrder.id = :repairOrderId')
           ->setParameter('repairOrderId', $repairOrderId)
           ->orderBy('q.createdAt', 'DESC')
           ->setMaxResults(1)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
}
