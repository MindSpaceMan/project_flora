<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use App\Entity\Order;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class AddItemResponse extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Категории цветочков',
            tags: ['Cart'],
            parameters: [
                new OA\Parameter(
                    name: 'productId',
                    in: 'query',
                    required: true,
                    schema: new OA\Schema(type: 'string', format: 'uuid'),
                    example: '15e7d25b-87db-4dad-b3ba-fc71f7d4effa'
                ),
                new OA\Parameter(
                    name: 'quantity',
                    in: 'query',
                    required: true,
                    schema: new OA\Schema(type: 'integer'),
                    example: 2
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Корзина после добавления позиции',
                    content: new OA\JsonContent(ref: new Model(type: Order::class, groups: ['cart:read']))
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}