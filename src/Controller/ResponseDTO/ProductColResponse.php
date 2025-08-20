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
final class ProductColResponse extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Продукты для пересмотра админом',
            tags: ['Admin'],

            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Продукты для пересмотра админом',
                    content: new OA\JsonContent(

                        type: 'array',
                        items: new OA\Items(ref: new Model(type: Product::class, groups: ['product:detail']))
                    )
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}