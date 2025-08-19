<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class CreateCartResponse extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Создание корзины',
            tags: ['Cart'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Текущая корзина (заказ) c позициями',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(property: 'token', type: 'string',example: '740d9ffe-3983-422e-bbea-aad96a97ae1c'),
                            new OA\Property(property: 'cart', ref: new Model(type: Order::class, groups: ['cart:read'])),
                        ],
                        type: 'object'
                    )
                ),
            ],
        );
    }
}