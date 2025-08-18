<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\ProductResponse;
use App\Controller\ResponseDTO\ProductsResponse;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product', name: 'product_map')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    #[Route('/{id}', name: 'api_product_by_id', methods: ['GET'])]
    #[ProductResponse]
    public function byId(string $id): JsonResponse
    {
        Uuid::fromString($id);

        $product = $this->productService->getById($id);

        if (!$product) {
            throw new NotFoundHttpException('Продукт не найден');
        }

        return $this->json(
            $product,
            200,
            [],
            [
                'groups' => ['product:detail'],
                'json_encode_options' => \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
            ]
        );
    }
}