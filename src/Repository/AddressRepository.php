<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Address>
 */
final class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    public function getAddressByLine1(string $line1): ? Address
    {
        return $this->createQueryBuilder('ad')
            ->andWhere('ad.line1 = :line1')
            ->setParameter('line1', $line1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
