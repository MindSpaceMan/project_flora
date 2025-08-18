<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Возвращает продукты по UUID категории.
     *
     * @return array<Product>
     */
    public function getByCategory(string $categoryId): array
    {
        $uuid = Uuid::fromString($categoryId);

        return $this->productRepository->findByCategoryId($uuid);
    }

    /**
     * Возвращает продукт по UUID.
     *
     * @return array<Product>
     */
    public function getById(string $id): array
    {
        $uuid = Uuid::fromString($id);

        return $this->productRepository->findById($uuid);
    }
}