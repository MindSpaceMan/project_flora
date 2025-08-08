<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Repository\AddressRepository;
use App\Repository\ProductImageRepository;
use App\Repository\ProductRepository;

final readonly class ProductImageService
{
    public function __construct(private ProductImageRepository $repository) {}

    public function getProductImage(string $productImage): ProductImage
    {
        return $this->repository->find($productImage);
    }
}