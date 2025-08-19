<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends ServiceEntityRepository<Order>
 */
final class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findCheckoutCartByToken(string $cartToken): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.cartTokenHash = :t')
            ->andWhere('o.status = :st')
            ->setParameter('t', $cartToken)
            ->setParameter('st', 'cart')
            ->leftJoin('o.items', 'oi')->addSelect('oi')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCartBeforeCheckoutByToken(string $hash): ?Order
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.items', 'oi')->addSelect('oi')
            ->leftJoin('oi.product', 'p')->addSelect('p')
            ->andWhere('o.cartTokenHash = :h')->setParameter('h', $hash)
            ->andWhere('o.status = :status')->setParameter('status', 'cart')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function findCartByUuid(UuidInterface $uuid)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.items', 'i')->addSelect('i')
            ->leftJoin('i.product', 'p')->addSelect('p')
            ->andWhere('o.id = :id')->setParameter('id', $uuid)
            ->getQuery()->getOneOrNullResult();
    }

}
