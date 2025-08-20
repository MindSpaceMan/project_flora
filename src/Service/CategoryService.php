<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\AddressRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $repository,
        private readonly NormalizerInterface $normalizer,
    ) {}

    /**
     * @return list<Category>
     */
    public function getActiveCategories(): array
    {
        return $this->repository->createQueryBuilder('c')
            ->select('c.id, c.name, c.slug, c.imagePath') // только нужные поля
            ->andWhere('c.isActive = :active')
            ->setParameter('active', true)
            ->addOrderBy('c.sortOrder', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Возвращает пэйлоад категории + продукты (нормализованные по группам).
     */
    public function getCategoryWithProducts(UuidInterface $id): array
    {
        $data = $this->repository->fetchCategoryWithProductsAndImages($id);

        if ($data === null) {
            throw new NotFoundHttpException('Категория не найдена или неактивна.');
        }

        return [
            'id'       => (string) $data['category']->getId(),
            'name'     => $data['category']->getName(),
            'slug'     => $data['category']->getSlug(),
            'products' => $this->normalizer->normalize(
                $data['products'],
                context: ['groups' => ['product:list']]
            ),
        ];
    }
}