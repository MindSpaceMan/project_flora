<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\AddressRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;

final readonly class OrderItemService
{
    public function __construct(private OrderItemRepository $repository) {}

    public function getOrderItem(string $orderItem): OrderItem
    {
        return $this->repository->find($orderItem);
    }
}