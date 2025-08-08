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

    public function getCategory(string $category): Category
    {
        return $this->repository->find($category);
    }
}