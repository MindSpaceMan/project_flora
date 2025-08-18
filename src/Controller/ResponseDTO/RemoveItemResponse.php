<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class RemoveItemResponse extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Категории цветочков',
            tags: ['Cart'],
            parameters: [
                new OA\Parameter(
                    name: 'itemId',
                    in: 'path', required: true,
                    schema: new OA\Schema(type: 'string', format: 'uuid')
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Корзина после удаления позиции',
                    content: new OA\JsonContent(ref: new Model(type: \App\Entity\Order::class, groups: ['cart:read']))
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}