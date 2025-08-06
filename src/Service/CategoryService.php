<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Product;
use App\Repository\AddressRepository;
use App\Repository\ProductRepository;

final readonly class CategoryService
{
    public function __construct(private AddressRepository $repository) {}

    /**
     * Get product
     */
    public function getAddress(string $address): Address
    {
        return $this->repository->find($address);
    }
}