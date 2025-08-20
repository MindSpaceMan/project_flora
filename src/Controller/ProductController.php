<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\OrderColResponse;
use App\Controller\ResponseDTO\ProductColResponse;
use App\Controller\ResponseDTO\ProductResponse;
use App\Controller\ResponseDTO\ProductsResponse;
use App\Service\ProductService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
//    #[IsGranted('ROLE_ADMIN')]
    #[Route('', name: 'get_all', methods: ['GET'])]
    #[ProductColResponse]
    public function getAll(): JsonResponse
    {

        $products = $this->productService->getAll();

        return $this->json(
            $products,
            200,
            [],
            ['groups' => ['product:detail']]
        );
    }
}