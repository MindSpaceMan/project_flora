<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\ResponseDTO\CategoriesResponse;
use App\Controller\ResponseDTO\ContactMailCreateResponse;
use App\Controller\ResponseDTO\ContactMailsResponse;
use App\Controller\ResponseDTO\ProductsResponse;
use App\DTO\MailDto;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Service\ContactMailService;
use App\Service\ProductService;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact', name: 'contact_owner')]
final class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactMailService $mailService,
    )
    {
    }


    #[Route('', name: '', methods: ['POST'])]
    #[ContactMailCreateResponse]
    public function index(#[MapRequestPayload] MailDto $mail): JsonResponse
    {

        return $this->json(
            $this->mailService->contact($mail),
            200,
            [],
            [
                'json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ]
        );
    }

    #[Route('', name: 'api_contact_mail', methods: ['GET'])]
    #[ContactMailsResponse]
    public function get(): JsonResponse
    {
        return $this->json(
            $this->mailService->get(),
            200,
            [],
            [
                'groups' => ['contact:read'],
                'json_encode_options' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ]
        );
    }
}