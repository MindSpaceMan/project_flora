<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use App\Entity\Product;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class ProductsResponse extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Список продуктов по категории',
            tags: ['Продукт'],
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    description: 'UUID категории',
                    in: 'path',
                    required: true,
                    schema: new OA\Schema(type: 'string', format: 'uuid', example: '15e7d25b-87db-4dad-b3ba-fc71f7d4effa')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Продукты цветочков',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:list']))
                    )
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}