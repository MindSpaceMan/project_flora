<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

final readonly class CategoryService
{
    public function __construct(private CategoryRepository $repository) {}

    /**
     * @return list<Category>
     */
    public function getActiveCategories(): array
    {
        return $this->repository->createQueryBuilder('c')
            ->select('c.id, c.name, c.slug') // только нужные поля
            ->andWhere('c.isActive = :active')
            ->setParameter('active', true)
            ->addOrderBy('c.sortOrder', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}