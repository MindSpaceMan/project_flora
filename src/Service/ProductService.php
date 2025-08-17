<?php
declare(strict_types=1);

namespace App\Service;

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
     * @return array<\App\Entity\Product>
     */
    public function getByCategory(string $categoryId): array
    {
        $uuid = Uuid::fromString($categoryId);

        return $this->productRepository->findByCategoryId($uuid);
    }
}