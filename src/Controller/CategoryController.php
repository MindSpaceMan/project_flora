<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\CategoriesResponse;
use App\Entity\Category;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
    )
    {
    }


    #[Route('/categories', name: 'api_categories_index', methods: ['GET'])]
    #[CategoriesResponse]
    public function index(): JsonResponse
    {
        /** @var iterable<Category> $categories */
        $categories = $this->categoryService->getActiveCategories();

        return $this->json(
            $categories,
            200,
            [],
            ['json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                'groups' => ['category:list']
            ]
        );
    }
}