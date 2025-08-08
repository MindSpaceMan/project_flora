<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

final readonly class OrderService
{
    public function __construct(private OrderRepository $repository) {}

    public function getOrder(string $order): Order
    {
        return $this->repository->find($order);
    }
}