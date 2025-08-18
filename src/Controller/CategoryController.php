<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\CategoriesResponse;
use App\Controller\ResponseDTO\ProductsResponse;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Service\ProductService;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'category_map')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly ProductService  $productService,
    )
    {
    }


    #[Route('', name: 'api_categories_col', methods: ['GET'])]
    #[CategoriesResponse]
    public function index(): JsonResponse
    {
        /** @var iterable<Category> $categories */
        $categories = $this->categoryService->getActiveCategories();

        return $this->json(
            $categories,
            200,
            [],
            [
                'groups' => ['category:list'],
                'json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            ]
        );
    }

    #[Route('/{id}/product', name: 'api_products_by_category_col', methods: ['GET'])]
    #[ProductsResponse]
    public function show(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            return $this->json(['message' => 'Некорректный UUID'], 400);
        }
        $uuid = Uuid::fromString($id);

        $payload = $this->categoryService->getCategoryWithProducts($uuid);

        return $this->json(
            $payload,
            200,
            [],
            ['json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES]
        );
    }
}