<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
final class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findNewByCartToken(string $cartToken): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.cartToken = :t')
            ->andWhere('o.status = :st')
            ->setParameter('t', $cartToken)
            ->setParameter('st', 'new')
            ->leftJoin('o.items', 'oi')->addSelect('oi')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
