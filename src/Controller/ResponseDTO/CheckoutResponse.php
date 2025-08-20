<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class CheckoutResponse extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path: '/api/order/checkout',
            summary: 'Оформление заказа (анонимно) по X-Cart-Token',
            tags: ['Cart'],
            parameters: [
                new OA\Parameter(
                    name: 'X-Cart-Token',
                    description: 'Токен корзины, выданный на этапе добавления в корзину',
                    in: 'header',
                    required: true,
                    schema: new OA\Schema(type: 'string', example: 'c9d8f0f0-bf0a-4a09-9a52-4d52a6f1b3a1')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Оформлено',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '15e7d25b-87db-4dad-b3ba-fc71f7d4effa'),
                            new OA\Property(property: 'status', type: 'string', example: 'sent'),
                            new OA\Property(property: 'customerEmail', type: 'string', example: 'ivan@example.com'),
                        ],
                        type: 'object'
                    )
                ),
                new OA\Response(response: 400, description: 'Ошибка валидации'),
                new OA\Response(response: 404, description: 'Корзина не найдена'),
            ]
        );
    }
}