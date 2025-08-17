<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\ProductsResponse;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    #[Route('/categories/{id}/products', name: 'api_products_by_category', methods: ['GET'])]
    #[ProductsResponse]
    public function byCategory(string $id): JsonResponse
    {
        Uuid::fromString($id);

        $products = $this->productService->getByCategory($id);

        return $this->json(
            $products,
            200,
            [],
            [
                'groups' => ['product:list'],
                'json_encode_options' => \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ]
        );
    }
}