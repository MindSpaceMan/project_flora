<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Address;
use App\Entity\Product;
use App\Entity\Review;
use App\Repository\AddressRepository;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;

final readonly class ReviewService
{
    public function __construct(private ReviewRepository $repository) {}

    public function getReview(string $review): Review
    {
        return $this->repository->find($review);
    }
}