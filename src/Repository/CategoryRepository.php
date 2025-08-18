<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Вернёт массив:
     * [
     *   'category' => Category,
     *   'products' => list<Product>  // c JOIN изображениями и категорией
     * ]
     *
     * Замечание: products тянем через DQL из этого же репозитория.
     */
    public function fetchCategoryWithProductsAndImages(UuidInterface $id): ?array
    {
        /** @var Category|null $category */
        $category = $this->find($id);
        if (!$category || $category->isActive() === false) {
            return null;
        }

        // Выбираем продукты по категории + подтягиваем изображения и саму категорию (чтоб не было N+1)
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('p', 'i', 'c')
            ->from(Product::class, 'p')
            ->leftJoin('p.images', 'i')
            ->leftJoin('p.category', 'c')
            ->andWhere('c.id = :cid')
            ->setParameter('cid', $id)
            ->orderBy('p.titleRu', 'ASC');

        /** @var list<Product> $products */
        $products = $qb->getQuery()->getResult();

        return [
            'category' => $category,
            'products' => $products,
        ];
    }
}
