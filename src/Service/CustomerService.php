<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\Product;
use App\Repository\AddressRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;

final readonly class CustomerService
{
    public function __construct(private CustomerRepository $repository) {}

    public function getCustomer(string $customer): Customer
    {
        return $this->repository->find($customer);
    }
}