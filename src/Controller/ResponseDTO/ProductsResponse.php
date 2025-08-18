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
            summary: 'Категория со списком продуктов',
            tags: ['Category'],
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
                    description: 'Категория со списком продуктов',
                    content: new OA\JsonContent(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '740d9ffe-3983-422e-bbea-aad96a97ae1c'),
                            new OA\Property(property: 'name', type: 'string', example: 'Тюльпаны', nullable: true),
                            new OA\Property(property: 'slug', type: 'string', example: 'tulipany', nullable: true),
                            new OA\Property(
                                property: 'products',
                                type: 'array',
                                items: new OA\Items(ref: new Model(type: \App\Entity\Product::class, groups: ['product:list']))
                            ),
                        ],
                        type: 'object'
                    )
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
                new OA\Response(response: 404, description: 'Категория не найдена'),
            ],
        );
    }
}