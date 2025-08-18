<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use App\Entity\Order;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class GetCartResponse extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Корзина товаров',
            tags: ['Cart'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Текущая корзина (заказ) c позициями',
                    content: new OA\JsonContent(
                        ref: new Model(type: Order::class, groups: ['cart:read'])
                    )
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}