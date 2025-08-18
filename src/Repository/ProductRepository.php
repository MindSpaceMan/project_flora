<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return array<Product>
     */
    public function findByCategoryId(UuidInterface $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->andWhere('c.id = :cid')
            ->setParameter('cid', $categoryId)
            // картинки подтягиваем за один запрос
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->orderBy('p.titleRu', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Возвращает продукт по UUID вместе с категорией и картинками.
     */
    public function findOneWithCategoryAndImages(UuidInterface $id): ?Product
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->leftJoin('p.images', 'i')->addSelect('i')
            ->andWhere('p.id = :id')
            // если маппинг поля — Doctrine "uuid", можно явно указать тип:
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
