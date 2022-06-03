<?php

namespace App\Repository;

use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transactions[]    findAll()
 * @method Transactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transactions::class);
    }

     /**
      * @return Transactions[] Returns an array of Transactions objects
      */
    public function findTransactionsLastYear()
    {
        $month = date('m');
        $year = date('Y');
        $prevYear = date('Y') - 1;
        $startDate = new \DateTimeImmutable("$prevYear-$month-01T00:00:00");
        $endDate = new \DateTimeImmutable("$year-$month-01T00:00:00");
        return $this->createQueryBuilder('t')
            ->where('t.createdAt BETWEEN :start AND :end')
            ->andWhere('t.status = :paid')
            ->setParameter('paid', 'paid')
            ->setParameter('start', $startDate->format('Y-m-d H:i:s'))
            ->setParameter('end', $endDate->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();
    }

     /**
      * @return Transactions[] Returns an array of Transactions objects
      */
    public function findByCartId()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status', 't.shipped = :shipped', 't.cartId > :cartId')
            ->setParameters(['status' => 'paid', 'shipped' => 0, 'cartId' => 0])
            ->getQuery()
            ->getResult()
        ;
    }
}
