<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class CategoriesResponse extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Purchasing product with payment process',
            tags: ['Категория'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Категории цветочков',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: Category::class, groups: ['category:list']))
                    )
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}